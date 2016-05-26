var h = require('react-hyperscript');
var MaterialUI = require('material-ui')

var createClass = require('../createClass');

var navigationWidth = 256;

var Navigation = require('./navigation');
var public_navigation = require('./navigation/public');

var getMuiTheme = require('../style/muiTheme');

module.exports = createClass({
	displayName: 'view.App',
	render: function renderApp() {
		return h(MaterialUI.styles.MuiThemeProvider, {muiTheme:getMuiTheme(this.props.req)}, [
			h("div", { style: { marginLeft: navigationWidth } }, [
				h(MaterialUI.AppBar, { title: "Latin", iconClassNameRight: "muidocs-icon-navigation-expand-more" }),
				Navigation.h({ pages: this.props.navigation || public_navigation, value: this.props.page }),
				h("div", {}, this.props.children)
			])
		]);
	},
});
