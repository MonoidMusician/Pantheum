var attributes = {};

module.exports = attributes;

attributes.abbreviation = function(tag, value) {
	var result;
	switch (tag) {
		case "transitive":
			switch (value) {
				case "true":  return [ "TR","Transitive"];
				case "false": return ["NTR","Intransitive"];
			}; break;
		case "irregular":
			switch (value) {
				case "true":  return [ "REG","Regular"];
				case "false": return ["NREG","Irregular"];
			}; break;
		case "common":
			switch (value) {
				case "true":  return [ "COM","Common"];
				case "false": return ["NCOM","Uncommon"];
			}; break;
		case "person":
			result = {"person-1":"1st person","person-2":"2nd person","person-3":"3rd person"}; break;
		case "case":
			switch (value) {
				case "ablative":        return ["+ABL", "Uses the "+value];
				case "accusative":      return ["+ACC", "Uses the "+value];
				case "genitive":        return ["+GEN", "Uses the "+value];
				case "dative":          return ["+DAT", "Uses the "+value];
				case "dative-personal": return ["+DAT (of persons)", "Uses the dative for people"];
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
			var CLC = ["CLC", "Cambridge Latin Course"];
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
	if (value === "true" && abbrs[tag]) return [abbrs[tag], Tag];
};
