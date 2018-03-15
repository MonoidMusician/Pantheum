var Score = require('../../Score');

module.exports = function*(quiz) {
	var {options, length} = quiz;
	var {person, number} = options;
	while (questions.length < length) {
		let questions = [];
		let answers = [];

		let word = randomword();

		yield {answers, questions};
	}
	return quiz;
};

module.exports.options = {
	'person': ['random', 'person-1', 'person-2', 'person-3'],
	'number': ['random', 'singular', 'plural'],
};
module.exports.length = Infinity;
