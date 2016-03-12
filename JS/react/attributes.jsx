// Ensure pantheum exists...
if (!pantheum) window.pantheum = {_private:{}};
if (!pantheum.view) pantheum.view = {};
(function() {
	"use strict";
	var view = pantheum.view;

	view.Abbreviation = React.createClass({
		render: function() {
			return <abbr title={this.props.title}>{this.props.text}</abbr>
		}
	});
	var format_abbr = view.format_abbr = function(abbr, desc) {
		return <view.Abbreviation title={desc} text={abbr}/>
	};
	view.Attribute = React.createClass({
		_render: function() {
			var tag = this.props.tag, value = this.props.value;
			var result;
			switch (tag) {
				case "transitive":
					switch (value) {
						case "true":  return format_abbr( "TR","Transitive");
						case "false": return format_abbr("NTR","Intransitive");
					}; break;
				case "irregular":
					result = ["irregular", "regular"]; break;
				case "person":
					result = {"person-1":"1st person","person-2":"2nd person","person-3":"3rd person"}; break;
				case "case":
					switch (value) {
						case "ablative":        return format_abbr("+ABL", "Uses the "+value);
						case "accusative":      return format_abbr("+ACC", "Uses the "+value);
						case "dative":          return format_abbr("+DAT", "Uses the "+value);
						case "dative-personal": return format_abbr("+DAT (of persons)", "Uses the dative for people");
						case "genitive":        return format_abbr("+GEN", "Uses the "+value);
					}; break;
				case "declension":
					result = {
						"decl-1":"1st Declension",
						"decl-2":"2nd Declension",
						"decl-3":"3rd Declension",
						"decl-4":"4th Declension",
						"decl-5":"5th Declension",
						"decl-3-i":"3rd Declension i-stem",
						"decl-2-neuter":"2nd Declension Neuter",
						"decl-3-neuter":"3rd Declension Neuter",
						"decl-3-i-neuter":"3rd Declension Neuter i-stem",
						"decl-4-neuter":"4th Declension Neuter",
						"decl-2-4":"2nd/4th Declension",
						"adjective-12":"1st/2nd Declension",
						"adjective-3-3":"3rd Declension",
					 }; break;
				case "conjugation":
					result = {
						"conj-1":"1st Conjugation",
						"conj-2":"2nd Conjugation",
						"conj-3":"3rd Conjugation",
						"conj-3-io":"3rd Conjugation i-stem",
						"conj-4":"4th Conjugation",
						"conj-1-deponent":"1st Conjugation Deponent",
						"conj-2-deponent":"2nd Conjugation Deponent",
						"conj-3-deponent":"3rd Conjugation Deponent",
						"conj-3-io-deponent":"3rd Conjugation Deponent i-stem",
						"conj-4-deponent":"4th Conjugation Deponent",
					}; break;
				case "clc-stage":
					var sp = value.split("+");
					var CLC = format_abbr("CLC", "Cambridge Latin Course");
					if (sp.length === 1)
						return <span>Stage {value} ({CLC})</span>;
					else if (sp.length === 2)
						return <span>Stages {sp[0]} and {sp[1]} ({CLC})</span>;
					var value = sp.slice(0,sp.length-1).join(", ") + ", and" + sp[sp.length-1];
					return <span>Stages {value} ({CLC})</span>;
			}
			if (Array.isArray(result) && result.length == 2)
				result = {"true":result[0],"false":result[1]};
			if (typeof result === "object")
				result = result[value];
			if (result) return result;
			var abbrs = {
				"copulative": "COP",
			};
			if (value === "true" && abbrs[tag]) return format_abbr(abbrs[tag], tag);
			return (value !== null && value !== "true") ? tag+"="+value : tag;
		},
		render: function() {
			var r = this._render();
			if (typeof r === 'string') return <span>{r}</span>;
			return r;
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
	view.create_attribute = function(a,i) {
		var [k, v] = a.split("=");
		if (!v) return a;
		return <view.Attribute key={i} tag={k} value={v}/>;
	};

	function intersperse(arr, sep) {
		if (arr.length === 0) {
			return [];
		}

		return arr.slice(1).reduce(function(xs, x, i) {
			return xs.concat([sep, x]);
		}, [arr[0]]);
	}

	view.Attributes = React.createClass({
		render: function() {
			var attrs = [this.props.spart].concat(this.props.attrs).map(view.create_attribute);
			return <span>({intersperse(attrs, '; ')})</span>;
		}
	});

})();
