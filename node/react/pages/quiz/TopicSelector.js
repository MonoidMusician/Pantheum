var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
var muiThemeable = require('material-ui/styles/muiThemeable').default; // wat??

var createClass = require('../../createClass');

var NAME = 'quiz-category';

var TopicSelectable = muiThemeable()(createClass({
	render: function renderTopicSelectable() {
		var {palette} = this.props.muiTheme;
		var props = Object.assign({}, this.props);
		delete props.children; delete props.muiTheme;
		return h('span', [
			h('input', Object.assign(props, {
				type: 'radio',
				style: {display:'none'},
			})),
			h('label', {
				htmlFor: this.props.id,
				style: Object.assign({
					margin: '0px 3px',
					padding: '5px 5px 2px',
					borderBottom: '2px solid '+palette.primary1Color,
					color: this.props.checked ? palette.primary2Color : palette.primary1Color,
					fontWeight: this.props.checked ? 'bold' : 'normal',
					lineHeight: '1.5'
				}, this.props.style),
			}, this.props.children)
		])
	}
}));

module.exports = createClass({
	render() {
		var {topics, value} = this.props;
		var children = [], childs;
		var i = 0;
		for (let topic of topics) {
			let tprops = Object.assign({
				name: NAME,
				id: NAME+(i++),
				checked: topic.value == this.props.value,
				onChange: event => this.props.onChange(event, topic.value),
			}, topic);
			if (tprops.checked)
				childs = tprops.children;
			children.push(h(TopicSelectable, tprops, tprops.label));
		}
		children = [h('div', {style:{marginBottom:15}}, children)];
		if (childs) children = children.concat(childs);
		return h('div', children);
	},
});
