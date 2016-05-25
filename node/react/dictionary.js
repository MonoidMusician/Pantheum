var ReactDOM = require('react-dom');
var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var model = require('../model');
var user = require('../user');

var la_ipa = require('../lib/la_ipa');


module.exports = function(view) {
	var languages = {
		"la": "Latin",
		"en": "English",
	};

	view.Language = view.createClass({
		displayName: 'view.Language',
		render: function renderLanguage() {
			var title = this.props.name || languages[this.props.children];
			return h('sup', {title}, ["[",this.props.children,"]"]);
		},
		componentDidMount: function() {
			view.$dom(this).qtip({
				style: {
					classes: "qtip-light qtip-abbr"
				},
				position: {
					at: "center left",
					my: "center right",
					adjust: {y:5},
				},
				show: {
					delay: 1000,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		}
	});
	view.WordName = view.createClass({
		displayName: 'view.WordName',
		handleNewValue(name) {
			console.log(name);
		},
		render: function renderWordName() {
			var classes = ["word-name"];
			if (this.props.word.lang)
				classes.push("format-word-"+this.props.word.lang);
			return view.EditableText.h({
				disabled: !user.administrator,
				spanClassName: classes.join(" "),
				onNewValue: this.handleNewValue,
				value: this.props.word.name,
				display: this.props.word.entry,
			});
		}
	});

	view.RevealText = view.createClass({
		displayName: 'view.RevealText',
		getInitialState() {
			return {min:undefined, max:undefined, hover:false};
		},
		handleMouseOver() {
			this.setState({hover:true});
		},
		handleMouseOut() {
			this.setState({hover:false});
		},
		componentDidMount() {
			var element = view.$dom(this)[0];
			element.textContent = this.props.long;
			var max = element.offsetWidth || undefined;
			element.textContent = this.props.short;
			var min = element.offsetWidth || undefined;
			this.setState({min,max});
		},
		render: function renderRevealText() {
			var {hover, min, max} = this.state;
			var {short,long} = this.props;
			var width, text;
			if (hover) {
				width = max;
				text = long;
			} else {
				width = min;
				text = short;
			}
			return h('span', {
				style: {
					display: 'inline-block',
					whiteSpace: 'nowrap',
					overflow: 'hidden',
					verticalAlign: 'text-top',
					transition: 'width '+(hover ? '0.4s ease-out' : '0.2s ease-in'),
					width,
				},
				onMouseOver: this.handleMouseOver,
				onMouseOut: this.handleMouseOut,
			}, text);
		},
	});

	view.DefTag = view.createClass({
		displayName: 'view.DefTag',
		getInitialState() {
			return {hover:false};
		},
		render: function renderDefTag() {
			var path = this.props.path, {tag, value} = path;
			var children = [];
			if (!path || (!tag && !value)) return h('span');
			if (tag) {
				if (tag.includes('/'))
					tag = view.RevealText.h({
						long: tag,
						short: tag.split('/')[0] + '/…',
					});
				children.push(tag);
				if (value) children.push(' ');
			}
			if (value) {
				children.push('“'+value+'”');
			}
			return h('span', ['(',...children,') ']);
		},
	})
	view.Definitions = view.createClass({
		displayName: 'view.Definitions',
		handleNewValue(id) {
			return (name) => {
				console.log(name);
			};
		},
		render: function renderDefinitions() {
			var edit;
			if (user.administrator)
				edit = view.Icon.h({type:"delete"});
			return h('ol', this.props.definitions.map((def, key) => {
				return h('li', {key}, [
					view.Language.h(def.lang),
					view.DefTag.h({path:def.tag}),
					view.EditableText.h({
						disabled: !user.administrator,
						onNewValue: this.handleNewValue(def.id),
						value: def.value.split('\n').join(', '),
					}),
					edit
				]);
			}));
		}
	});

	view.Inflection = view.createClass({
		displayName: 'view.Inflection',
		getInitialState() {
			return {onlyleaves:false};
		},
		handleCheckbox(event, onlyleaves) {
			this.setState({onlyleaves});
		},
		render: function renderInflection() {
			var edit;
			if (user.administrator)
				edit = view.Icon.h({type:"delete"});
			var {onlyleaves} = this.state;
			var mgr = this.props.mgr;
			var sorted = model.Path.sort(this.props.forms);
			if (onlyleaves) sorted = sorted.filter((form, i) => i===sorted.length-1 || !sorted[i+1].issub(form));
			var rows = [
				mgr.all_sub_keys.concat(['value']),
				...sorted.map(
					(form, key) => mgr.all_sub_keys.map(k=>form.key_value(k)).concat([
						view.EditableText.h({
							disabled: !user.administrator,
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
				view.create_table.merge_vertical(rows, {}, {className:'inflection'}),
			]);
		}
	});
	view.EntryName = view.createClass({
		displayName: 'view.EntryName',
		render: function renderEntryName() {
			return h('span', [view.Language.h(this.props.word.lang), view.WordName.h(this.props)]);
		}
	});
	view.Wiktionary = view.createClass({
		displayName: 'view.Wiktionary',
		render: function renderWiktionary() {
			// TODO: slugify (transform æ, œ, macrons....)
			return h('a', {
				href: "http://en.wiktionary.org/wiki/"+this.props.word.name+"#"+languages[this.props.word.lang],
				target: "_blank"
			}, this.props.text||"Wiktionary");
		}
	});
	view.LewisShort = view.createClass({
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
	view.PronunciationTool = view.createClass({
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
	view.Entry = view.createClass({
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
				tools = [view.Icon.h.tools({action})];
			} else {
				var k = 0;
				tools = [
					view.Icon.h.tools(   { key:k++, className: "hider", action }),
					view.Icon.h.hardlink({ key:k++, link: "dictionary.php?id="+this.props.id }),
					view.Icon.h.refresh( { key:k++ }),
					view.Icon.h.delete(     { key:k++ }),
					h('div', {key:k++,style:{"paddingLeft":"2em"}}, [
						view.Wiktionary.h(Object.assign({}, this.props, {key:0})),
						view.LewisShort.h(Object.assign({}, this.props, {key:1})),
						h('br', {key:2}),
						'Pronunciation: ',
						view.PronunciationTool.h({key:4})
					])
				];
			}
			return h('section', {id:this.id}, [
				view.EntryName.h(this.props),
				" ",
				view.Attributes.h(this.props),
				...tools,
				view.Definitions.h({definitions:this.props.word.definitions}),
				h(MaterialUI.Checkbox, {
					label: 'Show classic inflection table',
					checked: this.state.classic,
					onCheck: this.handleCheckbox,
				}),
				h('div', {style:{marginBottom:'1ex'}}),
				this.state.classic ?
					view.InflectionTable.h({word:this.props.word}) :
					view.Inflection.h({forms:this.props.word.forms, mgr:this.props.word.mgr}),
				h('hr'),
			]);
		}
	});
	view.pages['sum'] = {
		title: 'sum | Dictionary | Pantheum',
		heading: 'Dictionary entry for sum, esse, fui',
		data(get, form) {
			var word = {
				id: 10176,
				entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
				attrs: {
					common:true,
					copulative:true,
					irregular:true,
					transitive:false
				},
			};
			view.word = word = model.Word(word, true);
			return word.pullall();
		},
		toJSON(word) {
			return word.toJSON();
		},
		fromJSON(json) {
			var word;
			view.word = word = model.Word({}, true).fromJSON(json);
			Object.assign(word, {
				entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
				attrs: {
					common:true,
					copulative:true,
					irregular:true,
					transitive:false
				}
			});
			return word;
		},
		render(word) {
			var muiTheme = MaterialUI.styles.getMuiTheme();
			return h(MaterialUI.styles.MuiThemeProvider, {muiTheme}, view.Entry.h({word}));
		},
	};
};
