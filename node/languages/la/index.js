var model = require('../../model');

var depaths = require('./depaths');

for (let spart in depaths) {
	if (!(depaths[spart] instanceof model.Depath))
		depaths[spart] = new model.Depath('la/'+spart, depaths[spart]);
	model.Depath.add('la', spart, depaths[spart]);
}

var translate = require('./translate');

module.exports = {depaths, translate};
