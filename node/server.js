// BASE SETUP

// call the packages we need
var express     = require('express');
var bodyParser  = require('body-parser');
var morgan      = require('morgan');
var serveStatic = require('serve-static');

// Create our server app
var app = express();

// Display request status
app.use(morgan('dev')); // log requests to the console

// Configure body parser
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

var port = process.env.PORT || 8080; // set our port

app.use('/api', require('./model/router'));
app.use(require('./pages'));

// This is how we leak all the credentials for all the things.
app.use(serveStatic('../'));

// START THE SERVER/DOHICKY/WHATCHAMACALLIT/MAJGIICXZKS
app.listen(port);
console.log('Magic happens on port ' + port);
// Op, you missed it. Too late. Magic happened without you.
