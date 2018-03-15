var h = require('react-hyperscript');
var MaterialUI = require('material-ui');
MaterialUI.svgicons = require('material-ui/svg-icons');

var App = require('../../app');

var createClass = require('../../createClass');

var LoginForm = createClass({
	getInitialState: function() {
		return {
			username: '',
			userValid: undefined,
			userTimeout: undefined,
			password: '',
			loading: false,
		};
	},
	handleUsername: function(event, value) {
		if (this.state.userTimeout)
			clearTimeout(this.state.userTimeout);
		if (!value) {
			this.setState({userValid: undefined, userTimeout: undefined});
			return;
		}
		this.setState({
			userValid: null,
			userTimeout: value === 'pause' ? undefined : setTimeout(() => {
				this.setState({userValid: value === 'hello', userTimeout: undefined});
			}, 2000)
		});
	},
	render() {
		var validation;
		if (this.state.userValid === null)
			validation = h(MaterialUI.CircularProgress, {
				size:0.3,
				style: {
					top: 25,
				},
			});
		if (this.state.userValid === true)
			validation = h(MaterialUI.svgicons.NavigationCheck, {
				color: 'green',
				style: {
					position: 'relative',
					top: 12.5,
					left: 12.5,
				},
			});
		if (this.state.userValid === false)
			validation = h(MaterialUI.svgicons.ContentClear, {
				color: 'red',
				style: {
					position: 'relative',
					top: 12.5,
					left: 12.5,
				},
			});
		var children = [
			h('div', {
				style: {
					//display: 'inline-div',
				}
			}, [
				h(MaterialUI.TextField, {
					hintText: "Username",
					onChange: this.handleUsername,
					style: {
						marginTop: 25,
					},
				}),
				validation,
				h('br'),
				h(MaterialUI.TextField, {
					hintText: "Password",
					type: 'password',
				}), h('br'),
				h(MaterialUI.RaisedButton, {
					label: "Log in",
					primary: true,
					onTouchTap: this.handleLogin,
					disabled: this.state.loading,
				}),
			])
		];
		var makeentry = word => Entry.h({
			word,
			onRefresh: () => {
				this.setState({words: []});
				this.handleSearch();
			},
			onDelete: () => {
				this.setState({
					words: this.state.words.filter(w => w !== word),
					snackbar: {
						message: "Word deleted",
					},
				});
				return true;
			},
		});
		if (this.state.loading) children.push(h(MaterialUI.LinearProgress));
		children.push(h(MaterialUI.Snackbar, Object.assign({
			open: !!this.state.snackbar,
			onRequestClose: (e) => this.setState({snackbar: null}),
			autoHideDuration: 3000,
		}, this.state.snackbar || {message: ''})));
		return h('div#form', children);
	},
});

module.exports = {
	title: 'Log in | Pantheum',
	data(get, form) {
	},
	render() {
		return App.h({page:'login'}, [
			h('h1', 'Log in'),
			LoginForm.h()
		]);
	}
};
