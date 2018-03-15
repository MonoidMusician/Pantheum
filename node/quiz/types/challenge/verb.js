var Promise = require('bluebird');

var random = require('../../../lib/util/random');

console.log(random.integer(0, 32));

var model = require('../../../model');
var queryP = require('../../../model/mysqlpromise');

var FR = require('../../react/question/freeresponse');

// `state` should be immutable and serializable via .toJSON()
module.exports.init = function(quiz) {
	var state = queryP("SELECT word_id FROM words WHERE word_spart = (?) AND word_lang = (?) AND word_id NOT IN (SELECT word_id FROM attributes WHERE attr_tag = (?)) AND word_id NOT IN (SELECT word_id FROM attributes WHERE attr_tag = (?)) AND word_id NOT IN (SELECT word_id FROM attributes WHERE attr_tag = (?)) AND EXISTS (SELECT 1 FROM forms WHERE forms.word_id = words.word_id AND form_tag != '' AND form_value != '')", ['verb', 'la', 'irregular', 'hidden', 'template']).pick.word_id.then(word_ids => ({word_ids}));
	return state;
};
module.exports.next = Promise.coroutine(function*(state) {
	var id = random.pick(state.word_ids);
	var word = model.Word({id});
	yield Promise.all([
		word.pull(),
		word.pullchildrenscarce([model.Path])
	]);
	var path = yield random.pick(word.forms).pull();
	var forms = path.get().split('\n');
	var path_desc = path.tag;
	return [{
		help: `What is the ${path_desc} for ${word.name}?`,
		sentence: [{
			type: 'question-FR',
			label: "Enter form",
			//value: "",
			answer: forms//.map(w => format_wordlink(word, w, true, true)),
		}],
	}, state];
});

module.exports.name = "Random verb forms";
module.exports.language = "la"; // TODO
module.exports.length = Infinity;
