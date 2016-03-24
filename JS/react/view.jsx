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
	view.Icon = React.createClass({
		render: function() {
			var glyph = {
				"edit": "pencil",
				"refresh": "reload",
				"hardlink": "link-intact",
				"del": "trash",
				"tools": "wrench",
				"rename": "text",
				"change POS": "compass", // FIXME
				"&lt;&lt;": "media-skip-backward",
				"&lt;": "media-step-backward",
				"&gt;": "media-step-forward",
				"&gt;&gt;": "media-skip-forward",
				"visibility": "eye",
				"add": "plus",
			};
			glyph = glyph[this.props.type];
			var classes = this.props.className || [];
			if (typeof classes === 'string') classes = classes.split(" ");
			classes.push('oi', 'inline', 'spaced');
			if (this.props.small) classes.push('small');
			return <a href={this.props.link||"javascript:void(0)"} onClick={this.props.action||this.props.onClick} className={classes.join(" ")} title={this.props.desc} data-glyph={glyph} id={this.props.id}></a>
		},
		componentDidMount: function() {
			$(ReactDOM.findDOMNode(this)).qtip({
				style: {
					classes: "qtip-light qtip-abbr"
				},
				position: {
					at: "top center",
					my: "bottom center",
					adjust: {y:0},
				},
				show: {
					delay: 800,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		}
	});
})(pantheum.view);