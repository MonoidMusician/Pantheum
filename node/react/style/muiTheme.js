var default_palette = require('./palette');
var {serif, sansserif} = require('./fonts');

var muiStyles = require('material-ui/styles');

module.exports = function({req, user}) {
	var palette = user && user.palette ? Object.assign({}, default_palette, user.palette) : default_palette;
	return muiStyles.getMuiTheme({
		fontFamily: serif,
		palette,
		// Note: this actually does not have any effect ... see CSS/react.css
		button: {
			fontFamily: sansserif,
		},
		radioButton: {
			checkedColor: palette.primary2Color,
		},
		checkbox: {
			checkedColor: palette.primary2Color,
		},
	}, req ? {userAgent:req.headers['user-agent']} : {});
};
