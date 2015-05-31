autocompletions = (function(){
	var splitter = /(?:,\s*)/;
	function getcheckbox(name) {
		var ret=[];
		$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
			ret.push($(this).val());
		});
		return ret.join();
	};
	var lock = false;
	var last1 = ($('#enter-names').val()||"").split(splitter);
	var last2 = ($('#enter-attrs').val()||"").split(splitter);
	return {
		'dictionary-names': {
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-names-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				return;
				if (lock) return; lock=true;
				var el = $('#enter-names');
				if ($.inArray(selection.value, last1) === -1) {
					el.val(el.val()+", ");
				}
				last1 = el.val().split(splitter);
				el.focus();
				lock=false;
			},
			paramName: "name",
			deferRequestBy: 150,
			onSearchStart: function(query) {
				$(this).autocomplete().options.params["attr"] = $('#enter-attrs').val();
				$(this).autocomplete().options.params["lang"] = getcheckbox('enter-lang');
				$(this).autocomplete().options.params["spart"] = getcheckbox('enter-spart');
			},
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 2,
		},
		'dictionary-forms': {
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-forms-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				return;
				if (lock) return; lock=true;
				var el = $('#enter-forms');
				if ($.inArray(selection.value, last1) === -1) {
					el.val(el.val()+", ");
				}
				last1 = el.val().split(splitter);
				el.focus();
				lock=false;
			},
			paramName: "form",
			deferRequestBy: 150,
			onSearchStart: function(query) {
				$(this).autocomplete().options.params["attr"] = $('#enter-attrs').val();
				$(this).autocomplete().options.params["lang"] = getcheckbox('enter-lang');
				$(this).autocomplete().options.params["spart"] = getcheckbox('enter-spart');
			},
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 5,
		},
		'dictionary-attributes': {
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-attributes-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				//return;
				if (lock) return; lock=true;
				var el = $('#enter-attrs');
				if ($.inArray(selection.value, last2) === -1) {
					if (selection.value.indexOf("={") === -1) {
						el.val(el.val()+", ");
					} else {
						var prev = /^(.*?,?)(?:[^{,}]|\{[^{}]*\})+$/.exec(el.val())[1];
						//console.log(prev);
						var re = /\{([^,]+)\}$/;
						var matched = re.exec(selection.value);
						/*console.log(matched);
						console.log(selection);/**/
						if (matched !== null)
							el.val(prev+selection.value.split("=")[0]+"="+matched[1]);
						else el.val(prev+selection.value.split("=")[0]+"=");
					}
				}
				last2 = el.val().split(splitter);
				el.focus();
				lock=false;
			},
			paramName: "attr",
			deferRequestBy: 150,
			onSearchStart: function(query) {
				$(this).autocomplete().options.params["name"] = $('#enter-names').val();
				$(this).autocomplete().options.params["lang"] = getcheckbox('enter-lang');
				$(this).autocomplete().options.params["spart"] = getcheckbox('enter-spart');
			},
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 0,
		},
		'word-attributes': function(id,lang,spart) {
			return {
				serviceUrl: '/PHP5/dictionary/get-attributes-json.php',
				params: {
					"lang": lang,
					"spart": spart,
				},
				delimiter: splitter,
				onSelect: function(selection) {
					if (lock) return; lock=true;
					var el = $('#word'+id+'_value_attr');
					if ($.inArray(selection.value, last2) === -1) {
						if (selection.value.indexOf("={") === -1) {
							el.val(el.val()+", ");
						} else {
							var prev = /^(.*?,?)(?:[^{,}]|\{[^{}]*\})+$/.exec(el.val())[1];
							var re = /\{([^,]+)\}$/;
							var matched = re.exec(selection.value);
							if (matched !== null)
								el.val(prev+selection.value.split("=")[0]+"="+matched[1]);
							else el.val(prev+selection.value.split("=")[0]+"=");
						}
					}
					el.focus();
					lock=false;
				},
				paramName: "attr",
				deferRequestBy: 150,
				transformResult: function(response) {
					response = JSON.parse(response);
					return {suggestions: response};
				},
				minChars: 0,
			}
		},
		'word-templates': function(id,names) {
			
		},
	};
})();
