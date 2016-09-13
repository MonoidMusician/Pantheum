({
	init: null,
	score(answers, key) {
		if (key) answers = {[key]:answers};
		if (key) return answers[key];
		return answers;
	},
	save() {},
	load() {},
})