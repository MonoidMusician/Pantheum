var connection = require("./mysql");
var Promise = require('promise');

function promiseQuery(...arg) {
	return new Promise((resolve, reject) => {
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
	});
}

module.exports = promiseQuery;
