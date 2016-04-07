var connection = require('./mysql');
var Definition = require('./definition');

var D = Definition({}, 19, true);
console.log('D:', D, Object.getPrototypeOf(D));
var D2 = Definition({id:19}, true);
console.log('Equal?', D2 === D);
