var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var createClass = require('../../createClass');

var {romanize, reromanize, deromanize} = require('./lib/numerals');
var {verbalize} = require('./lib/numbers');

module.exports = createClass({
	displayName: 'page.tools.RomanNumerals',
	getInitialState() {
		return {
			number: '',
			numeral: '',
			_gender: 'masculine',
			_number: 'singular',
			_case: 'nominative',
		};
	},
	setGender(event, i, _gender) {
		this.setState({_gender});
	},
	setCase(event, i, _case) {
		this.setState({_case});
	},
	setNumber(event, i, _number) {
		this.setState({_number});
	},
	setArabic(event, number) {
		this.setState({number, numeral: romanize(number)});
	},
	setRoman(event, numeral) {
		numeral = numeral.toUpperCase();
		var number = deromanize(numeral);
		if (number === false) {
			if (numeral) return; // reject event
			else number = '';
		}
		this.setState({numeral, number});
	},
	render: function renderRomanNumerals() {
		var unicode = reromanize(this.state.numeral||'');
		var {cardinal, ordinal, distributive, adverbial} = verbalize(this.state);
		return h('article', [
			h('h2', "Roman numerals"),
			h('br'),
			h(MaterialUI.TextField, {
				hintText: "1337",
				floatingLabelText: "Arabic Number",
				type: 'number',
				min: 0, max: 499999,
				value: this.state.number,
				onChange: this.setArabic,
			}), ' = ',
			h(MaterialUI.TextField, {
				hintText: "XLII",
				floatingLabelText: "Roman Numeral",
				value: this.state.numeral||'',
				onChange: this.setRoman,
			}),
			unicode && ' = ', unicode,
			unicode && ' = ', unicode.toUpperCase(),
			h('br'),
			h(MaterialUI.SelectField, {
				value: this.state._gender,
				onChange: this.setGender,
			}, ['masculine', 'feminine', 'neuter'].map(
				value => h(MaterialUI.MenuItem, {value, primaryText: value[0].toUpperCase() + value.substr(1)})
			)),
			h(MaterialUI.SelectField, {
				value: this.state._number,
				onChange: this.setNumber,
			}, ['singular', 'plural'].map(
				value => h(MaterialUI.MenuItem, {value, primaryText: value[0].toUpperCase() + value.substr(1)})
			)),
			h(MaterialUI.SelectField, {
				value: this.state._case,
				onChange: this.setCase,
			}, ['nominative', 'accusative', 'ablative', 'dative', 'genitive', 'vocative'].map(
				value => h(MaterialUI.MenuItem, {value, primaryText: value[0].toUpperCase() + value.substr(1)})
			)),
			h('br'), 'Cardinal: ', cardinal,
			h('br'), 'Ordinal: ', ordinal,
			h('br'), 'Distributive: ', distributive,
			h('br'), 'Adverbial: ', adverbial,
		]);
	},
});
