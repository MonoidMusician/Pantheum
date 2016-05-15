var request = require('request-promise').defaults({
	baseUrl: 'http://52.3.75.179/api/',
});
var Promise = require('bluebird');

module.exports = function queryAPI(data, ...uri) {
	uri = uri.join('/');
	return request.post({
		uri,
		json: data,
	});
}
