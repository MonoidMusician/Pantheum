var express    = require('express');
var url        = require('url');
var Promise    = require('bluebird');
var quiz       = require('./main');

var router = express.Router();
module.exports = router;

var handle = function(res, err) {
	console.log(err.stack);
	if (err instanceof Error && !Object.keys(err).length)
		err = err.message;
	res.send(err);
};

var handler = function(gen) {
	return Promise.coroutine(function*(req, res, next) {
		try {
			return yield* gen(req, res, next);
		} catch(err) {
			return handle(res, err);
		}
	});
};

router.get('/', function(req, res) {
	res.send(req.method);
});

var quiz = {
	current_page: 0,
	length: 2,
	pages: [],
	score: undefined,
};

function get_public(data) {
	return data;
}

var pages = [{
	name: "Page 1: Hello",
	help: "Type “Hello” as the correct answer!",
	questions: [{
		type: 'question-FR',
		challenge: "Type hello",
		floating: "Hello",
		response: "",
		answer: "hello",
		score: undefined,
	}],
	locked: false,
}, {
	name: "Page 2: Help",
	questions: [{
		type: 'question-FR',
		challenge: "Help",
		response: "",
		answer: "help",
		score: undefined,
	}],
	locked: false,
}];
quiz.pages = pages;

router.get('/-1', function(req, res) {
	res.json(get_public(quiz));
});

router.get('/-1/pages', function(req, res) {
	res.json(get_public(quiz.pages));
});

router.get('/-1/pages/0', function(req, res) {
	quiz.pages[0] = pages[0];
	res.json(get_public(quiz.pages[0]));
});
router.get('/-1/pages/1', function(req, res) {
	quiz.pages[1] = pages[1];
	res.json(get_public(quiz.pages[1]));
});
function update_page(page, data) {
	if (!data) return;
	if (data.questions) {
		for (let key in data.questions) {
			let qdata = data.questions[key];
			if (!qdata) continue;
			let question = page.questions[key];
			if ('response' in qdata) {
				question.response = qdata.response;
				question.score = [question.response == question.answer ? 1 : 0, 1];
			}
		}
	}
	page.score = add_scores(page.questions);
	quiz.score = add_scores(quiz.pages);
	if (!page.locked && data.locked) page.locked = true;
}
function add_scores(source) {
	var score = [0, 0];
	for (let s of source) {
		if (!s.score) return;
		score[0] += s.score[0];
		score[1] += s.score[1];
	}
	return score;
}
router.post('/-1/pages/0', function(req, res) {
	var page = quiz.pages[0];
	if (page.locked) {
		return handle(res, new Error("Page 0 of quiz -1 had been locked"));
	}
	update_page(page, req.body);
	res.json(get_public(page));
});
router.post('/-1/pages/1', function(req, res) {
	var page = quiz.pages[1];
	if (page.locked) {
		return handle(res, new Error("Page 1 of quiz -1 had been locked"));
	}
	update_page(page, req.body);
	res.json(get_public(page));
});
router.get('/-1/scores', function(req, res) {
	var scores = quiz.pages.map(page => page.score).concat([quiz.score]);
	res.json(scores);
});
