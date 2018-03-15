/*
function getstatus$(value$) {
  var status$ = value ? PENDING : HIDDEN;
  var status$ = throttle$(value, time);
	var status$ = value$.map(tf(PENDING, HIDDEN)).merge(
		throttle$(value$, time).filter(Boolean)
		.flatMapLatest(maybeOFn(getValue(props.scorer)))
		.map(tf(CORRECT, INCORRECT))
	).publish().refCount().startWith(HIDDEN);
}
function r$(a$,b$,c$) {
  var d$ = (a&b) + c + c;
  var e$ = (d | c) ^ a;
  var as$ = [a, a];
  var f$ = [a, b, 2, c];
  return d+2;
}
*/

export default function (babel) {
  var {types: t, template: T} = babel;
  var opT = T(`LEFT.OP(RIGHT)`);
  var makeop = op => (OP => opt => opT(Object.assign({OP}, opt)))(t.identifier(op));
  var merge = makeop('merge');
  var concat = makeop('concat');
  var combine = makeop('combine');
  var map = makeop('map');
  var Oof = T(`O.of(OPND)`);
  var mapF = T(`
    LEFT$.map(LEFT => EXPR)
  `);
  var combMap = T(`
    LEFT$.combine(RIGHT$).map((LEFT, RIGHT) => EXPR)
  `);
  var spread = a => a.length ? a.length > 1 ? t.spreadElement(t.arrayExpression(a)) : a[0] : null;
  var property = (l,r) => t.memberExpression(l, t.identifier(r));
  var Ocombine = ([LEFT, ...RIGHT]) => t.callExpression(property(LEFT, 'combine'), RIGHT);
  var ofObservables = es => es.map(e => e.observable || Oof({OPND:e}).expression);
  var binOps = {
    '&': merge,
    '|': combine,
    '^': concat,
  };
  var visitReturn = {
    ReturnStatement: {
      exit(path) {
        var {node, parent, scope} = path, {argument} = node;
        if (argument.observable) path.replaceWith(t.returnStatement(node.argument.observable));
      },
    },
  };
  return {
    visitor: {
      Identifier(path) {
        path.node.observable = path.scope.hasOwnBinding(path.node.name+'$') ? t.identifier(path.node.name+'$') : false;
      },
      ArrayExpression: {
        exit(path) {
          var {node, scope} = path, {elements} = node;
          var obs = elements.map(e => e.observable);
          var deps = obs.filter(Boolean);
          if (node.observable || !deps.length) return;
          deps = ofObservables(elements);
          node.observable = Ocombine(deps);
        },
      },
      BinaryExpression: {
        exit(path) {
          var {node, scope} = path, {left, right} = node;
          if (!left.observable && !right.observable)
            return;
          if (!left.observable || !right.observable) {
            var side = left.observable ? left : right;
            var other = left.observable ? right : left;
            var id = scope.generateUidIdentifier(side.name);
            node.observable = mapF({
              LEFT$: side.observable,
              LEFT: id,
              EXPR: t.binaryExpression(node.operator, side===left?id:left, side===right?id:right),
            }).expression;
            return;
          }
          if (node.operator in binOps) {
            node.observable = binOps[node.operator]({LEFT:left.observable,RIGHT:right.observable}).expression;
          } else {
            var l = scope.generateUidIdentifier(left.name);
            var r = scope.generateUidIdentifier(right.name);
            node.observable = combMap({
              LEFT$: left.observable,
              RIGHT$: right.observable,
              LEFT: l,
              RIGHT: r,
              EXPR: t.binaryExpression(node.operator, l, r),
            }).expression;
          }
        },
      },
      ConditionalExpression: {
        exit(path) {
          var {node, scope} = path, {test} = node;
          if (!test.observable) return;
          var id = scope.generateUidIdentifier(test.name);
          node.observable = mapF({
            LEFT$: test.observable,
            LEFT: id,
            EXPR: t.conditionalExpression(id, node.consequent, node.alternate),
          }).expression;
        },
      },
      CallExpression: {
        exit(path) {
          var {node, scope} = path, {arguments: args} = node;
          if (node.observable || node.callee.observable || (node.callee.name && node.callee.name.endsWith('$'))) return;
          if (!args.some(e => e.observable)) return;
          console.log(node);
          node.observable = t.callExpression(t.memberExpression(Ocombine(ofObservables(args)), t.identifier('map')), [node.callee]);
        },
      },
      FunctionDeclaration: {
        exit(path) {
          path.traverse(visitReturn);
        },
      },
      VariableDeclarator: {
        exit(path) {
          var {node, scope} = path;
          if (!node.id.name.endsWith('$') || !node.init.observable) return;
          path.replaceWith(t.variableDeclarator(node.id, node.init.observable));
        },
      },
    }
  };
}
