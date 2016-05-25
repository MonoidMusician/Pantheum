var h = require('react-hyperscript');
var MaterialUI = require('material-ui');

var Icon = require('./Icon');

var createClass = require('../createClass');
var $dom = require('../jquery-dom');

var Input = createClass({
	displayName: 'view.Input',
	componentDidMount: function() {
		var input = $dom(this);
		if (this.props.autoSize)
			input.autosizeInput(this.props.autoSize||{space:30});
		if (this.props.autoFocus)
			input[0].focus(); // actually redundant...
		if (this.props.autoSelect)
			input[0].select();
	},
	handleChange: function(event) {
		if (this.props.onChange)
			this.props.onChange(event);
		var {target: {value}} = event;
		if (this.props.onNewValue)
			this.props.onNewValue(value);
	},
	render: function() {
		return h('input', Object.assign({}, this.props, {onChange: this.handleChange}));
	}
});


module.exports = createClass({
	displayName: 'view.EditableText',
	getInitialState: function() {
		return {
			initial: this.props.value,
			value: this.props.value,
			editing: false,
			focused: false,
			wasfocused: false,
		};
	},
	toggle: function() {
		this.setState({editing: !this.state.editing});
	},
	edit: function() {
		this.setState({editing: true, wasfocused: this.state.focused||true});
	},
	set: function(value) {
		this.setState({value});
	},
	done: function() {
		if (!this.state.value) return;
		this.setState({editing: false, initial: this.state.value});
		this.props.onNewValue(this.state.value);
	},
	cancel: function() {
		this.setState({editing: false, value: this.state.initial});
	},
	focus: function() {
		this.setState({focused: true});
	},
	blur: function() {
		this.setState({focused: false});
	},
	handleKeyUp: function({which: key}) {
		if (key === 13 || key === 32) this.done();
		else if (key === 27) this.cancel();
	},
	componentDidUpdate: function(){
		if (this.wasfocused && this._edit)
			$dom(this._edit)[0].focus();
	},
	render: function() {
		var text = this.props.display || this.state.value;
		this._edit = null;
		if (this.props.disabled) {
			var props = Object.assign({}, this.props);
			if (props.spanClassName) props.className = props.spanClassName;
			delete props.spanClassName;
			return h('span', props, text);
		}
		if (!this.state.editing) {
			var props = Object.assign({}, this.props);
			if (props.spanClassName) props.className = props.spanClassName;
			delete props.spanClassName;
			props.onClick = this.edit;
			return h('span', props, [
				text,
				Icon.h.small.edit({
					ref: r => this._edit = r,
				})
			]);
		} else {
			var props = Object.assign({}, this.props);
			if (props.inputClassName) props.className = props.inputClassName;
			delete props.inputClassName;
			props.autoFocus = props.autoSelect = props.autoSize = true;
			props.value = this.state.value;
			props.onBlur = this.cancel;
			props.onNewValue = this.set;
			props.onKeyUp = this.handleKeyUp;
			return Input.h(props);
		}
	}
});
