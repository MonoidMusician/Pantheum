var express        = require('express');
var url            = require('url');
var Promise        = require('bluebird');
var React          = require('react');
var ReactDOMServer = require('react-dom/server');
var h              = require('react-hyperscript');
require('./model/depaths');

var view = require('./react/react.js');
var model = require('./model/model.js');

var router = express.Router();
module.exports = router;

var createClass = function(c) {
    var r = React.createClass(c);
    r.h = h.bind(undefined, r);
    return r;
};

var handle = function(res, err) {
	console.log(err.stack);
	if (err instanceof Error && !Object.keys(err).length)
		err = err.message;
	res.send(err);
};

var handler = function(gen) {
	return Promise.coroutine(function*(req, res, next) {
		try {
			return yield* gen(req, res, next);
		} catch(err) {
			return handle(res, err);
		}
	});
};

var Page = createClass({
    displayName: 'Page',
    render: function() {
        return h('html', [
            h('head', [
                h('title', this.props.title),
                ...(this.props.scripts||[]).map(s=>h('script', s)),
                ...(this.props.links||[]).map(s=>h('link', s)),
            ]),
            h('body', this.props.children)
        ]);
    }
});

router.route('/').get(function(req, res) {
    var root = Page.h({
        title: 'HALLO',
    }, 'hello');
    res.send(ReactDOMServer.renderToString(root));
});

router.route('/dictionary').get(handler(function*(req, res) {
    console.log('before pull');
    model.Word({
        id: 10176,
        entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
        attrs: {
            common:true,
            copulative:true,
            irregular:true,
            transitive:false
        }
    }, true).pullall().then(function(word) {
        console.log('after pull');
        var entry = view.Entry.h({word});
        var root = Page.h({
            title: 'Dictionary',
            links: [
                {rel:'stylesheet', type:'text/css', href:'/CSS/react.css'},
                {rel:'stylesheet', type:'text/css', href:'/Images/open-iconic/font/css/open-iconic.css'},
            ]
        },  {dangerouslySetInnerHTML:{__html:ReactDOMServer.renderToString(entry)}});
        //res.send(ReactDOMServer.renderToString(entry));
        res.send(ReactDOMServer.renderToStaticMarkup(root));
        console.log('after render');
    });
}));
