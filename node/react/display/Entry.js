var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var model = require('../../model');
var createClass = require('../createClass');
var la_ipa = require('../../lib/la_ipa');

var EditableText = require('../components/EditableText');
var Icon = require('../components/Icon');

var Language = require('./Language');
var WordName = require('./WordName');
var Attributes = require('./Attributes/Attributes');
var Definitions = require('./Definitions/Definitions');

var {create_table, InflectionTable} = require('./InflectionTable');

var languages = {
	"la": "Latin",
	"en": "English",
};

var Inflection = createClass({
	displayName: 'view.Inflection',
	getInitialState() {
		return {onlyleaves:false};
	},
	handleCheckbox(event, onlyleaves) {
		this.setState({onlyleaves});
	},
	render: function renderInflection() {
		var edit;
		var user = this.props.user || this.context.user;
		if (user && user.administrator)
			edit = Icon.h({type:"delete"});
		var {onlyleaves} = this.state;
		var mgr = this.props.mgr;
		var sorted = model.Path.sort(this.props.forms);
		if (onlyleaves) sorted = sorted.filter((form, i) => i===sorted.length-1 || !sorted[i+1].issub(form));
		var rows = [
			mgr.all_sub_keys.concat(['value']),
			...sorted.map(
				(form, key) => mgr.all_sub_keys.map(k=>form.key_value(k)).concat([
					EditableText.h({
						disabled: !user || !user.administrator,
						onNewValue: this.handleNewValue,
						value: form.value && form.value.split('\n').join(', '),
						key: form.value,
					})
				])
			)
		];
		return h('div', [
			h(MaterialUI.Checkbox, {
				label: 'Show only leaf nodes',
				checked: this.state.onlyleaves,
				onCheck: this.handleCheckbox,
			}),
			create_table.merge_vertical(rows, {}, {className:'inflection'}),
		]);
	}
});
var EntryName = createClass({
	displayName: 'view.EntryName',
	render: function renderEntryName() {
		return h('span', [Language.h({}, this.props.word.lang), WordName.h(this.props)]);
	}
});
var Wiktionary = createClass({
	displayName: 'view.Wiktionary',
	render: function renderWiktionary() {
		// TODO: slugify (transform æ, œ, macrons....)
		return h('a', {
			href: "http://en.wiktionary.org/wiki/"+this.props.word.name+"#"+languages[this.props.word.lang],
			target: "_blank"
		}, this.props.text||"Wiktionary");
	}
});
var LewisShort = createClass({
	displayName: 'view.LewisShort',
	render: function renderLewisShort() {
		if (this.props.word.lang != 'la') return h('span');
		// TODO: slugify (transform æ, œ, macrons....)
		return h('span', [' – ', h('a', {
			href: "http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry="+this.props.word.name,
			target: "_blank"
		}, this.props.word.text||"Lewis & Short")]);
	}
});
var PronunciationTool = createClass({
	displayName: 'view.PronunciationTool',
	transform: la_ipa.transforms["IPA transcription"],
	getInitialState() {
		return {value: ""};
	},
	handleChange({target: {value}}) {
		this.setState({value});
	},
	render: function renderPronunciationTool() {
		return h('span', [
			h('input',{onChange:this.handleChange}),
			h('span', this.transform(this.state.value))
		]);
	}
});
module.exports = createClass({
	displayName: 'view.Entry',
	getInitialState() {
		return {toolsOpen: false, classic:true};
	},
	toggleTools() {
		this.setState({toolsOpen: !this.state.toolsOpen});
	},
	handleCheckbox(event, classic) {
		this.setState({classic});
	},
	render: function renderEntry() {
		var tools, action = this.toggleTools;
		if (!this.state.toolsOpen) {
			tools = [Icon.h.tools({action})];
		} else {
			var k = 0;
			tools = [
				Icon.h.tools(   { key:k++, className: "hider", action }),
				Icon.h.hardlink({ key:k++, link: "dictionary.php?id="+this.props.id }),
				Icon.h.refresh( { key:k++ }),
				Icon.h.delete(     { key:k++ }),
				h('div', {key:k++,style:{"paddingLeft":"2em"}}, [
					Wiktionary.h(Object.assign({}, this.props, {key:0})),
					LewisShort.h(Object.assign({}, this.props, {key:1})),
					h('br', {key:2}),
					'Pronunciation: ',
					PronunciationTool.h({key:4})
				])
			];
		}
		return h('section', {id:this.id}, [
			EntryName.h(this.props),
			" ",
			Attributes.h(this.props),
			...tools,
			Definitions.h({definitions:this.props.word.definitions}),
			h(MaterialUI.Checkbox, {
				label: 'Show classic inflection table',
				checked: this.state.classic,
				onCheck: this.handleCheckbox,
			}),
			h('div', {style:{marginBottom:'1ex'}}),
			this.state.classic ?
				InflectionTable.h({word:this.props.word}) :
				Inflection.h({forms:this.props.word.forms, mgr:this.props.word.mgr}),
			h('hr'),
		]);
	}
});