var stampit = require('stampit');



cached = stampit({
	init: function(context) {
		var cache = {};
		context.cache = function cache() {
			cache[this._id] = this;
		};
		context.uncache = function uncache() {
			delete cache[this._id];
		};
	},
});

module.exports = cached;
