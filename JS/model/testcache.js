var connection = require('./mysql');
var Definition = require('./model').Definition;

var D = Definition({id:19}, true);
console.log('D:', D, Object.getPrototypeOf(D));
var D2 = Definition({id:19, cacheable:true});
console.log('Equal?', D2 === D);
var D3 = Definition(D2);
console.log('Equal?', D3 === D);
