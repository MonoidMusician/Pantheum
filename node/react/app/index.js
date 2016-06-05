var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');
MaterialUI.styles = require('material-ui/styles');

var createClass = require('../createClass');

var getMuiTheme = require('../style/muiTheme');
var UserProvider = require('../display/UserProvider');

var navigationWidth = 256;

var Navigation = require('./Navigation');
var AppBar = require('./AppBar');
var public_navigation = require('./Navigation/public');

module.exports = createClass({
	displayName: 'view.App',
	getInitialState() {
		return {
			navOpen: false,
			notifications: null,
		};
	},
	toggleNav() {
		this.setState({navOpen: !this.state.navOpen});
	},
	handleNav(navOpen) {
		this.setState({navOpen});
	},
	toggleNotifications() {
		var notifications = this.state.notifications;
		if (notifications === null) notifications = [null];
		else notifications = null;
		this.setState({notifications});
	},
	render: function renderApp() {
		var user = {
			administrator:true,
			notifications: this.state.notifications,
			/*
			palette: {
				primary1Color: '#0080ff',
				primary2Color: '#0037FF',
				accent1Color: '#BA6EFF',
			},
			*/
		};
		return h(MaterialUI.styles.MuiThemeProvider, {
			muiTheme:getMuiTheme({
				req: this.props.req,
				user,
			})
		}, [
			h(UserProvider, {user}, [
				AppBar.h({
					onLeftIconButtonTouchTap: this.toggleNav,
					onRightIconButtonTouchTap: this.toggleNotifications,
				}),
				Navigation.h({
					pages: this.props.navigation || public_navigation,
					value: this.props.page,
					open: this.state.navOpen,
					docked: false,
					onRequestChange: this.handleNav,
				}),
				h('div', {}, this.props.children)
			])
		]);
	},
});
