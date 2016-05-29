module.exports.expand = require('./expand');

Object.assign(module.exports, require('./components'), require('./display'));
module.exports.Page = require('./html');
module.exports.pages = require('./pages');
module.exports.style = require('./style');
