(function(view) {
	"use strict";
	var createClass = function(c) {
		var r = React.createClass(c);
		r.h = h.bind(undefined, r);
		return r;
	};
	var propsdata = function(props, data) {
		for (let p in data) {
			props["data-"+p] = data[p];
		}
		return props;
	};

	view.Input = createClass({
		componentDidMount: function() {
			var input = ReactDOM.findDOMNode(this);
			if (this.props.autoSize)
				$(input).autosizeInput();
			if (this.props.autoFocus)
				input.focus(); // actually redundant...
			if (this.props.autoSelect)
				input.select();
		},
		render: function() {
			return h('input', this.props);
		}
	});
	view.EditableText = createClass({
		getInitialState: function() {
			return {editing: false, value: this.props.value, initial: this.props.value};
		},
		handleClick: function(event) {
			this.setState({editing: !this.state.editing});
		},
		handleChange: function(event) {
			this.setState({value: event.target.value});
		},
		handleKeyUp: function(event) {
			var key = event.which;
			if (key === 13) this.done();
			else if (key === 27) this.cancel();
		},
		done: function(event) {
			if (!this.state.value) return;
			this.setState({editing: false, initial: this.state.value});
			this.props.onNewValue(this.state.value);
		},
		cancel: function(event) {
			this.setState({editing: false, value: this.state.initial});
		},
		render: function() {
			var text = this.props.display || this.state.value;
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
				props.onClick = this.handleClick;
				return h('span', props, [text, view.Icon.h.small({type:"edit"})]);
			} else {
				var props = Object.assign({}, this.props);
				if (props.inputClassName) props.className = props.inputClassName;
				delete props.inputClassName;
				props.autoFocus = props.autoSelect = props.autoSize = true;
				props.value = this.state.value;
				props.onBlur = this.cancel;
				props.onKeyUp = this.handleKeyUp;
				props.onChange = this.handleChange;
				return h(view.Input, props);
			}
		}
	});
	view.Icon = createClass({
		render: function() {
			var glyph = {
				"edit": "pencil",
				"refresh": "reload",
				"hardlink": "link-intact",
				"del": "trash",
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
			glyph = glyph[this.props.type];
			var classes = this.props.className || [];
			if (typeof classes === 'string') classes = classes.split(" ");
			classes.push('oi', 'inline', 'spaced');
			if (this.props.small) classes.push('small');
			return h('a', propsdata({
				href: this.props.link||"javascript:void(0)",
				onClick: this.props.action||this.props.onClick,
				className: classes.join(" "),
				title: this.props.desc,
				id: this.props.id,
			}, {glyph}));
		},
		componentDidMount: function() {
			$(ReactDOM.findDOMNode(this)).qtip({
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
	var iconproxy = {
		get: function(target, name) {
			if (name in target) return target[name];
			return function(props, children) {
				props.type = name;
				return target(props, children);
			}
		}
	};
	view.Icon.h.small = function(props, children) {
		props.small = true;
		return view.Icon.h(props, children);
	};
	view.Icon.h.small = new Proxy(view.Icon.h.small, iconproxy);
	view.Icon.h = new Proxy(view.Icon.h, iconproxy);
})(pantheum.view);
