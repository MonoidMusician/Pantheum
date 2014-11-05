function jWord() {
	this.id2vals = {};
	this.dependencies = {};
	this.register = function(id2vals, dependencies) {
		var my = this;
		$.each(id2vals, function(id, vals) {
			my.id2vals[id] = vals;
			$.each(vals, function(_, val) {
				var el = $('input[name="word'+id+'-'+val+'"]');
				el.on("change", function(event) {
					my.word_db_val(id, function(data){
						if (!data.trim()) var data = "form, ...";
						else var data = "currently “"+data.split("\n").join(", ")+"”";
						$('#word'+id+'_value').prop('placeholder', data);
					});
				});
			});
		});
		$.each(dependencies, function(id, vals) { $.each(vals, function(key, keys) {
			//alert(key,keys);
			var lastkey = null;
			var ch = false;
			var first = true;
			var perkey = function(key, children) {
				//alert(key);
				//if (!first) alert(ch+" "+children);
				//var ch = el.is(':checked') && el.is(':visible');
				//alert('checked: '+ch+' ('+el.is(':checked')+', '+el.is(':visible')+')');
				$.each(children, function(key, values) {
					var child = $('#word'+id+'-'+key);
					if (ch) child.show(); else child.hide();
					//alert(values);
					$.each(values, function(_, val) {
						var child = $('#word'+id+'-div-'+val[0]);
						if (ch && val[1]) child.show(); else child.hide();
					});
				});
			};
			var callback = function(target) {
				var key = target.prop('id');
				key = key.replace('word'+id+'-div-', '');
				//alert(key);
				if (key in keys || key === "") {
					if (lastkey !== null) {
						ch = false;
						perkey(lastkey, keys[lastkey]);
						lastkey = null;
					}
					if (key in keys) {
						ch = true;
						perkey(key, keys[key]);
						lastkey = key;
					}
				}
			};
			$.each(keys, perkey);
			first = false;
			var el = $('input[name="word'+id+'-'+key+'"]');
			el.on("change", function(event) {
				//alert($(event.target).prop('id'));
				callback($(event.target));
			});
			if (el.filter(":checked").length !== 0) {
				callback(el.filter(":checked"));
			}
		});});
	};
	function getcheckbox(name) {
		var ret=[];
		$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
			ret.push($(this).val());
		});
		return ret.join();
	}
	function getradio(name) {
		return $('input:radio[name="'+name+'"]:checked:visible').val();
	}
	this.searcher = function() {
		var loc = "", op = "", r;
		if ($('#enter-ids').val().trim()) {
			return "id=" + encodeURIComponent($('#enter-ids').val());
		}
		if ($('#enter-names').val()) {
			loc += op + "name=" + encodeURIComponent($('#enter-names').val());
			op = "&";
		}
		if ($('#enter-attrs').val()) {
			loc += op + "attr=" + encodeURIComponent($('#enter-attrs').val());
			op = "&";
		}
		r = getcheckbox("enter-lang");
		if (r) {
			loc += op + "lang=" + encodeURIComponent(r);
			op = "&";
		}
		r = getcheckbox("enter-spart");
		if (r) {
			loc += op + "spart=" + encodeURIComponent(r);
			op = "&";
		}
		r = getcheckbox("to-hide-inflection");
		if (r) {
			loc += op + "no_inflection=" + true;
			op = "&";
		}
		return loc;
	};
	this.path = function(id, as_uri) {
		if (as_uri === undefined) as_uri = true;
		var path = "", sep = "";
		this.id2vals[id].forEach(function(key) {
			var r = getradio('word'+id+'-'+key);
			if (!r) return;
			path += sep + r;
			sep = "/";
		});
		return as_uri ? encodeURIComponent(path) : path;
	};
	this.word_user_val = function(id, as_uri) {
		if (as_uri === undefined) as_uri = true;
		var val =  $('#word'+id+'_value').val();
		lits = val.split(",");
		for (var i in lits)
			lits[i] = lits[i].trim();
		val = lits.join("\n");
		return as_uri ? encodeURIComponent(val) : val;
	};
	this.word_user_def = function(id, as_uri) {
		if (as_uri === undefined) as_uri = true;
		var val = $('#word'+id+'_value_def').val();
		lits = val.split(",");
		for (var i in lits)
			lits[i] = lits[i].trim();
		val = lits.join("\n");
		return as_uri ? encodeURIComponent(val) : val;
	};
	this.word_user_pron = function(id, as_uri) {
		if (as_uri === undefined) as_uri = true;
		var val = $('#word'+id+'_value_pron').val();
		lits = val.split(",");
		for (var i in lits)
			lits[i] = lits[i].trim();
		val = lits.join("\n");
		return as_uri ? encodeURIComponent(val) : val;
	};
	this.word_db_val = function(id, callback) {
		$.get('/latin/PHP5/dictionary/get-path.php',
		      'path='+this.path(id)+
		      '&id='+id)
		.done(callback);
	};
	this.word_set_val = function(id) {
		var my = this;
		var path = this.path(id);
		$.get('/latin/PHP5/dictionary/set-path.php',
		      'path='+path+
		      '&id='+id+
		      '&val='+this.word_user_val(id))
		.done(function(data){
			if (data == "success") return path ? my.refreshInflection(id) : my.refreshEntry(id);
			alert("Could not set path: "+data);
		})
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};
	this.word_del_val = function(id) {
		var my = this;
		var path = this.path(id);
		$.get('/latin/PHP5/dictionary/delete-path.php',
		      'path='+path+
		      '&id='+id)
		.done(function(data){
			if (data == "success") return path ? my.refreshInflection(id) : my.refreshEntry(id);
			alert("Could not delete path: "+data);
		})
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};

	this.word_add_pron = function(id) {
		if (!this.word_user_pron(id)) return;
		messageTip("Trying to add pronunciation...");
		var my = this;
		var path = this.path(id);
		$.get('/latin/PHP5/dictionary/add-pronunciation.php',
		      'path='+path+
		      '&id='+id+
		      '&val='+this.word_user_pron(id))
		.done(function(data){
			if (data == "success") {
				messageTip("Successfully added pronunciation");
				return path ? my.refreshInflection(id) : my.refreshEntry(id);
			}
			alert("Could not add pronunciation: "+data);
		});
	};
	this.word_add_def = function(id) {
		if (!this.word_user_def(id)) return;
		messageTip("Trying to add definition...");
		var my = this;
		$.get('/latin/PHP5/dictionary/add-definition.php',
		      'path='+this.path(id)+
		      '&id='+id+
		      '&val='+this.word_user_def(id))
		.done(function(data){
			if (data == "success") {
				messageTip("Successfully added definition");
				return my.refreshDefinitions(id);
			}
			alert("Could not add definition: "+data);
		});
	};
	this.word_add_attr = function(id) {
		var attr = encodeURIComponent($('#word'+id+'_value_attr').val());
		messageTip("Trying to add attribute(s)...");
		var my = this;
		$.get('/latin/PHP5/dictionary/add-attributes.php',
		      'attr='+attr+'&id='+id)
		.done(function(data){
			if (data == "success") {
				messageTip("Successfully added attribute(s)");
				return my.refreshEntry(id);
			}
			alert("Could not add definition: "+data);
		});
	};
	this.definition_delete = function(id,word_id) {
		messageTip("Trying to delete definition...");
		var my = this;
		$.get('/latin/PHP5/dictionary/delete-definition.php','id='+id)
		.done(function(data){
			if (data == "success") {
				messageTip("Successfully deleted definition");
				return my.refreshDefinitions(word_id);
			}
			alert("Could not delete definition: "+data);
		});
	};
	this.word_add_connect = function(from_id, to_id, type) {
		messageTip("Trying to add connection...");
		var my = this;
		var to_id = $('#word'+from_id+'_connection_to').val();
		var type = $('#word'+from_id+'_connection_type').val();
		var mutual = $('#word'+from_id+'_connection_ismutual:checked').length;
		$.get('/latin/PHP5/dictionary/add-connection.php',
		      'from='+from_id+
		      '&to='+to_id+
		      '&type='+type+
		      '&mutual='+(mutual?'true':'false'))
		.done(function(data){
			if (data == "success") {
				messageTip("Successfully added connection");
				my.refreshEntry(from_id);
				if (mutual) my.refreshEntry(to_id);
				return;
			}
			alert("Could not add connection: "+data);
		})
		.fail(function(){alert("failed")});
	};
	this.connection_delete = function(from_id, to_id, type) {
		messageTip("Trying to delete connection...");
		var my = this;
		$.get('/latin/PHP5/dictionary/delete-connection.php',
		      'from='+from_id+
		      '&to='+to_id+
		      '&type='+type)
		.done(function(data){
			if (data == "success") {
				messageTip("Successfully deleted connection");
				return my.refreshEntry(from_id);
			}
			alert("Could not delete connection: "+data);
		});
	};
	this.word_delete = function(id) {
		var test = confirm("Are you sure you want to delete this word? This action *CANNOT* be undone.");
		if (!test)
			return;
		var my = this;
		$.get('/latin/PHP5/dictionary/delete-word.php','id='+id)
		.done(function(data){
			if (data == "success") return my.refreshEntries();
			alert("Could not delete word: "+data);
		});
	};
	this.word_rename = function(id) {
		var my = this;
		var old_name = $('#word'+id+'_name').text();
		$('#word'+id+'_name').replaceWith(
			'<input style="width: 100px;" id="word'+id+'_name" type="text"'+
			' placeholder="name" value="'+old_name+
			'" required>'
		);
		var show_name = function(new_name) {
			$('#word'+id+'_name').replaceWith(
				'<span class="word-name" id="word'+id+'_name">'+new_name+'</span>'
			);
			$('#word'+id+'_rename').on("click.rename", function() {
				dict.word_rename(id);
			});
		}
		var rename = function(new_name) {
			if (new_name == old_name || !new_name) {
				return show_name(old_name);
			}
			$.get('/latin/PHP5/dictionary/rename-word.php','id='+id+'&newname='+new_name)
			.done(function(data){
				if (data == "success") {
					return show_name(new_name);
				}
				alert("Could not rename word: "+data);
			});
		};
		$('#word'+id+'_rename').off("click.rename");
		$('#word'+id+'_rename').on("click.rename", function() {
			rename($('#word'+id+'_name').val());
		});
		$('#word'+id+'_name').on("keypress", function(e) {
			if (e.which === 13) {
				rename($('#word'+id+'_name').val());
			} else if (e.which === 27) {
				rename(old_name);
			}
		});
	};

	this.word_run_templ = function(id) {
		var my = this;
		var arg = "";
		var val = $('#word'+id+'_value_templ').val().split(":");
		arg = val[0].split("?").slice(1).join("?");
		val[0] = val[0].split("?")[0];
		var re = /^(?:(?:overwrite=true)|(?:ignore)=([_a-zA-Z0-9-]+[,;]?)+|(?:change-to)=([_a-zA-Z0-9-]+,[_a-zA-Z0-9-]+;?)+)*$/;
		if (!re.test(arg)) return alert("Bad template argument syntax: "+arg);
		//return alert(arg);
		val = [val[0],val.slice(1).join(":")];
		if (arg) arg+='&';
		arg += 'template='+encodeURIComponent(val[0].trim());
		$.each(val[1].split(";"), function(i,argi) {
			arg += '&'+i+'='+encodeURIComponent(argi.trim());
		});
		$.get('/latin/PHP5/dictionary/run-template.php',
		      'id='+id+
		      '&path='+this.path(id)+'&'+arg)
		.done(function(data){
			if (data == "success") return my.refreshInflection(id);
			alert("Could not run template on word: "+data);
		})
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};

	this.init = function(qelement, gurl, surl) {
		this.qelement = qelement;
		this.gurl = gurl;
		this.surl = surl;
	};
	
	this.refreshEntries = function() {
		messageTip("Loading entries...");
		var my = this;
		var loc = this.searcher();
		if (loc === undefined) return messageTip("Empty loc");
		if (loc != this.last_loc) {
			window.history.pushState(loc, "", 'dictionary2.php?'+loc);
		}
		$('#'+this.qelement+'-permalink').prop('href', 'dictionary2.php?'+loc);
		$.get('/latin/PHP5/dictionary/get-entries.php', loc)
		.done($.proxy(this, "handleResponse"))
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};
	this.refreshInflection = function(id) {
		var my = this;
		function handle1Response(data) {
			var $html = $(data);
			$('#word'+id+'_forms').html($html.find('#word'+id+'_forms').html());
		}
		$.get('/latin/PHP5/dictionary/get-entries.php', 'id='+id)
		.done(handle1Response)
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText);
		});
	};
	this.refreshDefinitions = function(id) {
		var my = this;
		function handle1Response(data) {
			var $html = $(data);
			$('#word'+id+'_definitions').html($html.find('#word'+id+'_definitions').html());
		}
		$.get('/latin/PHP5/dictionary/get-entries.php', 'id='+id)
		.done(handle1Response)
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText);
		});
	};
	this.refreshEntry = function(id) {
		var my = this;
		function handle1Response(data) {
			var $html = $('<section>'+data+'</section>');
			var selector = $html.find('#word'+id);
			$('#word'+id).html(selector.html());
		}
		$.get('/latin/PHP5/dictionary/get-entries.php', 'id='+id)
		.done(handle1Response)
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText);
		});
	};
	this.addEntry = function(id, callback) {
		var my = this;
		var loc = this.searcher();
		if (loc === undefined) return;
		$('#'+this.qelement+'-permalink').prop('href', 'dictionary2.php?'+loc);
		$.get('/latin/PHP5/dictionary/add-word.php', loc)
		.done(function(data) {
			if (data == "success")
			{ my.refreshEntries(); messageTip("Word successfully added"); }
			else alert("Word could not be created: "+data);
			if (callback !== undefined) callback();
		});
	};
	this.handleResponse = function(data) {
		messageTip("Response succeeded");
		$('#dictionary').empty();
		$('#dictionary').html(data);
	};
	
	this.bindEvents = function() {
		this.unbindEvents();
		
		/*$(document).on('click', '#' + this.qelement + '-back', $.proxy(this.handleBack, this));
		$(document).on('click', '#' + this.qelement + '-submit', $.proxy(this.handleSubmit, this));
		$(document).on('click', '#' + this.qelement + '-next', $.proxy(this.handleNext, this));*/
	};
	
	this.unbindEvents = function() {
		/*$(document).off('click', '#' + this.qelement + '-back');
		$(document).off('click', '#' + this.qelement + '-submit');
		$(document).off('click', '#' + this.qelement + '-next');
		$(document).off('click', '#' + this.qelement + '-finish');*/
	};
	
	this.start = function() {
		this.getNextQuestion();
		this.bindEvents();
	};
}
