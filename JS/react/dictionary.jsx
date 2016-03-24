Plugins.AutosizeInput.getDefaultOptions().space = 30;

(function(view) {
	"use strict";
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
					at: "center left",
					my: "center right",
					adjust: {y:5},
				},
				show: {
					delay: 1000,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		}
	});
	view.WordName = React.createClass({
		handleNewValue: function(name) {
			console.log(name);
		},
		render: function() {
			var classes = ["word-name"];
			if (this.props.lang)
				classes.push("format-word-"+this.props.lang);
			return <view.EditableText disabled={!pantheum.user.administrator} spanClassName={classes.join(" ")} onNewValue={this.handleNewValue} value={this.props.name} display={this.props.entry}/>
		}
	});
	view.Definitions = React.createClass({
		render: function() {
			var edit;
			if (pantheum.user.administrator)
				edit = <view.Icon type="del"/>;
			var definitions = this.props.definitions.map(function(def, i) {
				return <li key={i}><view.Language lang="en"/>{def}{edit}</li>
			});
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
	view.Wiktionary = React.createClass({
		render: function() {
			// TODO: slugify (transform æ, œ, macrons....)
			return <a href={"http://en.wiktionary.org/wiki/"+this.props.name+"#"+languages[this.props.lang]} target="_blank">{this.props.text||"Wiktionary"}</a>;
		}
	});
	view.LewisShort = React.createClass({
		render: function() {
			if (this.props.lang != 'la') return <span/>;
			// TODO: slugify (transform æ, œ, macrons....)
			return <span> – <a href={"http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry="+this.props.name} target="_blank">{this.props.text||"Lewis & Short"}</a></span>;
		}
	});
	view.PronunciationTool = React.createClass({
		transform: la_ipa.transforms["IPA transcription"],
		getInitialState: function() {
			return {value: ""};
		},
		handleChange: function(event) {
			this.setState({value: event.target.value});
		},
		render: function() {
			return <span>
				<input onChange={this.handleChange}/>
				<span>{this.transform(this.state.value)}</span>
			</span>
		}
	});
	view.Entry = React.createClass({
		getInitialState: function() {
			return {toolsOpen: false};
		},
		toggleTools: function() {
			this.setState({toolsOpen: !this.state.toolsOpen});
		},
		render: function() {
			var tools;
			if (!this.state.toolsOpen) {
				tools = <view.Icon action={this.toggleTools} type="tools"/>
			} else {
				var k = 0;
				tools = [
					<view.Icon key={k++} action={this.toggleTools} className="hider" type="tools"/>,
					<view.Icon key={k++} type="hardlink" link={"dictionary.php?id="+this.props.id}/>,
					<view.Icon key={k++} type="refresh"/>,
					<view.Icon key={k++} type="del"/>,
					<div key={k++} style={{"paddingLeft":"2em"}}>
						<view.Wiktionary {...this.props}/>
						<view.LewisShort {...this.props}/>
						<br/>
						Pronunciation: <view.PronunciationTool/>
					</div>
				];
			}
			return <section id={this.id}>
				<hr/>
				<view.EntryName {...this.props}/>
				{" "}
				<view.Attributes {...this.props}/>
				{tools}
				<view.Definitions definitions={this.props.definitions}/>
			</section>;
		}
	});
	var word = {
		id: 10176,
		lang: "la",
		name: "sum",
		entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
		spart: "verb",
		attrs: {
			common:true,
			copulative:true,
			irregular:true,
			transitive:false
		},
		definitions: ["be, exist"],
		forms: {},
	};
	word.onAttrDelete = function(tag, value) {
		delete word.attrs[tag];
		view.render();
	};
	view.render = function() {
		ReactDOM.render(
			<view.Entry {...word}/>,
			document.getElementById('dictionary')
		);
	};
})(pantheum.view);
