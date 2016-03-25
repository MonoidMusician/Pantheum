Plugins.AutosizeInput.getDefaultOptions().space = 30;

(function(view) {
	"use strict";
	var languages = {
		"la": "Latin",
		"en": "English",
	};
	var createClass = function(c) {
		var r = React.createClass(c);
		r.h = h.bind(undefined, r);
		return r;
	};

	view.Language = createClass({
		render: function() {
			var title = this.props.name || languages[this.props.children];
			return h('sup', {title}, ["[",this.props.children,"]"]);
		},
		componentDidMount: function() {
			$(ReactDOM.findDOMNode(this)).qtip({
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
	view.WordName = createClass({
		handleNewValue: function(name) {
			console.log(name);
		},
		render: function() {
			var classes = ["word-name"];
			if (this.props.lang)
				classes.push("format-word-"+this.props.lang);
			return view.EditableText.h({
				disabled: !pantheum.user.administrator,
				spanClassName: classes.join(" "),
				onNewValue: this.handleNewValue,
				value: this.props.name,
				display: this.props.entry,
			});
		}
	});
	view.Definitions = createClass({
		handleNewValue: function(name) {
			console.log(name);
		},
		render: function() {
			var edit;
			if (pantheum.user.administrator)
				edit = view.Icon.h({type:"del"});
			return h('ol', this.props.definitions.map(function(def, key) {
				return h('li', {key}, [
					view.Language.h("en"),
					view.EditableText.h({
						disabled: !pantheum.user.administrator,
						onNewValue: this.handleNewValue,
						value: def,
					}),
					edit
				]);
			}, this));
		}
	});
	view.EntryName = createClass({
		render: function() {
			return h('span', [view.Language.h(this.props.lang), view.WordName.h(this.props)]);
		}
	});
	view.Wiktionary = createClass({
		render: function() {
			// TODO: slugify (transform æ, œ, macrons....)
			return h('a', {
				href: "http://en.wiktionary.org/wiki/"+this.props.name+"#"+languages[this.props.lang],
				target: "_blank"
			}, this.props.text||"Wiktionary");
		}
	});
	view.LewisShort = createClass({
		render: function() {
			if (this.props.lang != 'la') return h('span');
			// TODO: slugify (transform æ, œ, macrons....)
			return h('span', [' – ', h('a', {
				href: "http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry="+this.props.name,
				target: "_blank"
			}, this.props.text||"Lewis & Short")]);
		}
	});
	view.PronunciationTool = createClass({
		transform: la_ipa.transforms["IPA transcription"],
		getInitialState: function() {
			return {value: ""};
		},
		handleChange: function(event) {
			this.setState({value: event.target.value});
		},
		render: function() {
			return h('span', [
				h('input',{onChange:this.handleChange}),
				h('span', this.transform(this.state.value))
			]);
		}
	});
	view.Entry = createClass({
		getInitialState: function() {
			return {toolsOpen: false};
		},
		toggleTools: function() {
			this.setState({toolsOpen: !this.state.toolsOpen});
		},
		render: function() {
			var tools, action = this.toggleTools;
			if (!this.state.toolsOpen) {
				tools = [view.Icon.h.tools({action})];
			} else {
				var k = 0;
				tools = [
					view.Icon.h.tools(   { key:k++, className: "hider", action }),
					view.Icon.h.hardlink({ key:k++, link: "dictionary.php?id="+this.props.id }),
					view.Icon.h.refresh( { key:k++ }),
					view.Icon.h.del(     { key:k++ }),
					h('div', {key:k++,style:{"paddingLeft":"2em"}}, [
						view.Wiktionary.h({...this.props,key:0}),
						view.LewisShort.h({...this.props,key:1}),
						h('br', {key:2}),
						'Pronunciation: ',
						view.PronunciationTool.h({key:4})
					])
				];
			}
			return h('section', {id:this.id}, [
				h('hr'),
				view.EntryName.h(this.props),
				" ",
				view.Attributes.h(this.props),
				...tools,
				view.Definitions.h({definitions:this.props.definitions})
			]);
		}
	});
	var word = {
		id: 10176,
		lang: "la",
		name: "sum",
		entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
		spart: "verb",
		attrs: {
			common:true,
			copulative:true,
			irregular:true,
			transitive:false
		},
		definitions: ["be, exist"],
		forms: {},
	};
	word.onAttrDelete = function(tag, value) {
		delete word.attrs[tag];
		view.render();
	};
	view.render = function() {
		ReactDOM.render(
			view.Entry.h(word),
			document.getElementById('dictionary')
		);
	};
})(pantheum.view);
