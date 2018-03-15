function Quiz(type, length, user, options) {
	var quiz = Object.create(Quiz.prototype);
	if (!options) options = {};

	quiz.type = type;
	quiz.length = length;
	quiz.user = user;

	quiz.security = null;
	quiz.pages = [];
	quiz.score = undefined;
	quiz.status = "loading";
	return Promise.resolve(type.init()).then(state => {
		quiz.generator_state = state;
		return quiz;
	});
}

Object.assign(Quiz.prototype, {
	nextpage() {
		return this.type.next(this.state).then(([page,state]) => {
			this.state = state;
			this.pages.push(page);
			return page;
		});
	},
});
