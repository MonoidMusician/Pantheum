function jWord() {
	this.id2vals = {};
	this.dependencies = {};
	this.entries = {sorted:[],changed:{},max_length:0};
	this.api_path = '/PHP5/dictionary/';
	this.title = "Pantheum";
	// "Register" dependencies of one path part on another, e.g.
	// "supine-1" only shows when mood="supine" is selected.
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
			var lastkey = null;
			var ch = false;
			var first = true;
			var perkey = function(key, children) {
				$.each(children, function(key, values) {
					var child = $('#word'+id+'-'+key);
					if (ch) child.show(); else child.hide();
					$.each(values, function(_, val) {
						var child = $('#word'+id+'-div-'+val[0]);
						if (ch && val[1]) child.show(); else child.hide();
					});
				});
			};
			var callback = function(target) {
				var key = target.prop('id');
				key = key.replace('word'+id+'-div-', '');
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
	// Helper, get a checkbox's value
	function getcheckbox(name) {
		var ret=[];
		$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
			ret.push($(this).val());
		});
		return ret.join();
	}
	// Helper, get the selected radio button's value
	function getradio(name) {
		return $('input:radio[name="'+name+'"]:checked:visible').val();
	}
	// Helper: "normalize" list formatting
	function normlist(val) {
		return val ? val.trim().replace(/(?:,\s*)+(?=,|$)|^(?:,\s*)+/g, "") : "";
	}
	// Helper: return select2 as CSV
	function getselect2(sel) {
		return sel.val() && sel.val().join(',');
	}
	// Get the current URL to go to when user clicks "search"
	this.searcher = function() {
		var r, h;
		if (r = $('#enter-ids').val().trim()) {
			return "id=" + encodeURIComponent(r);
		} else
		var h = {
			"name":normlist($('#enter-names').val()),
			"form":normlist($('#enter-forms').val()),
			"attr":normlist($('#enter-attrs').val()),
			"lang":getselect2($('#enter-langs')),
			"spart":getselect2($("#enter-sparts")),
			"def":$("#enter-defs").val(),
			"no_inflections":!!getcheckbox("no-inflections"),
			"no_definitions":!!getcheckbox("no-definitions"),
			"show_templates":!!getcheckbox("show-templates"),
			"start":$('#start-at').val(),
			"limit":$('#limit').val(),
		};
		for (var p in h) {
			if (!h[p]) delete h[p];
		}
		return $.param(h);
	};
	this.gettitle = function() {
		var t = this.title;
		if (this.entries.sorted.length) {
			var l = $(this.entries[this.entries.sorted[0]]).text();
			if (this.entries.sorted.length == 1)
				return l+" | "+t;
			var r = $(this.entries[this.entries.sorted[this.entries.sorted.length-1]]).text();
			return l+" … "+r+" | "+t;
		}
		return "Dictionary | "+t;
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
		$.get(this.api_path+'get-path.php',
		      'path='+this.path(id)+
		      '&id='+id)
		.done(callback);
	};
	this.word_set_val = function(id) {
		var my = this;
		var path = this.path(id);
		$.get(this.api_path+'set-path.php',
		      'path='+path+
		      '&id='+id+
		      '&val='+this.word_user_val(id))
		.done(function(data){
			if (data == "success") return path ? my.refreshInflection(id) : my.refreshEntry(id);
			errorTip("Could not set path: "+data,6900);
		})
		.fail(function(data) {
			errorTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};
	this.word_del_val = function(id) {
		var my = this;
		var path = this.path(id);
		$.get(this.api_path+'delete-path.php',
		      'path='+path+
		      '&id='+id)
		.done(function(data){
			if (data == "success") return path ? my.refreshInflection(id) : my.refreshEntry(id);
			errorTip("Could not delete path: "+data,6900);
		})
		.fail(function(data) {
			errorTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};

	this.word_add_pron = function(id) {
		if (!this.word_user_pron(id)) return;
		messageTip("Trying to add pronunciation...");
		var my = this;
		var path = this.path(id);
		$.get(this.api_path+'add-pronunciation.php',
		      'path='+path+
		      '&id='+id+
		      '&val='+this.word_user_pron(id))
		.done(function(data){
			if (data == "success") {
				successTip("Successfully added pronunciation");
				return path ? my.refreshInflection(id) : my.refreshEntry(id);
			}
			errorTip("Could not add pronunciation: "+data,6900);
		});
	};
	this.word_add_def = function(id) {
		var def = this.word_user_def(id,false).trim();
		if (!def) return;
		var extra = "";
		if (def.startsWith("{:expr:}")) {
			extra += "&type=expr";
			def = def.substr(8).trim();
		}
		var re = /^\[([a-z]{1,3})\]\s*(.*?)\s*$/;
		var matched = re.exec(def);
		if (matched !== null) {
			extra = "&lang="+matched[1];
			def = matched[2];
		}
		if (!def) {
			return;
		}
		def = encodeURIComponent(def);
		messageTip("Trying to add definition...");
		var my = this;
		$.get(this.api_path+'add-definition.php',
		      'path='+this.path(id)+
		      '&id='+id+
		      '&val='+def+
		      extra)
		.done(function(data){
			if (data == "success") {
				successTip("Successfully added definition");
				return my.refreshDefinitions(id);
			}
			errorTip("Could not add definition: "+data,6900);
		});
	};
	this.word_add_attr = function(id) {
		var attr = encodeURIComponent($('#word'+id+'_value_attr').val());
		messageTip("Trying to add attribute(s)...");
		var my = this;
		$.get(this.api_path+'add-attributes.php',
		      'attr='+attr+'&id='+id)
		.done(function(data){
			if (data == "success") {
				successTip("Successfully added attribute(s)");
				return my.refreshEntry(id);
			}
			errorTip("Could not add attribute: "+data,6900);
		});
	};
	this.definition_delete = function(id,word_id) {
		messageTip("Trying to delete definition...");
		var my = this;
		$.get(this.api_path+'delete-definition.php','id='+id)
		.done(function(data){
			if (data == "success") {
				successTip("Successfully deleted definition");
				return my.refreshDefinitions(word_id);
			}
			errorTip("Could not delete definition: "+data,6900);
		});
	};
	this.word_add_connect = function(from_id, to_id, type) {
		messageTip("Trying to add connection...");
		var my = this;
		var to_id = $('#word'+from_id+'_connection_to').val();
		var type = $('#word'+from_id+'_connection_type').val();
		var mutual = $('#word'+from_id+'_connection_ismutual:checked').length;
		$.get(this.api_path+'add-connection.php',
		      'from='+from_id+
		      '&to='+to_id+
		      '&type='+type+
		      '&mutual='+(mutual?'true':'false'))
		.done(function(data){
			if (data == "success") {
				successTip("Successfully added connection");
				my.refreshEntry(from_id);
				if (mutual) my.refreshEntry(to_id);
				return;
			}
			errorTip("Could not add connection: "+data,6900);
		});
	};
	this.connection_delete = function(from_id, to_id, type) {
		messageTip("Trying to delete connection...");
		var my = this;
		$.get(this.api_path+'delete-connection.php',
		      'from='+from_id+
		      '&to='+to_id+
		      '&type='+type)
		.done(function(data){
			if (data == "success") {
				successTip("Successfully deleted connection");
				return my.refreshEntry(from_id);
			}
			errorTip("Could not delete connection: "+data,6900);
		});
	};
	this.word_delete = function(id) {
		var test = confirm("Are you sure you want to delete this word? This action *CANNOT* be undone.");
		if (!test)
			return;
		var my = this;
		$.get(this.api_path+'delete-word.php','id='+id)
		.done(function(data){
			if (data == "success") return my.refreshEntries();
			errorTip("Could not delete word: "+data);
		});
	};
	this.word_refresh = function(id) {
		var my = this;
		if ($.jStorage) {
			$.jStorage.set("word"+id+"_changed","");
			$.jStorage.set("word"+id,"");
		}
		$.get(this.api_path+'clear-cache.php','id='+id)
		.done(function(data){
			if (data == "success") return my.getWord(id);
			errorTip("Could not refresh word: "+data,2300);
		});
	};
	this.word_rename = function(id, old_name) {
		var my = this;
		//var old_name = $('#word'+id+'_name').text();
		$('#word'+id+'_name').replaceWith(
			'<input style="width: 100px;" id="word'+id+'_name" type="text"'+
			' placeholder="name" value="'+old_name+
			'" required>'
		);
		var show_name = function(new_name) {
			$('#word'+id+'_name').replaceWith(
				'<span class="word-name" id="word'+id+'_name">'+new_name+'</span>'
			);
			return my.refreshEntry(id);
			$('#word'+id+'_rename').on("click.rename", function() {
				dict.word_rename(id);
			});
		}
		var rename = function(new_name) {
			if (new_name == old_name || !new_name) {
				return show_name(old_name);
			}
			$.get(my.api_path+'rename-word.php','id='+id+'&newname='+new_name)
			.done(function(data){
				if (data == "success") {
					return show_name(new_name);
				}
				errorTip("Could not rename word: "+data);
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
	this.word_change_POS = function(id, pos) {
		var my = this;
		$.get(my.api_path+'change-pos.php','id='+id+'&newpos='+pos)
		.done(function(data){
			if (data == "success") {
				return my.refreshEntry(id);
			}
			errorTip("Could not rename word: "+data);
		});
	}

	this.word_run_templ = function(id) {
		var my = this;
		var arg = "";
		var val = $('#word'+id+'_value_templ').val().split(":");
		arg = val[0].split("?").slice(1).join("?");
		val[0] = val[0].split("?")[0];
		var re = /^(?:(?:(?:overwrite=true)|(?:ignore)=([_a-zA-Z0-9-]+[,;/]?)+|(?:change)=([_a-zA-Z0-9-]+,[_a-zA-Z0-9-]+;?)+)[&]?)*$/;
		if (!re.test(arg)) return alert("Bad template argument syntax: "+arg);
		//return alert(arg);
		val = [val[0],val.slice(1).join(":")];
		if (arg && arg.slice(-1) != "&") arg+='&';
		arg += 'template='+encodeURIComponent(val[0].trim());
		$.each(val[1].split(";"), function(i,argi) {
			arg += '&'+i+'='+encodeURIComponent(argi.replace(/\s*,\s*/g,"\n").trim());
		});
		$.get(this.api_path+'run-template.php',
		      'id='+id+
		      '&path='+this.path(id)+'&'+arg)
		.done(function(data){
			if (data == "success") return my.refreshInflection(id);
			errorTip("Could not run template on word: "+data);
		})
		.fail(function(data) {
			errorTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};

	this.init = function(qelement, gurl, surl) {
		this.qelement = qelement;
		this.gurl = gurl;
		this.surl = surl;
	};
	
	this.previewEntries = function(callback) {
		var my = this, serv = this.previewEntries_service;
		serv.pending = true;
		var found = false;
		for (k in serv.callbacks) {
			if (callback === serv.callbacks[k])
			{ found=true;break }
		}
		if (!found)
			serv.callbacks.push(callback);
		if (serv.deferRequestBy === null) {
			serv.execute(my);
		} else {
			setTimeout(function() {
				serv.execute(my);
			}, serv.deferRequestBy);
		}
	};
	this.previewEntries_service = {
		pending: false,
		deferRequestBy: 550,
		callbacks: [],
		execute: function(my) {
			if (!this.pending) return;
			this.pending = false;
			var callbacks = this.callbacks;
			this.callbacks = [];
			var loc = my.searcher();
			$.get(my.api_path+'search-json.php', loc)
			.done(function(data) {
				my.entries = JSON.parse(data);
				if (my.entries !== undefined)
					$.each(callbacks, function(k,c) {c(my.entries);})
			})
			.fail(function(data) {
				errorTip('Query failed! The server returned status '+data.status+(data.statusText?": "+data.statusText:""))
			});
		}
	};

	this.handleResponse = function(data) {
		messageTip("Response succeeded");
		$('#dictionary').empty();
		$('#dictionary').html(data);
	};

	this.getForm = function() {
		var form = {};
		$(':input:not(button)').each(function() {
			var $this = $(this), id = $this.attr('id'), name = $this.attr('name'), q = "", val;
			if ($this.is('[type=checkbox]'))
				val = $this.is(':checked');
			else
				val = $this.val();
			//console.log(this,val);
			if (id) q += '#'+id;
			if (name) q += '[name='+name+']'
			if (q in form)
				console.log("name conflict");
			else if (q) form[q] = val;
		});
		//console.log(form);
		return form;
	};
	this.resetForm = function(form) {
		$.each(form, function(q,val) {
			var $q = $(q);
			if (typeof val === "boolean") {
				if ($q.is(':checked') != val)
					$q.prop('checked', val);
			} else if ($q.val() != val) {
				$q.val(val).change();
			}
		});
	};

	this.refreshEntries = function() {
		var serv = this.previewEntries_service;
		var delay = serv.deferRequestBy;
		serv.deferRequestBy = null;
		var loc = this.searcher();
		if (loc === undefined) return errorTip("Empty loc");
		if (loc != this.last_loc) {
			var t = this.gettitle();
			document.title = t;
			history.pushState(null, t, 'dictionary.php?'+loc);
			this.last_loc = loc;
		}
		$('#'+this.qelement+'-permalink').prop('href', 'dictionary.php?'+loc);
		$('#dictionary').empty();
		this.previewEntries($.proxy(this, 'updateContent', this.getForm()));
		serv.deferRequestBy = delay;
	};

	this.updateContent = function(form, entries) {
		//alert(entries.sorted);
		document.title = this.gettitle();
		console.log(document.title);
		history.replaceState({'entries':entries,'form':form}, document.title, document.location.href);
		//alert(form);
		this.resetForm(form);
		if (!entries.sorted) return;
		var m, n = m = entries.sorted.length, my = this;
		var start = Date.now();
		var callback = function() {
			if (n-=1) return;
			var time = Date.now() - start;
			time = time/1000;
			time = Math.round(time * 100) / 100;
			if (m === 1) {
				messageTip("Done loading! Took "+time+" seconds.");
			} else {
				var time_per = Math.round(time/m * 100) / 100;
				messageTip("Done loading! Took "+time+" seconds ("+time_per+" per word, with "+m+" word(s)).");
			}
		};
		var prev = [];
		$.each(entries.sorted, function(i, id) {
			if (my.getWord(id, prev.slice(), callback, undefined, entries.changed[id])) n-=1;
			prev.push(id);
		});
		if (n) messageTip("Loading entries...", null);
	};

	this.tooltips = function(element, id) {
		var my = this, $e = $(element);
		$e.find('[data-path]:not([title])').each(function() {
			$(this).qtip({
				style:{
					classes: "qtip-light qtip-medium"
				},
				position:{
					my: "center left",
					at: "center right"
				},
				show: {
					event: 'mouseenter',
					solo: true,
				},
				hide: {
					event: 'click mouseleave',
					fixed: true,
					delay: 100,
				},
				content: {
					text: 'Loading...',
					ajax: {
						url: my.api_path + 'translate.php',
						type: 'GET',
						data: {
							id: id,
							path: $(this).attr('data-path')
						},
						success: function(data, status) {
							data = data.trim();
							var text = data[0].toUpperCase() + data.slice(1);
							this.set('content.text', text);
						},
					}
				}
			});
		});
	}

	this.getWord = function(id, prev, callback, find, changed) {
		var my = this, cached = false, data;
		var done = function(data) {
			if (!cached) $.jStorage.set("word"+id, data);
			var $data = $(data);
			pantheum.update($data);
			my.tooltips($data,id);
			var s = $('#dictionary section#word'+id);
			if (find !== undefined) {
				var ss = s.find(find);
				if (ss.length)
					s = ss;
			}
			if (s.length) {
				var d = $data.filter('section');
				if (find !== undefined) {
					var dd = d.find(find);
					if (dd.length)
						d = dd;
				}
				s.removeClass('pending');
				s.find('input').each(function() {
					var $this = $(this);
					var autocomplete = $this.autocomplete();
					if (autocomplete) autocomplete.dispose();
				});
				if (d.length) s.replaceWith(d);
				else s.replaceWith($data);
			} else {
				var i = 0, sel = [];
				// reverse iterate the list, find the last one which has been added already
				while (i<prev.length && !(sel = $('#dictionary section#word'+prev[prev.length-i-1])).length) {
					i+=1;
				}
				if (sel.length) {
					// add this word after that one
					sel.after($data);
				} else {
					// or as the first word
					$('#dictionary').prepend($data);
				}
			}
			if (!cached && callback !== undefined) callback();
		};
		if ((data = $.jStorage.get("word"+id)) && (changed && changed == $.jStorage.get("word"+id+"_changed"))) {
			cached = true;
			done(data);
			return true;
		} else {
			cached = false;
			if (changed) $.jStorage.set("word"+id+"_changed", changed);
			else {
				var s = $('#dictionary section#word'+id);
				if (find !== undefined) {
					var ss = s.find(find);
					if (ss.length)
						s = ss;
				}
				s.addClass('pending');
				$.get(my.api_path+'last-changed.php?id='+id)
				.success(function(changed) {
					$.jStorage.set("word"+id+"_changed", changed);
				})
			}
			var extra = $('[name=no-inflections]:checked').length ? '&no_inflections=true' : '';
			$.get(my.api_path+'get-entries.php', 'id='+id+extra)
			.done(done);
			return false;
		}
	};

	this.refreshInflection = function(id) {
		var my = this;
		function handle1Response(data) {
			var $html = $(data);
			$('#word'+id+'_forms').html($html.find('#word'+id+'_forms').html());
		}
		$.get(this.api_path+'get-entries.php', 'id='+id)
		.done(handle1Response)
		.fail(function(data) {
			errorTip('Query failed! The server returned status '+data.status+": "+data.statusText);
		});
	};

	this.refreshDefinitions = function(id) {
		var my = this;
		function handle1Response(data) {
			var $html = $(data);
			$('#word'+id+'_definitions').html($html.find('#word'+id+'_definitions').html());
		}
		$.get(this.api_path+'get-entries.php', 'id='+id)
		.done(handle1Response)
		.fail(function(data) {
			errorTip('Query failed! The server returned status '+data.status+": "+data.statusText);
		});
	};

	this.refreshEntry = function(id) {
		var my = this;
		function handle1Response(data) {
			var $html = $('<section>'+data+'</section>');
			var selector = $html.find('#word'+id);
			$('#word'+id).replaceWith(selector);
			$html.find('script').each(function() {
				$.globalEval($(this).text());
			});
		}
		$.get(this.api_path+'get-entries.php', 'id='+id)
		.always(function() {
			$('#word'+id).removeClass('pending');
		})
		.done(handle1Response)
		.fail(function(data) {
			errorTip('Query failed! The server returned status '+data.status+": "+data.statusText);
		});
	};
	this.refreshEntry = function(id) {
		this.getWord(id);
	};
	this.refreshInflection = function(id) {
		var my = this;
		if ($.jStorage) {
			$.jStorage.set("word"+id+"_changed","");
			$.jStorage.set("word"+id,"");
		}
		$.get(this.api_path+'clear-cache.php','id='+id)
		.done(function(data){
			if (data == "success") return my.getWord(id, undefined, undefined, "#word"+id+"_forms");
			errorTip("Could not refresh word: "+data,2300);
		});
	};
	this.addEntry = function(id, callback) {
		var my = this;
		var loc = this.searcher();
		if (loc === undefined) return;
		$('#'+this.qelement+'-permalink').prop('href', 'dictionary.php?'+loc);
		$.get(this.api_path+'add-word.php', loc)
		.done(function(data) {
			if (data == "success")
			{ my.refreshEntries(); successTip("Word successfully added"); }
			else errorTip("Word could not be created: "+data);
			if (callback !== undefined) callback();
		});
	};

	this.simple_search = function() {
		$('#enter-attrs,#enter-ids,#enter-names').val('').hide();
		$('[name=enter-spart], [name=enter-lang]').prop('checked', false).parent().parent().hide();
	};
	this.advanced_search = function() {
		$('#enter-attrs,#enter-ids,#enter-names').show();
		$('[name=enter-spart], [name=enter-lang]').parent().parent().show();
	};
	this.start_at = function(start) {
		$('#start-at').val(start);
		this.refreshEntries();
	};

	this.onpopstate = function(event) {
		if (!event.state) return;
		$('#dictionary').empty();
		console.log(event.state);
		this.updateContent(event.state.form, event.state.entries);
	};

	this.bindEvents = function() {
		this.unbindEvents();
		var t = this;
		window.addEventListener('popstate', $.proxy(this, 'onpopstate'));
	};

	this.unbindEvents = function() {
		window.removeEventListener('popstate', this.onpopstate);
	};
}
