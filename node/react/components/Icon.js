var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var createClass = require('../createClass');
var $dom = require('../jquery-dom');


var Icon = createClass({
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
		var glyph = Icon.glyphs[this.props.type];
		var title = (s=>s[0].toUpperCase()+s.substr(1))(this.props.type);

		var classes = this.props.className || [];
		if (typeof classes === 'string') classes = classes.split(" ");
		classes.push('oi', 'inline', 'spaced');

		var styles = [Icon.style.base];
		if (this.props.small) styles.push(Icon.style.small);
		if (this.props.nospace) styles.push({paddingLeft:null});
		var style = Object.assign({}, ...styles);
		if (this.state.active) style.color = 'red';
		else if (this.state.focus) style.color = '#CC3333';
		else if (this.state.hover) style.color = '#DA9031';
		else style.color = '#DA7B00';

		var props = {
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
			'data-glyph': glyph,
		};
		return h('a', props);
	},
	componentDidMount: function() {
		$dom(this).qtip({
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
Icon.style = {
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
Icon.glyphs = {
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
Icon.h.small = function(props, children) {
	props = Object.assign({}, props||{}, {small:true});
	return Icon.h(props, children);
};
for (let fn of [Icon.h.small, Icon.h]) {
	for (let type in Icon.glyphs) {
		if (!/^[$_a-zA-Z][$_a-zA-Z0-9]*$/.test(type)) continue;
		fn[type] = function(props, children) {
			props = Object.assign({}, props||{}, {type});
			return fn(props, children);
		};
	}
}

module.exports = Icon;
