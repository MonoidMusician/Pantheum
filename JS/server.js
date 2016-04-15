// BASE SETUP
// =============================================================================

// call the packages we need
var express    = require('express');
var bodyParser = require('body-parser');
var app        = express();
var morgan     = require('morgan');

// configure app
//app.use(morgan('dev')); // log requests to the console

// configure body parser
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

var port = process.env.PORT || 8080; // set our port

app.use('/api', require('./router'));

var proxy = require('express-http-proxy');
var url = require('url');
var qs = require('qs');

// New hostname+path as specified by question:
var apiProxy = proxy('localhost:8080', {
	forwardPath: function (req, res) {
		return url.parse(req.baseUrl).path+'?'+url.parse(req.url).query;
	}
});
app.use('*', apiProxy);

// START THE SERVER
// =============================================================================
app.listen(port);
console.log('Magic happens on port ' + port);
