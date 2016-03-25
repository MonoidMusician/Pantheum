(function(view) {
	"use strict";
	var createClass = function(c) {
		var r = React.createClass(c);
		r.h = h.bind(undefined, r);
		return r;
	};

	var format_abbr_del = function(abbr, desc, action) {
		return view.format_abbr(desc, abbr, view.del({action:action, key:1}));
	};
	view.del = function(props) {
		if (pantheum.user.administrator)
			return view.Icon.h.small.del(props);
	};
	view.Attribute = createClass({
		delete_API: function() {
			var {tag, value, id, onDelete} = this.props;
			$.get(pantheum.api_path+'add-attributes.php',
				  'attr=!'+tag+'&id='+id)
			.done(function(data){
				if (true||data == "success") {
					successTip("Successfully deleted "+tag+" attribute");
					if (onDelete) onDelete(tag, value);
				} else errorTip("Could not delete "+tag+" attribute: "+data,6900);
			});
		},
		_render: function() {
			var {tag, value} = this.props;
			var result;
			switch (tag) {
				case "transitive":
					switch (value) {
						case "true":  return format_abbr_del( "TR","Transitive",   this.delete_API);
						case "false": return format_abbr_del("NTR","Intransitive", this.delete_API);
					}; break;
				case "irregular":
					switch (value) {
						case "true":  return format_abbr_del( "REG","Regular",   this.delete_API);
						case "false": return format_abbr_del("NREG","Irregular", this.delete_API);
					}; break;
				case "common":
					switch (value) {
						case "true":  return format_abbr_del( "COM","Common",  this.delete_API);
						case "false": return format_abbr_del("NCOM","Uncommon", this.delete_API);
					}; break;
				case "person":
					result = {"person-1":"1st person","person-2":"2nd person","person-3":"3rd person"}; break;
				case "case":
					switch (value) {
						case "ablative":        return format_abbr_del("+ABL", "Uses the "+value, this.delete_API);
						case "accusative":      return format_abbr_del("+ACC", "Uses the "+value, this.delete_API);
						case "genitive":        return format_abbr_del("+GEN", "Uses the "+value, this.delete_API);
						case "dative":          return format_abbr_del("+DAT", "Uses the "+value, this.delete_API);
						case "dative-personal": return format_abbr_del("+DAT (of persons)", "Uses the dative for people", this.delete_API);
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
					var CLC = format_abbr_del("CLC", "Cambridge Latin Course", this.delete_API);
					if (sp.length === 1)
						return h('span', ['Stage ',value,' (',CLC,')']);
					else if (sp.length === 2)
						return h('span', ['Stages ',sp[0],' and ',sp[1],' (',CLC,')']);
					var value = sp.slice(0,sp.length-1).join(", ") + ", and" + sp[sp.length-1];
					return h('span', ['Stage ',value,' (',CLC,')']);
			}
			if (Array.isArray(result) && result.length == 2)
				result = {"true":result[0],"false":result[1]};
			if (typeof result === "object")
				result = result[value];
			if (result) return result;
			var abbrs = {
				"copulative": "COP",
			};
			var Tag = tag.charAt(0).toUpperCase() + tag.substr(1);
			if (value === "true" && abbrs[tag]) return format_abbr_del(abbrs[tag], Tag, this.delete_API);
			return (value !== null && value !== "true") ? tag+"="+value : tag;
		},
		render: function() {
			var r = this._render();
			if (typeof r === 'string')
				return h('span', [r, view.del(this.delete_API)]);
			return r;
		}
	});
	view.create_attribute = function(props) {return function(a, key) {
		if (React.isValidElement(a)) return a;
		var [tag, value] = a.split("=");
		if (!value) return a;
		return view.Attribute.h({...props, key: tag, tag, value});
	}};

	function intersperse(arr, sep) {
		if (arr.length === 0) {
			return [];
		}

		return arr.slice(1).reduce(function(xs, x, i) {
			return xs.concat([sep, x]);
		}, [arr[0]]);
	}

	view.Attributes = createClass({
		handleNewValue: function(value) {
			console.log(value);
		},
		render: function() {
			var props = {
				id: this.props.id,
				onDelete: this.props.onAttrDelete,
			};
			var spart = view.EditableText.h({
				key:0, disabled: !pantheum.user.administrator,
				value: this.props.spart,
				onNewValue: this.handleNewValue,
			});
			var attrs = [spart];
			if (!Array.isArray(this.props.attrs)) {
				$.each(this.props.attrs, function(key, value) {
					attrs.push(key+'='+value);
				})
			} else attrs.push(...this.props.attrs);
			if (pantheum.user.administrator) attrs.push(view.Icon.h.add({key:attrs.length}));
			attrs = attrs.map(view.create_attribute(props));
			return h('span', ['(', ...intersperse(attrs, '; '), ')']);
		}
	});

})(pantheum.view);
