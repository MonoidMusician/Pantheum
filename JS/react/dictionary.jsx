// Ensure pantheum exists...
if (!pantheum) window.pantheum = {_private:{}};
if (!pantheum.view) pantheum.view = {};
(function() {
	"use strict";
	var view = pantheum.view;
	var administrator = true;
	var languages = {
		"la": "Latin",
		"en": "English",
	};
	view.Language = React.createClass({
		render: function() {
			var title = this.props.name || languages[this.props.lang];
			return (<sup title={title}>[{this.props.lang}]</sup>);
		},
		componentDidMount: function() {
			$(ReactDOM.findDOMNode(this)).qtip({
				style: {
					classes: "qtip-light qtip-abbr"
				},
				position: {
					at: "top center",
					my: "bottom center",
					adjust: {y:5},
				},
				show: {
					delay: 200,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		}
	});
	view.WordName = React.createClass({
		getInitialState: function() {
			return {editing: false, name: this.props.name};
		},
		handleClick: function(event) {
			this.setState({editing: !this.state.editing});
		},
		handleChange: function(event) {
			this.setState({name: event.target.value});
		},
		handleKeyUp: function(event) {
			var key = event.which;
			if (key === 13) this.handleBlur();
		},
		handleBlur: function(event) {
			this.setState({editing: false});
			var props = Object.assign({}, this.props, {name:this.state.name});
		},
		render: function() {
			var classes = ["word-name"];
			if (this.props.lang)
				classes.push("format-word-"+this.props.lang);
			if (!this.state.editing)
				return <span className={classes.join(" ")} onClick={this.handleClick}>{this.state.name}</span>
			return <input value={this.state.name} onBlur={this.handleBlur} onKeyUp={this.handleKeyUp} onChange={this.handleChange}/>
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
			};
			glyph = glyph[this.props.type];
			var classes = ["oi", "inline"];
			return <a href={this.props.link||"javascript:void(0)"} className={classes.join(" ")} title={this.props.desc} data-glyph={glyph} id={this.props.id}></a>
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
	view.Definitions = React.createClass({
		render: function() {
			var edit;
			if (administrator)
				edit = <view.Icon type="del"/>;
			console.log(this.props);
			var definitions = this.props.definitions.map(function(def, i) {
				return <li key={i}><view.Language lang="en"/>{def}{" "}{edit}</li>
			});
			console.log(definitions);
			return <ol>{definitions}</ol>
		}
	});
	view.EntryName = React.createClass({
		render: function() {
			return <span>
				<view.Language lang={this.props.lang}/>
				<view.WordName {...this.props}/>
			</span>;
		}
	});
	view.Entry = React.createClass({
		render: function() {
			return <section id={this.id}>
				<hr/>
				<view.EntryName {...this.props}/>
				{" "}
				<view.Attributes {...this.props}/>
				<view.Definitions definitions={this.props.definitions}/>
			</section>;
		}
	});
	view.render = function() {
		ReactDOM.render(
			<view.Entry id="10176" lang="la" name="sum, esse, fui" spart="verb" attrs={["common=true","copulative=true","irregular=true","transitive=false"]} definitions={["be, exist"]}/>,
			document.getElementById('dictionary')
		);
	};
})();
