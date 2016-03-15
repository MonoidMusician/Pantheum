if (!pantheum.view) pantheum.view = {};
(function(view) {
	"use strict";
	view.Input = React.createClass({
		componentDidMount: function() {
			var input = ReactDOM.findDOMNode(this);
			if (this.props.autoSize)
				$(input).autosizeInput();
			if (this.props.autoFocus)
				input.focus(); // actually redundant...
			if (this.props.autoSelect)
				input.select();
		},
		render: function() {
			return <input {...this.props}/>
		}
	});
	view.EditableText = React.createClass({
		getInitialState: function() {
			return {editing: false, value: this.props.value, initial: this.props.value};
		},
		handleClick: function(event) {
			this.setState({editing: !this.state.editing});
		},
		handleChange: function(event) {
			this.setState({value: event.target.value});
		},
		handleKeyUp: function(event) {
			var key = event.which;
			if (key === 13) this.done();
			else if (key === 27) this.cancel();
		},
		done: function(event) {
			if (!this.state.value) return;
			this.setState({editing: false, initial: this.state.value});
			this.props.onNewValue(this.state.value);
		},
		cancel: function(event) {
			this.setState({editing: false, value: this.state.initial});
		},
		render: function() {
			var text = this.props.display || this.state.value;
			if (this.props.disabled) {
				var props = Object.assign({}, this.props);
				if (props.spanClassName) props.className = props.spanClassName;
				return <span {...props}>{text}</span>
			}
			if (!this.state.editing) {
				var props = Object.assign({}, this.props);
				if (props.spanClassName) props.className = props.spanClassName;
				return <span {...props} onClick={this.handleClick}>{text}<view.Icon small type="edit"/></span>
			} else {
				var props = Object.assign({}, this.props);
				if (props.inputClassName) props.className = props.inputClassName;
				return <view.Input {...props} autoFocus autoSelect autoSize value={this.state.value} onBlur={this.cancel} onKeyUp={this.handleKeyUp} onChange={this.handleChange}/>
			}
		}
	});
})(pantheum.view);