var connection = require("./mysql");
var Promise = require('bluebird');

var extend = p => Object.defineProperty(p, "pick", {
	get() {
		return Object.defineProperty(p, "pick", {value:new Proxy(key => p.then(rs => rs.map(r => r[key])), {
			get(target, key, proxy) {
				return target(key);
			}
		})}).pick;
	},
	configurable: true,
});

module.exports = function promiseQuery(...arg) {
	return extend(new Promise((resolve, reject) => {
		try {
			connection.query(...arg, (err, results) => {
				if (err) reject(err);
				resolve(results);
			});
		} catch(e) {
			console.log(arg);
			console.log(e.stack);
			throw e;
		}
	}));
}
