var h = CycleDOM.h;
var O;
if (typeof xstream !== 'undefined') {
	O = xstream.Stream
} else {
	O = Rx.Observable;
	O.combine = (...streams) => O.combineLatest(...streams, (...args) => args);
}

var combineTemplate = function(template) {
	var keys = _.keys(template);
	return O.combine(..._.values(template)).map(values => _.object(keys, values));
};

var app1 = {
	intent({DOM, components, HTTP}) {
		var events$ = (selector, type) => DOM.select(selector).events(type);
		var input$ = selector => events$(selector, 'input').map(ev => ev.target.value);
		var change$ = selector => events$(selector, 'change').map(ev => ev.target.value);
		return {
			arabic$: input$('#arabic'),
			roman$: input$('#roman'),
			gender$: change$('#gender'),
			number$: change$('#number'),
			case$: change$('#case'),
		};
	},

	model(actions) {
		var state$ = combineTemplate({
			_gender: actions.gender$.startWith('masculine'),
			_number: actions.number$.startWith('singular'),
			_case: actions.case$.startWith('nominative'),
		});

		// These both affect both numeral and number properties
		var arabic$ = actions.arabic$.map(number => ({number, numeral: romanize(number)}));
		var _roman$ = actions.roman$.startWith('').map(numeral => {
			numeral = numeral.toUpperCase();
			var number = deromanize(numeral);
			if (number === false) {
				if (numeral) return null; // reject event
				else number = '';
			}
			return {numeral, number};
		});

		// Hack to force updates when _roman$ emits "null"
		var force = 0;
		var roman$ = O.combine(
			// all events, null or otherwise
			_roman$,
			// events without null; i.e., the last nonnull event
			_roman$.filter(Boolean)
		).map(
			// take a new event, or force the old one to update
			([a,b]) => a || Object.assign({force: force++}, b)
		);

		// Merge to obtain latest {number, numeral} values
		var numbers$ = O.merge(arabic$, roman$);

		// Merge with the rest of our state {_gender, _number, _case}
		return O.combine(state$, numbers$).map(parts => Object.assign({}, ...parts));
	},

	view(state$, components, HTTP) {
		var makeoption = selected => value => h('option', {
			value,
			selected: value == selected
		}, value[0].toUpperCase() + value.substr(1));
		return {
			DOM: state$.map(
				state => {
					console.log(state, state.number);
					var unicode = reromanize(state.numeral||'');
					var {cardinal, ordinal, distributive, adverbial} = verbalize(state);
					return h('div', [
						h('h2', "Roman numerals"),
						h('input#arabic', {
							placeholder: "Arabic Number",
							type: 'number',
							min: 0, max: 499999,
							value: state.number,
						}),
						' = ',
						h('input#roman', {
							placeholder: "Roman Numeral",
							value: state.numeral||'',
						}),
						unicode && ' = ', unicode,
						unicode && ' = ', unicode.toLowerCase(),
						h('br'),
						h('select#gender', ['feminine', 'masculine', 'neuter'].map(makeoption(state._gender))),
						h('select#number', ['singular', 'plural'].map(makeoption(state._number))),
						h('select#case', ['nominative', 'accusative', 'ablative', 'dative', 'genitive', 'vocative'].map(makeoption(state._case))),
						h('br'), 'Cardinal: ', cardinal,
						h('br'), 'Ordinal: ', ordinal,
						h('br'), 'Distributive: ', distributive,
						h('br'), 'Adverbial: ', adverbial,
					])
				}
			),
		};
	}
};
function isolateComponentSinks(components) {
	var result$ = rx.empty();
	for (let C in components) {
		let c = components[C];
		for (let K in c) {
			let k$ = c[K];
			result$ = result$.merge(k$.map(v => ({[C]: {[K]: v}})));
		}
	}
	return result$;
}
function isolateComponentSources(components) {
	return (name, key) => components.filter(v => name in v).map(v => v[name]).filter(v => key in v).map(v => v[key]);
}

main = program => sources => {
	const actions = program.intent(sources);
	const state$ = program.model(actions);
	const sinks = program.view(state$, actions.components, actions.HTTP);
	return sinks;
}
Cycle.run(main(app1), {
	DOM: CycleDOM.makeDOMDriver('#app1'),
	HTTP: CycleHTTPDriver.makeHTTPDriver(),
	components: d => d,
});
