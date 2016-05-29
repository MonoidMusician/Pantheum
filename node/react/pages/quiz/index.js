var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var App = require('../../app');
var createClass = require('../../createClass');

var TopicSelector = require('./TopicSelector');

var SelectCategories = createClass({
	render: function() {
		return h(MaterialUI.RadioButtonGroup, {
			name: 'quizzes',
			valueSelected: this.props.value,
			style: {
				display: 'flex',
				flexFlow: 'row wrap',
			}
		}, this.props.children.map(
			q => h(MaterialUI.RadioButton, Object.assign({
				onChange: event => this.props.onChange(event, q.value),
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
		return {selectedTopic: 'all', selectedQuiz: 'random'};
	},
	handleSelectTopic: function(event, selectedTopic) {
		this.setState({selectedTopic, selectedQuiz: 'random'});
	},
	handleSelectQuiz: function(event, selectedQuiz) {
		this.setState({selectedQuiz});
	},
	render: function() {
		return h('div', [
			h('h1', 'Quiz'),
			TopicSelector.h({
				value: this.state.selectedTopic,
				onChange: this.handleSelectTopic,
				topics: [{
					value: 'all',
					label: 'All Topics',
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
					label: 'Grammar',
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
