var colors = require('./colors');

var muiStyles = require('material-ui/styles');

module.exports = muiStyles.getMuiTheme({
	fontFamily: 'Linux Biolinum',
	palette: {
		primary1Color: colors.primary,
		accent1Color:  colors.accent,
	},
});
