var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');

var createClass = require('../../createClass');

module.exports = createClass({
	displayName: 'view.Notifications',
	render: function renderNotifications() {
		if (this.props.value === null)
			return h(MaterialUI.IconButton, {
				style: {padding: 4},
				tooltip: 'No notifications',
			}, h(MaterialUI.svgicons.SocialNotificationsNone, this.props));
		return h(MaterialUI.Badge, {
			badgeContent: this.props.value,
			style: {padding: 4},
		}, h(MaterialUI.IconButton, {
			tooltip: 'Notifications',
		}, h(MaterialUI.svgicons.SocialNotifications, this.props)));
	},
});
