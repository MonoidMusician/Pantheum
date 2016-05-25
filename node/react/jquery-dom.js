var ReactDOM = require('react-dom');

module.exports = function $dom(component) {
	return $(ReactDOM.findDOMNode(component));
};
