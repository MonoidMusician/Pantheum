var h = require('react-hyperscript');

module.exports = function(view) {
	view.Input = view.createClass({
		displayName: 'view.Input',
		componentDidMount: function() {
			var input = view.$dom(this);
			if (this.props.autoSize)
				input.autosizeInput(this.props.autoSize||{space:30});
			if (this.props.autoFocus)
				input[0].focus(); // actually redundant...
			if (this.props.autoSelect)
				input[0].select();
		},
		handleChange: function(event) {
			if (this.props.onChange)
				this.props.onChange(event);
			var {target: {value}} = event;
			if (this.props.onNewValue)
				this.props.onNewValue(value);
		},
		render: function() {
			return h('input', Object.assign({}, this.props, {onChange: this.handleChange}));
		}
	});


	view.EditableText = view.createClass({
		displayName: 'view.EditableText',
		getInitialState: function() {
			return {
				initial: this.props.value,
				value: this.props.value,
				editing: false,
				focused: false,
				wasfocused: false,
			};
		},
		toggle: function() {
			this.setState({editing: !this.state.editing});
		},
		edit: function() {
			this.setState({editing: true, wasfocused: this.state.focused||true});
		},
		set: function(value) {
			this.setState({value});
		},
		done: function() {
			if (!this.state.value) return;
			this.setState({editing: false, initial: this.state.value});
			this.props.onNewValue(this.state.value);
		},
		cancel: function() {
			this.setState({editing: false, value: this.state.initial});
		},
		focus: function() {
			this.setState({focused: true});
		},
		blur: function() {
			this.setState({focused: false});
		},
		handleKeyUp: function({which: key}) {
			if (key === 13 || key === 32) this.done();
			else if (key === 27) this.cancel();
		},
		componentDidUpdate: function(){
			if (this.wasfocused && this._edit)
				view.$dom(this._edit)[0].focus();
		},
		render: function() {
			var text = this.props.display || this.state.value;
			this._edit = null;
			if (this.props.disabled) {
				var props = Object.assign({}, this.props);
				if (props.spanClassName) props.className = props.spanClassName;
				delete props.spanClassName;
				return h('span', props, text);
			}
			if (!this.state.editing) {
				var props = Object.assign({}, this.props);
				if (props.spanClassName) props.className = props.spanClassName;
				delete props.spanClassName;
				props.onClick = this.edit;
				return h('span', props, [
					text,
					view.Icon.h.small.edit({
						ref: r => this._edit = r,
					})
				]);
			} else {
				var props = Object.assign({}, this.props);
				if (props.inputClassName) props.className = props.inputClassName;
				delete props.inputClassName;
				props.autoFocus = props.autoSelect = props.autoSize = true;
				props.value = this.state.value;
				props.onBlur = this.cancel;
				props.onNewValue = this.set;
				props.onKeyUp = this.handleKeyUp;
				return view.Input.h(props);
			}
		}
	});

	view.Abbreviation = view.createClass({
		displayName: 'view.Abbreviation',
		render: function() {
			return h('abbr', {title:this.props.title}, this.props.children);
		},
		componentDidMount: function() {
			view.$dom(this).qtip({
				style: {
					classes: "qtip-light qtip-abbr"
				},
				position: {
					at: "top center",
					my: "bottom center",
					adjust: {y:5},
				},
				show: {
					delay: 200,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		},
		componentDidUpdate: function() {
			// Hint qtip to update
			view.$dom(this).attr('title', this.props.title);
		},
		componentWillUnmount: function() {
			view.$dom(this).qtip('destroy', true);
		}
	});
	view.format_abbr = function(desc, ...children) {
		return view.Abbreviation.h({title:desc}, children);
	};

	view.Icon = view.createClass({
		displayName: 'view.Icon',
		getInitialState: function() {
			return {hover:false, focus:false, active:false};
		},
		handleKeyUp: function(event) {
			event.preventDefault();
		},
		handleKeyDown: function(event) {
			event.preventDefault();
		},
		handleMouseOver: function() {
			this.setState({hover:true});
		},
		handleMouseOut: function() {
			this.setState({hover:false});
		},
		handleMouseDown: function() {
			this.setState({active:'viamouse'});
		},
		handleMouseUp: function() {
			this.setState({active:false});
		},
		handleFocus: function() {
			this.setState({focus:true});
		},
		handleBlur: function() {
			this.setState({focus:false});
		},
		render: function() {
			var glyph = view.Icon.glyphs[this.props.type];
			var title = (s=>s[0].toUpperCase()+s.substr(1))(this.props.type);

			var classes = this.props.className || [];
			if (typeof classes === 'string') classes = classes.split(" ");
			classes.push('oi', 'inline', 'spaced');

			var styles = [view.Icon.style.base];
			if (this.props.small) styles.push(view.Icon.style.small);
			if (this.props.nospace) styles.push({paddingLeft:null});
			var style = view.expand.make(...styles);
			if (this.state.active) style.color = 'red';
			else if (this.state.focus) style.color = '#CC3333';
			else if (this.state.hover) style.color = '#DA9031';
			else style.color = '#DA7B00';

			var props = view.expand({
				href: this.props.link||"javascript:void(0)",
				onClick: this.props.action||this.props.onClick,
				onKeyUp: this.handleKeyUp,
				onMouseOver: this.handleMouseOver,
				onMouseOut: this.handleMouseOut,
				onMouseDown: this.handleMouseDown,
				onMouseUp: this.handleMouseUp,
				onFocus: this.handleFocus,
				onBlur: this.handleBlur,
				className: classes.join(" "),
				title: this.props.desc||title,
				id: this.props.id,
				style: style,
				tabIndex: 0,
				data: {glyph},
			});
			return h('a', props);
		},
		componentDidMount: function() {
			view.$dom(this).qtip({
				style: {
					classes: "qtip-light qtip-abbr"
				},
				position: {
					at: "top center",
					my: "bottom center",
					adjust: {y:0},
				},
				show: {
					delay: 800,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		}
	});
	view.Icon.style = {
		base: {
			verticalAlign: 'sub',
			paddingLeft: '0.3em',
			color: '#DA7B00',
			textDecoration: 'none',
			outline: 'none',
			border: 'none',
		},
		small: {
			verticalAlign: 'inherit',
			fontSize: '60%',
			paddingLeft: '0.5em',
		},
	};
	view.Icon.glyphs = {
		"edit": "pencil",
		"refresh": "reload",
		"hardlink": "link-intact",
		"delete": "trash",
		"tools": "wrench",
		"rename": "text",
		"change POS": "compass", // FIXME
		"&lt;&lt;": "media-skip-backward",
		"&lt;": "media-step-backward",
		"&gt;": "media-step-forward",
		"&gt;&gt;": "media-skip-forward",
		"visibility": "eye",
		"add": "plus",
	};
	view.Icon.h.small = function(props, children) {
		props = Object.assign({}, props||{}, {small:true});
		return view.Icon.h(props, children);
	};
	for (let fn of [view.Icon.h.small, view.Icon.h]) {
		for (let type in view.Icon.glyphs) {
			if (!/^[$_a-zA-Z][$_a-zA-Z0-9]*$/.test(type)) continue;
			fn[type] = function(props, children) {
				props = Object.assign({}, props||{}, {type});
				return fn(props, children);
			};
		}
	}
};
