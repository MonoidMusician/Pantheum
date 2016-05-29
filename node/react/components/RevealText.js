var h = require('react-hyperscript');
var ReactDOM = require('react-dom');

var createClass = require('../createClass');

module.exports = createClass({
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
		var element = ReactDOM.findDOMNode(this);
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
