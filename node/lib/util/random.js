var Random = require('random-js');
var mt = Random.engines.mt19937().autoSeed();
module.exports = new Random(mt);
