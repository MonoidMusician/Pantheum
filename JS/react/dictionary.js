Plugins.AutosizeInput.getDefaultOptions().space = 30;

(function({view, model}) {
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
	var createClassR = c => Radium(createClass(c));

	view.Language = createClass({
		displayName: 'view.Language',
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
		displayName: 'view.WordName',
		handleNewValue: function(name) {
			console.log(name);
		},
		render: function() {
			var classes = ["word-name"];
			if (this.props.word.lang)
				classes.push("format-word-"+this.props.word.lang);
			return view.EditableText.h({
				disabled: !pantheum.user.administrator,
				spanClassName: classes.join(" "),
				onNewValue: this.handleNewValue,
				value: this.props.word.name,
				display: this.props.word.entry,
			});
		}
	});
	view.Definitions = createClass({
		displayName: 'view.Definitions',
		handleNewValue: function(id) {
			return (name) => {
				console.log(name);
			};
		},
		render: function() {
			var edit;
			if (pantheum.user.administrator)
				edit = view.Icon.h({type:"del"});
			return h('ol', this.props.definitions.map((def, key) => {
				var tag = def.form_tag;
				return h('li', {key}, [
					view.Language.h(def.lang),
					tag && '(',
					tag && tag,
					tag && def.tag.value && ' “'+def.tag.value+'”',
					tag && ') ',
					view.EditableText.h({
						disabled: !pantheum.user.administrator,
						onNewValue: this.handleNewValue(def.id),
						value: def.value.split('\n').join(', '),
					}),
					edit
				]);
			}));
		}
	});

	var autokey = (el, i) => typeof el === 'undefined' ? i : el.key || el;
	view.create_table = function(data, {noheader}, props) {
		var rows = data.map(
			(row, i) => React.isValidElement(row) ? row :
				h('tr', {key:i}, row.map(
					(el, k) => React.isValidElement(el) && (['td','th'].includes(el.type)) ? el :
						el !== undefined ? h(i || noheader ? 'td' : 'th', {key:autokey(el, k)}, el) : el
				))
		);
		return h('table', props||{}, [h('tbody', rows)]);
	};
	view.create_table.merge_vertical = function(data, options, ...arg) {
		var {noheader} = options;
		var header = data.slice(0, +!noheader), rest = data.slice(+!noheader);
		var nrows = (i,start,v) => {
			if (!start) start = 0;
			if (!v) var v = rest[start][i];
			for (var j=start; j<rest.length; j++)
				if (rest[j][i] != v) break;
			return i ? Math.min(j-start, nrows(i-1,start)) : j-start;
		};
		var keep = (i,start) => {
			if (!start) return true;
			for (i; i>=0; i--)
				if (rest[start-1][i] != rest[start][i]) return true;
			return false;
		};
		data = header.concat(rest.map(
			(row, j) =>
				row.map((el, i) => {
					if (!keep(i, j)) return;
					return h('td', {key:autokey(el, i), rowSpan: nrows(i, j, el)}, el)
				})
		));
		return view.create_table(data, options, ...arg);
	};
	view.Inflection = createClass({
		displayName: 'view.Inflection',
		getInitialState: function() {
			return {onlyleaves:false};
		},
		handleCheckbox: function(onlyleaves) {
			this.setState({onlyleaves});
		},
		render: function() {
			var edit;
			if (pantheum.user.administrator)
				edit = view.Icon.h({type:"del"});
			var {onlyleaves} = this.state;
			var mgr = this.props.mgr;
			var sorted = model.Path.sort(this.props.forms);
			if (onlyleaves) sorted = sorted.filter((form, i) => i===sorted.length-1 || !sorted[i+1].issub(form));
			var rows = [
				mgr.all_sub_keys.concat(['value']),
				...sorted.map(
					(form, key) => mgr.all_sub_keys.map(k=>form.key_value(k)).concat([
						view.EditableText.h({
							disabled: !pantheum.user.administrator,
							onNewValue: this.handleNewValue,
							value: form.value && form.value.split('\n').join(', '),
							key: form.value,
						})
					])
				)
			];
			return h('div', [
				view.Checkbox.h({
					checked: this.state.onlyleaves,
					onNewValue: this.handleCheckbox,
				}, 'Show only leaf nodes'),
				view.create_table.merge_vertical(rows, {}, {className:'inflection'}),
			]);
		}
	});
	view.EntryName = createClass({
		displayName: 'view.EntryName',
		render: function() {
			return h('span', [view.Language.h(this.props.word.lang), view.WordName.h(this.props)]);
		}
	});
	view.Wiktionary = createClass({
		displayName: 'view.Wiktionary',
		render: function() {
			// TODO: slugify (transform æ, œ, macrons....)
			return h('a', {
				href: "http://en.wiktionary.org/wiki/"+this.props.word.name+"#"+languages[this.props.word.lang],
				target: "_blank"
			}, this.props.text||"Wiktionary");
		}
	});
	view.LewisShort = createClass({
		displayName: 'view.LewisShort',
		render: function() {
			if (this.props.word.lang != 'la') return h('span');
			// TODO: slugify (transform æ, œ, macrons....)
			return h('span', [' – ', h('a', {
				href: "http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry="+this.props.word.name,
				target: "_blank"
			}, this.props.word.text||"Lewis & Short")]);
		}
	});
	view.PronunciationTool = createClass({
		displayName: 'view.PronunciationTool',
		transform: la_ipa.transforms["IPA transcription"],
		getInitialState: function() {
			return {value: ""};
		},
		handleChange: function({target: {value}}) {
			this.setState({value});
		},
		render: function() {
			return h('span', [
				h('input',{onChange:this.handleChange}),
				h('span', this.transform(this.state.value))
			]);
		}
	});
	view.Entry = createClass({
		displayName: 'view.Entry',
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
				view.EntryName.h(this.props),
				" ",
				view.Attributes.h(this.props),
				...tools,
				view.Definitions.h({definitions:this.props.word.definitions}),
				view.Inflection.h({forms:this.props.word.forms, mgr:this.props.word.mgr}),
				h('hr'),
			]);
		}
	});
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
	view.word = word = pantheum.model.Word(word, true);
	word.onAttrDelete = function(tag, value) {
		delete word.attrs[tag];
		view.render();
	};
	view.render = function() {
		word.pullall().then(w=>ReactDOM.render(
			view.Entry.h({word}),
			document.getElementById('dictionary')
		));
	};
})(pantheum);
