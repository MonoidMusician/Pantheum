var h = require('react-hyperscript');
var React = require('react');

var Icon = require('../../components/Icon');
var createClass = require('../../createClass');
var Abbreviation = require('../Abbreviation');

module.exports = createClass({
	displayName: 'view.Attribute',
	contextTypes: {
		user: React.PropTypes.object,
	},
	delete_API() {
		var {tag, value, id, onDelete} = this.props;
		console.log(tag, value, id, onDelete);
	},
	render: function renderAttribute() {
		var {lang, tag, value} = this.props;
		var result;
		if (lang && lang.attributes && lang.attributes.abbreviation) {
			let res = lang.attributes.abbreviation(tag, value);
			if (res)
				result = Abbreviation.h({title:res[1]}, res[0]);
		}
		if (!result) result = [null,true,'true'].includes(value) ? tag : tag+"="+value;
		var user = this.props.user || this.context.user;
		if (user && user.administrator)
			return h('span', [result, Icon.h.small.delete(this.delete_API)]);
		return result;
	}
});
