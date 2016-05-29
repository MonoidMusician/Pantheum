var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var colors = require('../../style/colors');

var createClass = require('../../createClass');

var NAME = 'quiz-category';

var TopicSelectable = createClass({
	render: function renderTopicSelectable() {
		return h('span', [
			h('input', Object.assign({}, this.props, {
				type: 'radio',
				style: {display:'none'},
			})),
			h('label', {
				htmlFor: this.props.id,
				style: Object.assign({
					margin: '0px 3px',
					padding: '5px 5px 2px',
					borderBottom: '2px solid '+colors.bold,
					color: this.props.checked ? colors.bold : colors.primary,
					fontWeight: this.props.checked ? 'bold' : 'normal',
					lineHeight: '1.5'
				}, this.props.style),
			}, this.props.children)
		])
	}
});

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
			children.push(TopicSelectable.h(tprops, tprops.label));
		}
		children = [h('div', {style:{marginBottom:15}}, children)];
		if (childs) children = children.concat(childs);
		return h('div', children);
	},
});
