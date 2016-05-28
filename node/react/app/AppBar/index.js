var React = require('react');
var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');

var createClass = require('../../createClass');

var Notifications = require('./Notifications');

module.exports = createClass({
	displayName: 'view.AppBar',
	contextTypes: {
		user: React.PropTypes.object,
	},
	render: function renderAppBar() {
		var user = this.props.user || this.context.user;
		var notifications = user && user.notifications;
		return h(MaterialUI.AppBar, Object.assign({
			title: "Latin",
			iconElementRight: user ? Notifications.h({
				value: notifications && notifications.length,
			}) : h(MaterialUI.IconButton, {
				style: {padding: 4},
				tooltip: 'Log in',
			}, h(MaterialUI.svgicons.ActionExitToApp, this.props)),
		}, this.props));
	},
});
