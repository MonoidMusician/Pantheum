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
	view.Checkbox = view.createClass({
		displayName: 'view.Checkbox',
		getInitialState: function() {
			return {hover:false, focus:false};
		},
		enter: function() {
			this.setState({hover:true});
		},
		leave: function() {
			this.setState({hover:false});
		},
		focus: function() {
			this.setState({focus:true});
		},
		blur: function() {
			this.setState({focus:false});
		},
		handleKeyPress: function(event) {
			var {which:key} = event;
			if (key === 13 || key === 32) {
				if (this.props.onNewValue)
					this.props.onNewValue(!this.props.checked);
				return event.preventDefault();
			}
		},
		handleChange: function(event) {
			if (this.props.onChange)
				this.props.onChange(event);
			var {target: {checked}} = event;
			if (this.props.onNewValue)
				this.props.onNewValue(checked);
		},
		render: function() {
			var style = (k,a) => view.expand.style.make.call(this, a, view.Checkbox.style[k]);
			return h('label', {
				style: style('label', this.props.style),
				onMouseEnter: this.enter,
				onMouseLeave: this.leave,
				onFocus: this.focus,
				onBlur: this.blur,
				onMouseUp: this.blur,
				onKeyPress: this.handleKeyPress,
				tabIndex: 0,
			}, [
				h('input', Object.assign({}, this.props, {onChange: this.handleChange, type:'checkbox', style: style('input')})),
				h('span', {style: style('box')}),
				h('span', {style: style('check')}),
				h('span', this.props.children)
			]);
		},
	});
	view.Checkbox.style = {
		label: {
			width: 20,
			position: 'relative',
			outline: 0,
		},
		input: {
			visibility: 'hidden',
			marginLeft: '10px',
		},
		box: {
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
		},
		check: {
			opacity: function() {
				return (this.props.checked ? 1 : (this.state.hover || this.state.focus ? 0.4 : 0));
			},
			border: function() {
				return '3px solid '+(this.state.focus ? '#C33' : '#333');
			},
			borderTop: 'none',
			borderRight: 'none',
			cursor: 'pointer',
			position: 'absolute',
			width: 9,
			height: 5,
			background: 'transparent',
			top: 4,
			left: 4,
			transform: 'rotate(-45deg)',
		}
	}

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
