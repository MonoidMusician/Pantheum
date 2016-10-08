var R = require('ramda');

var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');
MaterialUI.styles = require('material-ui/styles');

var createClass = require('../createClass');

var Page = require('./Page');

var Question = require('./Question');

var {Provider, connect} = require('react-redux');
var {createStore} = require('redux');

module.exports = createClass({
	displayName: 'view.Quiz',
	render: function renderQuizBody() {
		return h(Provider, {store}, h(quizapp));
	},
});

var initialstate = {
	current_page: 0,
	max: 2,
	pages: [{
		key: 0,
		name: "Page 1",
		help: "Type “Hello” as the correct answer!",
		questions: [{
			key: 0,
			type: 'question-FR',
			label: "Type hello",
			floating: "Hello",
			value: "hello",
			answer: "hello",
			scored: true,
		}],
		status: 'scoring',
	}, {
		key: 1,
		name: "Page 2",
		questions: [{
			key: 1,
			type: 'question-FR',
			label: "Help",
			value: "help"
		}],
		status: 'loaded',
	}],
};

var chain = R.curryN(2, R.compose);
var makelenses = (lenses, subj) => {
	var res = {};
	for (let lense of lenses) {
		let lens = R.lensProp(lense);
		if (subj) lens = chain(subj, lens);
		res[lense] = lens;
	}
	return res;
}
var singular = (lenses, sg, pl) => {
	if (!pl) pl = sg+'s';
	lenses[sg] = i => chain(lenses[pl], R.lensIndex(i));
};

var lenses = makelenses(['current_page', 'pages']);
singular(lenses, 'page');
Object.assign(lenses.pages, makelenses(['questions']));
singular(lenses.pages, 'question');
Object.assign(lenses.pages.questions, makelenses(['value', 'answer', 'scored']));

function pages(state = initialstate, action) {
	//console.log(action.type, action);
	switch (action.type) {
		case 'ADD_PAGE':
			return R.over(lenses.pages, arr => ([
				...arr,
				page(undefined, action)
			]), state);
		case 'SET_PAGES':
			return R.set(action.lens, action.value, state);
		case 'NEXT_PAGE':
			return R.over(lenses.current_page, current_page => Math.min(current_page+1, state.pages.length), state);
		case 'PREVIOUS_PAGE':
			return R.over(lenses.current_page, current_page => Math.max(current_page-1, 0), state);
		case 'OPEN_PAGE':
			return R.set(lenses.current_page, action.index, state);
		default:
			return state;
	}
}

var store = createStore(pages);

var nextstate = props => {
	var {page} = props;
	if (props.current_page === props.max)
		return {finish:"Finish"};
	var {questions} = page;
	switch (page.status) {
		case 'loading':
		case 'saving':
		case 'scoring':
			return {loading:page.status};
		case 'loaded':
			var answered = questions.filter(q => q.value).length;
			var {length} = questions;
			if (answered < length)
				return {disabled:(100*answered/length)+'%'};
			// fall-through
		default:
			return {next:"Next"};
	}
};

var displaynext = props => {
	var {status, action} = props;
	if (status.loading) {
		return h(MaterialUI.RaisedButton, {
			label: status.loading,
			primary: true,
			style: {margin:'12px 0'},
			disabled: true,
			icon: h(MaterialUI.CircularProgress, {
				size: 0.36,
				style: {
					margin: '-7px -10px'
				},
			}),
		});
	}
	if (status.disabled) {
		return h(MaterialUI.RaisedButton, {
			label: status.disabled,
			primary: true,
			style: {margin:'12px 0'},
			disabled: true,
		});
	}
	if (status.next || status.finish) {
		return h(MaterialUI.RaisedButton, {
			label: status.next || status.finish,
			primary: true,
			onTouchTap: action,
			style: {margin:'12px 0'},
			disabled: false,
		});
	}
};

var quizapp = props => {
	//console.log(props);
	var {setPage} = props;
	return h('div', {}, [
		"HELLO ", props.quiz,
		h('div', {key:-1}, [
			h(MaterialUI.FlatButton, {
				key: 0,
				label: "Back",
				onTouchTap: props.onBack,
				style: {marginLeft:'12px'},
				disabled: !props.current_page,
			}),
			h(MaterialUI.SelectField, {
				key: 1,
				value: props.current_page,
				onChange: (event, value) => props.openPage(value),
			}, props.pages.map(({name}, index) => h(MaterialUI.MenuItem, {
				value: index, primaryText: name,
			}))),
			h(displaynext, {
				key: 2,
				status: nextstate(props),
				action: props.onNext,
			}),
		]),
		props.page ? page(props.page, chain(lenses.page(props.current_page)), setPage) : h(default_page, props),
	]);
};
quizapp = connect(state => ({
	quiz: state.quiz,
	current_page: state.current_page,
	max: state.max,
	pages: state.pages,
	page: state.pages[state.current_page],
}), dispatch => ({
	onBack() {dispatch({type: 'PREVIOUS_PAGE'})},
	onNext() {dispatch({type: 'NEXT_PAGE'})},
	openPage(index) {dispatch({type: 'OPEN_PAGE', index})},
	setPage(lens,value) {dispatch({type: 'SET_PAGES', lens, value})},
}))(quizapp);

var page = (page, lenser, setPage) => {
	return h('div', [
		page.help && h('div', {
			key: 1,
			style: {
				color: 'orange',
			},
		}, page.help),
		page.questions.map(
			(q,key) => question(
				q,
				chain(lenser(lenses.pages.question(key))),
				setPage
			)
		),
		//100*page.questions.filter(q => q.value).length/page.questions.length,
		//'% completed',
	]);
};

var question = (question, lenser, setPage) => {
	if (question.type === 'question-FR') {
		if (question.scored) {
			return h('span', {key:question.key}, [question.value, ' (', question.answer, ')']);
		}
		return h(MaterialUI.TextField, {
			key: question.key,
			hintText: question.label,
			floatingLabelText: question.floating,
			value: question.value,
			onChange: (e,value) => setPage(lenser(R.lensProp('value')), value),
		});
	}
	return h('span', {key:question.key}, "INVALID QUESTION");
};

var default_page = props => h('div', {}, 'SORRY NO STATE ('+(props.pages.length)+' other pages though)');
