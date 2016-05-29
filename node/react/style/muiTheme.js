var colors = require('./colors');

var muiStyles = require('material-ui/styles');

module.exports = function(req) {
	return muiStyles.getMuiTheme({
		fontFamily: 'Linux Biolinum',
		palette: {
			primary1Color: colors.primary,
			accent1Color:  colors.accent,
		},
	}, req ? {userAgent:req.headers['user-agent']} : {});
};
