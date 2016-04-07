var connection = require('./mysql');
var model = require('./definition');

var D = model.Definition({}, 19, true);
console.log('D:', D, Object.getPrototypeOf(D));
var D2 = model.Definition({id:19}, true);
console.log('Equal?', D2 === D);
