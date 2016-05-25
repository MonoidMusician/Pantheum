var React = require('react');
var h = require('react-hyperscript');

var createClass = require('../../createClass');

var intersperse = require('../../../lib/util/intersperse');

var EditableText = require('../../components/EditableText');
var Icon = require('../../components/Icon');
var Attribute = require('./Attribute');

var create_attribute = function(props) {return function(a, key) {
	if (React.isValidElement(a)) return a;
	var [tag, value] = a.split("=");
	if (!value) return a;
	return Attribute.h(Object.assign(props, {key: tag, tag, value}));
}};


module.exports = createClass({
	displayName: 'view.Attributes',
	handleNewValue(value) {
		console.log(value);
	},
	contextTypes: {
		user: React.PropTypes.object,
	},
	render: function renderAttributes() {
		var user = this.props.user || this.context.user;
		var props = {
			word: this.props.word,
			id: this.props.word.id,
			lang: this.props.word.lang,
			onDelete: this.props.onAttrDelete,
		};
		var spart = EditableText.h({
			key:0, disabled: !user || !user.administrator,
			value: this.props.word.spart,
			onNewValue: this.handleNewValue,
		});
		var attrs = [spart];
		if (!Array.isArray(this.props.word.attrs)) {
			for (let key in this.props.word.attrs) {
				attrs.push(key+'='+this.props.word.attrs[key]);
			}
		} else attrs.push(...this.props.word.attrs);
		if (user && user.administrator) attrs.push(Icon.h.add({key:attrs.length}));
		attrs = attrs.map(create_attribute(props));
		return h('span', ['(', ...intersperse(attrs, '; '), ')']);
	}
});
