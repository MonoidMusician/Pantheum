var React = require('react');
var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');

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
	contextTypes: {
		user: React.PropTypes.object,
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
			mgr.all_sub_keys.concat(['value']).map(
				k => h('th', {
					style: {
						textAlign: 'left',
					},
				}, k)
			),
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
var Wiktionary = createClass({
	displayName: 'view.Wiktionary',
	render: function renderWiktionary() {
		// TODO: slugify (transform æ, œ, macrons....)
		return h('span', [
			h('img', {
				src: 'https://en.wiktionary.org/favicon.ico',
				style: {
					height: '3ex',
					width: '3ex',
					verticalAlign: 'bottom',
					position: 'relative',
					top: '2px',
					marginRight: '4px',
				}
			}),
			h('a', {
				href: "http://en.wiktionary.org/wiki/"+this.props.word.name+"#"+languages[this.props.word.lang],
				target: "_blank"
			}, this.props.text||"Wiktionary")
		]);
	}
});
var LewisShort = createClass({
	displayName: 'view.LewisShort',
	render: function renderLewisShort() {
		if (this.props.word.lang != 'la') return h('span');
		// TODO: slugify (transform æ, œ, macrons....)
		return h('span', [
			' – ',
			h('img', {
				src: "data:image/png;base64,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",
				style: {
					height: '3ex',
					width: '3ex',
					verticalAlign: 'bottom',
					position: 'relative',
					top: '2px',
					marginRight: '4px',
				}
			}),
			h('a', {
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
			h(MaterialUI.TextField,{
				floatingLabelText: 'Pronounce!',
				hintText: 'Orthography',
				onChange:this.handleChange,
			}),
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
	contextTypes: {
		user: React.PropTypes.object,
	},
	render: function renderEntry() {
		var tools, action = this.toggleTools;
		var user = this.props.user || this.context.user;
		var editor = user && user.administrator;
		var k = 0;
		if (!this.state.toolsOpen) {
			tools = [Icon.h.tools({action})];
		} else {
			tools = [
				Icon.h.tools(   { key:k++, action }),
				Icon.h.hardlink({ key:k++, link: "dictionary.php?id="+this.props.id }),
				Icon.h.refresh( { key:k++ }),
				Icon.h.delete(     { key:k++ }),
				h('div', {key:k++,style:{paddingLeft:'2em'}}, [
					Wiktionary.h(Object.assign({}, this.props, {key:0})),
					LewisShort.h(Object.assign({}, this.props, {key:1})),
					h('br', {key:2}),
					"Pronunciation: ",
					PronunciationTool.h({key:4})
				])
			];
		}
		return h(MaterialUI.Card, {
			expandable: !editor,
		}, [
			h(MaterialUI.CardTitle, {
				actAsExpander: !editor,
				title: [Language.h({key:0}, this.props.word.lang), WordName.h(Object.assign({}, this.props, {key:1}))],
				subtitle: Attributes.h(this.props),
			}, Definitions.h({definitions:this.props.word.definitions})),
			h(MaterialUI.CardTitle, {
				expandable: !editor,
			}, [
				h(MaterialUI.Checkbox, {
					label: "Show classic inflection table",
					checked: this.state.classic,
					onCheck: this.handleCheckbox,
				}),
				h('div', {style:{marginBottom:'1ex'}}),
				this.state.classic ?
					InflectionTable.h({word:this.props.word}) :
					Inflection.h({forms:this.props.word.forms, mgr:this.props.word.mgr}),
			]),
			...(editor ? [h(MaterialUI.CardActions, [
				h(MaterialUI.FlatButton, {
					label: "Hardlink",
					icon: h(MaterialUI.svgicons.ContentLink),
					href: "dictionary.php?id="+this.props.id,
					linkButton: true,
				}),
				h(MaterialUI.FlatButton, {
					label: "Refresh",
					icon: h(MaterialUI.svgicons.NavigationRefresh),
				}),
				h(MaterialUI.FlatButton, {
					label: "Delete",
					icon: h(MaterialUI.svgicons.ActionDeleteForever)
				}),
				PronunciationTool.h(),
				h('br'),
				Wiktionary.h(this.props),
				LewisShort.h(this.props),
			])] : [])
		]);
	}
});