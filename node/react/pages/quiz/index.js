var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var App = require('../../app');
var createClass = require('../../createClass');
var fonts = require('../../style/fonts');

var Quiz = require('../../quiz');

var TopicSelector = require('./TopicSelector');

var SelectCategories = createClass({
	render: function() {
		return h(MaterialUI.RadioButtonGroup, {
			name: 'quizzes',
			valueSelected: this.props.value,
			style: {
				display: 'flex',
				flexFlow: 'row wrap',
			},
			onChange: this.props.onChange,
		}, this.props.children.map(
			q => h(MaterialUI.RadioButton, Object.assign({
				key: q.value,
				style: {
					flex: '0 0 20%',
					display: 'inline-div',
					width: '20%',
					minWidth: '16em',
				}
			}, q))
		));
	},
});

var QuizPage = createClass({
	getInitialState: function() {
		return {
			selectedTopic: 'all',
			selectedQuiz: 'random',
			step: 1,
			loading: false,
			open: true,
		};
	},
	wait: function(time=600) {
		return new Promise((resolve,reject) => setTimeout(resolve, time));
	},
	load: function(time=600) {
		this.setState({open: false});
		this.wait().then(() => this.setState({open: true}));
	},
	handleSelectTopic: function(event, selectedTopic) {
		this.setState({selectedTopic, selectedQuiz: 'random'});
	},
	handleSelectQuiz: function(event, selectedQuiz) {
		this.setState({selectedQuiz});
	},
	render: function() {
		var vertical = true;
		var steps = [
			[
				TopicSelector.h({
					value: this.state.selectedTopic,
					onChange: this.handleSelectTopic,
					topics: [{
						value: 'all',
						label: "All Topics",
						children: SelectCategories.h({
							value: this.state.selectedQuiz,
							onChange: this.handleSelectQuiz,
						}, (m=>[
							m('random','Random quiz'),
							m('sth','Something'),
							m('any','Anything'),
							m('other','Other'),
							m('rest','Rest'),
							m('full','Full'),
							m('complete','Complete'),
						])((value,label)=>({value,label}))),
					}, {
						value: 'grammar',
						label: "Grammar",
						children: SelectCategories.h({
							value: this.state.selectedQuiz,
							onChange: this.handleSelectQuiz,
						}, (m=>[
							m('random','Random (grammar)'),
							m('1','Nouns: number and case'),
							m('2','Verbs: tense and number'),
							m('3','Complementary infinitives'),
							m('4','Noun–Verb agreement'),
							m('5','Subjunctive or indicative?'),
							m('6','Subjunctive practice'),
							m('7','Subjunctive Matching'),
						])((value,label)=>({value,label}))),
					}]
				}),
				h(MaterialUI.RaisedButton, {
					label: "Quiz Me!",
					primary: true,
					onTouchTap: (e) => this.setState({step:1}),
					style: {margin:'12px 0'},
				}),
			],
			[
				Quiz.h({
					quiz:this.state.selectedQuiz,
					onFinish: e => this.setState({step:2}),
					onBack: e => this.setState({step:0}),
				}),
			],
			[
				h(MaterialUI.RaisedButton, {
					label: "Finish",
					primary: true,
					onTouchTap: (e) => this.setState({step:0}),
					style: {margin:'12px 0'},
				}),
				h(MaterialUI.FlatButton, {
					label: "Back",
					onTouchTap: (e) => this.setState({step:1}),
					style: {marginLeft:'12px'},
				}),
			]
		];
		return h('div', [
			h('h1', 'Quiz'),
			steps[this.state.step] || h(MaterialUI.Stepper, {
				activeStep: this.state.step,
				orientation: vertical ? 'vertical' : 'horizontal',
			}, [
				h(MaterialUI.Step, [
					h(MaterialUI.StepLabel, {
						style: {
							fontFamily: fonts.sansserif,
							fontSize: '16px',
						},
					}, "Select quiz"),
					...(vertical ? [h(MaterialUI.StepContent, steps[0])] : []),
				]),
				h(MaterialUI.Step, [
					h(MaterialUI.StepLabel, {
						style: {
							fontFamily: fonts.sansserif,
							fontSize: '16px',
						},
					}, "Take quiz"),
					...(vertical ? [h(MaterialUI.StepContent, steps[1])] : []),
				]),
				h(MaterialUI.Step, [
					h(MaterialUI.StepLabel, {
						style: {
							fontFamily: fonts.sansserif,
							fontSize: '16px',
						},
					}, "View results"),
					...(vertical ? [h(MaterialUI.StepContent, steps[2])] : []),
				]),
			]),
			...(vertical ? [] : [h('div', steps[this.state.step])]),
		]);
	}
});

module.exports = {
	title: 'Quiz | Pantheum',
	data() {

	},
	render(data, req) {
		return App.h({page:'quiz',req}, QuizPage.h());
	},
};
