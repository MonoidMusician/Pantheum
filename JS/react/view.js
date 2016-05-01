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
	var $dom = function(component) {
		return $(ReactDOM.findDOMNode(component));
	};

	view.Input = createClass({
		displayName: 'view.Input',
		componentDidMount: function() {
			var input = $dom(this);
			if (this.props.autoSize)
				input.autosizeInput();
			if (this.props.autoFocus)
				input[0].focus(); // actually redundant...
			if (this.props.autoSelect)
				input[0].select();
		},
		render: function() {
			return h('input', this.props);
		}
	});

	/*
	.squaredFour {
		width: 20px;
		margin: 20px auto;
		position: relative;
	}

	.squaredFour label {
		cursor: pointer;
		position: absolute;
		width: 20px;
		height: 20px;
		top: 0;
		border-radius: 4px;

		-webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
		-moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
		box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
		background: #fcfff4;

		background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
		background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
		background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
		background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
		background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
	}

	.squaredFour label:after {
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
		filter: alpha(opacity=0);
		opacity: 0;
		content: '';
		position: absolute;
		width: 9px;
		height: 5px;
		background: transparent;
		top: 4px;
		left: 4px;
		border: 3px solid #333;
		border-top: none;
		border-right: none;

		-webkit-transform: rotate(-45deg);
		-moz-transform: rotate(-45deg);
		-o-transform: rotate(-45deg);
		-ms-transform: rotate(-45deg);
		transform: rotate(-45deg);
	}

	.squaredFour label:hover::after {
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";
		filter: alpha(opacity=30);
		opacity: 0.5;
	}

	.squaredFour input[type=checkbox]:checked + label:after {
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
		filter: alpha(opacity=100);
		opacity: 1;
	}
	*/
	view.Checkbox = createClass({
		displayName: 'view.Checkbox',
		getInitialState: function() {
			var id = this.props.id || view.Checkbox.newid();
			return {id, hover:false, focus:false};
		},
		handleMouseEnter: function() {
			this.setState({hover:true});
		},
		handleMouseLeave: function() {
			this.setState({hover:false});
		},
		handleFocus: function() {
			this.setState({focus:true});
		},
		handleBlur: function() {
			this.setState({focus:false});
		},
		handleKeyPress: function(event) {
			var key = event.which;
			if (key === 13 || key === 32) {
				if (this.props.onChange)
					this.props.onChange(!this.props.checked);
				return event.preventDefault();
			}
		},
		handleChange: function({target: {checked}}) {
			this.props.onChange(checked);
		},
		render: function() {
			var id = this.state.id;
			var divstyle = {
				width: 20,
				position: 'relative',
				outline: 0,
			};
			var inputstyle = {
				visibility: 'hidden',
			};
			var labelstyle = {
				cursor: 'pointer',
				position: 'absolute',
				top: 0,
				left: 0,
				width: 20,
				height: 20,
				borderRadius: 4,
				outline: 0,
				boxShadow: 'inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5)',
				background: 'linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%)',
			};
			var border = '3px solid '+(this.state.focus ? '#C33' : '#333');
			var afterstyle = {
				cursor: 'pointer',
				opacity: this.props.checked ? 1 : (this.state.hover || this.state.focus ? 0.5 : 0),
				position: 'absolute',
				width: 9,
				height: 5,
				background: 'transparent',
				top: 4,
				left: 4,
				borderLeft: border,
				borderBottom: border,
				transform: 'rotate(-45deg)',
			};
			return h('label', {
				style: divstyle,
				onMouseEnter: this.handleMouseEnter,
				onMouseLeave: this.handleMouseLeave,
				onFocus: this.handleFocus,
				onBlur: this.handleBlur,
				onMouseUp: this.handleBlur,
				onKeyPress: this.handleKeyPress,
				tabIndex: 0,
			}, [
				h('input', {...this.props, onChange: this.handleChange, id, type:'checkbox', style:inputstyle}),
				h('span', {style:labelstyle}),
				h('a', {style:afterstyle}),
				h('span', this.props.children)
			]);
		},
	});
	view.Checkbox.newid = function() {
		if (!this.id) this.id = 0;
		return 'react-checkbox-'+this.id++;
	};

	view.EditableText = createClass({
		displayName: 'view.EditableText',
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
				return view.Input.h(props);
			}
		}
	});

	view.Abbreviation = createClass({
		displayName: 'view.Abbreviation',
		render: function() {
			return h('abbr', {title:this.props.title}, this.props.children);
		},
		componentDidMount: function() {
			$dom(this).qtip({
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
			$dom(this).attr('title', this.props.title);
		},
		componentWillUnmount: function() {
			$dom(this).qtip('destroy', true);
		}
	});
	view.format_abbr = function(desc, ...children) {
		return view.Abbreviation.h({title:desc}, children);
	};

	view.Icon = createClass({
		displayName: 'view.Icon',
		render: function() {
			var glyph = view.Icon.glyphs[this.props.type];
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
	view.Icon.glyphs = {
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
	view.Icon.h.small = function(props, children) {
		props.small = true;
		return view.Icon.h(props, children);
	};
	for (let fn of [view.Icon.h.small, view.Icon.h]) {
		for (let type in view.Icon.glyphs) {
			if (!/^[$_a-zA-Z][$_a-zA-Z0-9]*$/.test(type)) continue;
			fn[type] = function(props, children) {
				props = {...props, type: type};
				return fn(props, children);
			};
		}
	}
})(pantheum.view);
