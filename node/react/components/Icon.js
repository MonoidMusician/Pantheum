var React = require('react');
var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');

var createClass = require('../createClass');
var $dom = require('../jquery-dom');


var Icon = createClass({
	displayName: 'view.Icon',
	contextTypes: {
		muiTheme: React.PropTypes.object,
	},
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
		if (0 && this.props.type in Icon.material) {
			var size = this.props.small ? 24 : 36;
			var iconSize = this.props.small ? 12 : 24;
			var mprops = Object.assign({
				tooltip: this.props.desc,
				onTouchTap: this.props.action||this.props.onClick,
				style: {
					width: size,
					height: size,
					padding: this.props.small ? 5.3 : 5,
					marginLeft: this.props.nospace ? 0 : (this.props.small ? 4 : 0),
				},
				iconStyle: {
					width: iconSize,
					height: iconSize,
				}
			}, this.props, {
				desc: undefined,
				action: undefined,
				link: undefined,
				type: undefined,
			});
			if (!this.props.small) {
				mprops.style.position = 'relative';
				mprops.style.top = 6;
			}
			if (this.props.link) {
				mprops.href = this.props.link;
				mprops.linkButton = true;
			}
			return h(MaterialUI.IconButton, mprops, h(
				MaterialUI.svgicons[Icon.material[this.props.type]], {
					color: this.context.muiTheme.palette.accent1Color,
					//hoverColor: this.context.muiTheme.palette.accent2Color,
				}
			));
		}
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
Icon.material = {
	"edit": "EditorModeEdit",
	"add": "ContentAdd",
	"delete": "ActionDelete",
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
