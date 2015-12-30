Plugins.AutosizeInput.getDefaultOptions().space = 30;

function jQuiz_escapeHTML(s) {
	return $('<div/>').text(s).html().split('"').join('&quot;');
}

function jQuiz_checkanswer(name) {
	return function(answer, callback) {
		$.post('/PHP5/quiz/checkQuestion.php', {
			name: name, answer: answer
		}).done(function(truth) {
			if (truth == 'true') truth = true;
			else if (truth == 'false') truth = false;
			else truth = 0;
			callback(truth);
		});
	}
}

dynamicAnswer = (function() {
	
	function dynamicAnswer_scale(s) {
		return s.replace(/\d+(\.\d+)?/g, function(n) {
			return (+n)*rel;
		});
	};

	var rel = 0.7;
	var w = rel < 0.5 ? 2 : 4;
	var m = ' '+(20+w/2)+' ', M = ' '+(20-w/2)+' ';
	var loading = dynamicAnswer_scale('M4'+m+'L14'+m+'L36'+m+'M4'+M+'L14'+M+'L36'+M);
	var Loading = dynamicAnswer_scale('M4'+m+'L20'+m+'L36'+m+'M4'+M+'L20'+M+'L36'+M);
	var check = dynamicAnswer_scale('M4 20 L14 32 L36 4 M4 20 L14 32 L36 4');
	var cross = dynamicAnswer_scale('M4 36 L20 20 L36 36 M4 4 L20 20 L36 4');
	var t = 1500, d = 220;
	var green = '#33CC33', red = '#CC3333', gray = '#BDBDBD';
	var dash = '4,4', ease = ['sin-out', 'sin-in'];

	var running = false;
	var loop = function() {
		if (!running) return;
		d3.selectAll('svg.status').attr('stroke-dashoffset', 0).transition().ease('linear').duration(200).attr('stroke-dashoffset', 8)
		.each("end", loop);
	};

	function dynamicAnswer(element, truth) {
		var that = this;
		var $t = $(element).filter(':first');

		var a = $('<div>').insertAfter($t).css('display','inline-block').css('vertical-align', '-7px');
		var correct = d3.select(a[0]).insert('svg').classed('status', true)
			.style('width', 40*rel+'px').attr('width', 40*rel).attr('height', 40*rel).style('display', 'inline-block')
			.append('path').attr('stroke-dasharray',dash)
				.style('stroke-width', w+'px').style('fill', 'none')
				.style('stroke', gray).attr('d', loading);

		this.element = $t;
		this.div = a;
		this.path = correct;
		// state
		//    0:     hidden
		//    null:  loading
		//    true:  correct
		//    false: incorrect
		this.state = 0;
		this.cache = {'':0};

		this.init = function() {
			if (!running) {running = true;loop();}
			return this;
		};
		this.destroy = function() {
			a.remove();
			running = false;
		};
		this.set = function(state) {
			if (state === true) this.check();
			else if (state === false) this.cross();
			else if (state === 0) this.hidden();
			else this.loading();
		};
		this.setcached = function(v,state) {
			if (v && (state === true || state === false))
				this.cache[v] = state;
			this.set(state);
		};
		this.update = function() {
			var v = $t.val();
			if (v in this.cache)
				return this.set(this.cache[v]);
			this.state = null;
			var t = truth.call(this, v, $.proxy(this, 'setcached', v));
			if (t === undefined && this.state !== null)
				return this.state;
			return t;
		};
		this.check = function() {
			if (this.state === true) return;
			this.state = true;
			this.shown();
			correct.attr('d', loading).attr('stroke-dasharray','');
			$t.css('border-color', green);
			correct.transition().duration(d).style('stroke', green).attr('d', check).ease('sin-out');
		};
		this.cross = function() {
			if (this.state === false) return;
			this.state = false;
			this.shown();
			correct.attr('d', Loading).attr('stroke-dasharray','');
			$t.css('border-color', red);
			correct.transition().duration(d).style('stroke', red).attr('d', cross).ease('sin-out');
		};
		this.loading = function() {
			this.state = null;
			var l = this.state ? loading : Loading;
			this.shown();
			$t.css('border-color', '');
			correct.transition().duration(d).ease('sin-in')
				.style('stroke', gray).attr('d', l)
				.each("end", function() {
					correct.attr('stroke-dasharray',dash)
				});
		};
		this.hidden = function() {
			this.state = 0;
			this.div.css('visibility', 'hidden');
			console.log(this.div);
		};
		this.shown = function() {
			this.div.css('visibility', '');
		};

		this.hidden();

		var timer;
		this.timeout = 450;

		$t.on('change', $.proxy(this, 'update'));
		$t.on('keyup', function() {
			var v = $t.val();
			clearTimeout(timer);
			if (v in that.cache)
				that.set(that.cache[v]);
			else {
				that.shown();
				timer = setTimeout($.proxy(that, 'update'), that.timeout);
				that.loading();
			}
		});
	}

	return dynamicAnswer;
})();

function jQuiz() {
	this.questions = [];
	this.results = [];
	this.answers = {};
	/*this.stats = [];*/
	this.score = 0;
	this.out_of = 0;
	this.active = true;
	this.scored = false; // Have we shown the user their score?
	this.nextable = false;
	this.current = 0;
	this.last = 1;
	this.qelement = 'quiz';
	this.gurl = '';
	this.surl = '';
	this.eurl = '';
	this.loading = false;

	this.init = function(qelement, gurl, surl, eurl) {
		this.qelement = qelement;
		this.loading_elements = '#' + qelement + '-next, #' + qelement + '-submit';
		this.gurl = gurl;
		this.surl = surl;
		this.eurl = eurl;
	};

	this.refocus = function($html) {
		if ($html === undefined) $html = $('#' + this.qelement);
		var n = 0;
		$html.find('.autotabindex').each(function() {
			$(this).attr('tabindex', ++n);
		});
		var m = 0;
		$html.find('[tabindex]').each(function() {
			var i = -1*-$(this).attr('tabindex');
			if (i > 0 && i > m) m = i;
		});
		$html.find('[data-tabindex]').each(function() {
			var $this = $(this),
			    t = 0-$this.attr('data-tabindex')*-1;
			$this.attr('tabindex', m + t);
			$this.removeAttr('data-tabindex');
		});
		var a = $html.find(':focus').blur();
		var b = $html.find('[tabindex=1]:first').focus();
		this.nextable = false;
	}

	this.getNextQuestion = function() {
		$.get(this.gurl, $.proxy(this.handleQuestion, this));
	};

	this.submitQuestion = function() {
		$.post(this.surl, this.answers, $.proxy(this.handleResponse, this));
	};

	this.handleQuestion = function(data) {
		this.loading = false;
		$(this.loading_elements).attr('disabled', false);
		if (!data || data[0] != '[' || data[data.length-1] != ']')
			return alert('Error: '+data);
		this.current += 1;
		this.questions[this.current] = jQuery.parseJSON(data);
		this.showQuestion();
	};

	this.handleResponse = function(data) {
		this.loading = false;
		$(this.loading_elements).attr('disabled', false);
		if (!data || data[0] != '{' || data[data.length-1] != '}')
			return alert('Error: '+data);
		var result = jQuery.parseJSON(data);
		this.results[this.current] = result;
		this.score += 0-(-result["subscore"]);
		this.out_of += 0-(-result["out_of"]);
		this.showQuestion();
	};

	this.buildHeader = function() {
		var header = '<section id="' + this.qelement + '-top">';

		if ((this.current - 1) >= 0) {
			header += '<button data-tabindex="2" id="' + this.qelement + '-back">Back</button>';
		} else {
			header += '<button id="' + this.qelement + '-back" class="disabled">Back</button>';
		}

		header += '<span> ' + this.select() + ' / ' + this.last + ' </span>';

		if (this.results[this.current] == undefined) {
			header += '<button data-tabindex="1" id="' + this.qelement + '-submit">Submit</button>';
		} else if (this.current >= this.last - 1) {
			if (!this.scored)
				header += '<button tabindex="1" id="' + this.qelement + '-next">Results</button>';
			else if (this.active)
				header += '<button tabindex="1" id="' + this.qelement + '-next">Finish</button>';
			else
				header += '<button tabindex="1" id="' + this.qelement + '-next">Return</button>';
		} else {
			header += '<button tabindex="1" id="' + this.qelement + '-next">Next</button>';
		}

		header += '</section>';

		return header;
	}

	this.select = function() {
		var ret = '<select id="' + this.qelement + '-page">';
		for (var i=0; this.questions[i] !== undefined; i++) {
			if (this.current == i)
				ret += '<option selected>';
			else ret += '<option>';
			ret += (i+1);
		}
		return ret + '</select>';
	}

	this.parsePart = function(part) {
		var result = '';

		var ncorrect = ' autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"';
		var ycorrect = ' spellcheck="true"';

		if (part[0] == 'wrap') {
			//pass
		} else if (part[0] == 'text') {
			result = '<span class="jquiz-text">' + part[1] + '</span>';
		} else if (part[0] == 'help') {
			if (this.results[this.current] != undefined) {
				var result = this.results[this.current];
				var correct = result['subscore'];
				var total = result['out_of'];
				var score = '<br>' + this.makeScore(correct, total);
			} else var score = '';
			result = '<span class="jquiz-help">' + part[1] + '</span><br>'+score+'<br>';
		} else if (part[0] == 'html') {
			result = part[1];
		} else {
			if (this.results[this.current] == undefined) {
				if (part[0] == 'input') {
					result = '<input class="autotabindex dynamic autosizeable"'+(part[4]!='en'?ncorrect:ycorrect)+' type="text" id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] /*+ '" title="' + part[3]/**/ + '">';
				} else if (part[0] == 'paragraph') {
					result = '<textarea class="autotabindex" id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] /*+ '" title="' + part[3]/**/ + '" style="font-family:Linux Libertine;"></textarea>';
				} else if (part[0] == 'select') {
					//result += '<select id="' + this.qelement + '-' + part[1] + '" title="' + part[2] + '">';
					result += '<span id="' + this.qelement + part[1] + '" class="select select-bordered">';
					var tabin = ' class="autotabindex"';
					for (var oid in part[3]) {
						var option = part[3][oid];
						result += '<label>';
						result += '<input'+tabin+' class="inputlabel" type="radio"';
						result += 'name="'+this.qelement + '-' + part[1]+'"';
						result += 'value="'+jQuiz_escapeHTML(option)+'" required>';
						result += option;
						result += '</label><br>';
						//result += '<option>' + option + '</option>';
						//var tabin = '';
					}
					result += '</span>';
					//result += '</select>';
				} else if (part[0] == 'matching-row') {
					var tabin = ' class="autotabindex"';
					for (var oid in part[3]) {
						var option = part[3][oid];
						result += '<td><label>';
						result += '<input'+tabin+' class="inputlabel" type="radio"';
						result += 'name="'+this.qelement + '-' + part[1]+'"';
						result += 'value="'+jQuiz_escapeHTML(option)+'" required>';
						result += (parseInt(oid)+1)+'.';
						result += '</label></td>';
						//result += '<option>' + option + '</option>';
						//var tabin = '';
					}
				} else if (part[0] == 'matching') {
					//result += '<select id="' + this.qelement + '-' + part[1] + '" title="' + part[2] + '">';
					result += '<div class="scrollable"><table id="' + this.qelement + part[1] + '" class="jquiz-matching"><tr><td></td>';
					for (var oid in part[4]) {
						var option = part[4][oid];
						result += '<th>';
						result += option;
						result += '</th>';
					}
					result += '</tr>';
					for (var vid in part[3]) {
						var left = part[3][vid];
						result += '<tr><th>'+left+'</th>';
						var tabin = ' class="autotabindex"';
						for (var oid in part[4]) {
							var option = part[4][oid];
							result += '<td><label>';
							result += '<input'+tabin+' class="inputlabel" type="radio"';
							result += 'name="'+this.qelement + '-' + part[1]+'-'+vid+'"';
							result += 'value="'+jQuiz_escapeHTML(option)+'" required>';
							result += (parseInt(oid)+1)+'.';
							result += '</label></td>';
							//result += '<option>' + option + '</option>';
							//var tabin = '';
						}
						result += '</tr>';
					}
					result += '</table></div>';
					//result += '</select>';
				}
			} else {
				if (this.questions[this.current][0][0] == "wrap") {
					var wrap = this.questions[this.current][0][1];
					if ([part[1]] in wrap)
						wrap = wrap[[part[1]]];
				} else var wrap = {
					'user-correct': ['<span class="jquiz-correct">','</span>'],
					'user-incorrect': ['<span class="jquiz-incorrect">','</span>'],
					'machine': ['<span class="jquiz-other">','</span>'],
				};
				var intro = "try"; var conj = "or";
				if (this.results[this.current][part[1]][0]) {
					result += wrap['user-correct'][0] + this.results[this.current][part[1]][1] + wrap['user-correct'][1];
					var intro = "also";
					var conj = "and";
				} else {
					result += wrap['user-incorrect'][0] + this.results[this.current][part[1]][1] + wrap['user-incorrect'][1];
				}
				for (var i=2;i<this.results[this.current][part[1]].length;i++) {
					if (i == 2) result += wrap['machine'][0] + ' ('+intro+': ';
					else if (i == this.results[this.current][part[1]].length-1)
						if (i == 3) result += " "+conj+" ";
						else result += ", "+conj+" ";
					else result += ", ";
					result += this.results[this.current][part[1]][i];
				}
				if (i > 2) result += ')' + wrap['machine'][1];
			}
		}

		return result;
	};

	this.buildBody = function() {
		this.tabindex = 0; // for parsePart
		var body = '<section id="' + this.qelement + '-content">';
		body += '<p class="jquiz-question">';
		for (var pid in this.questions[this.current]) {
			var part = this.questions[this.current][pid];
			body += this.parsePart(part);
		}
		body += '</p>';
		body += '</section>';

		return body;
	};

	this.makeScore = function(correct, total, intro, classes) {
		if (intro === undefined) intro = "You got ";
		if (classes === undefined) classes = "jquiz-score";
		var incorrect = total - correct;
		var percent = Math.round(correct / total * 100);
		if (correct) correct = '<span class="jquiz-correct">+' + correct + '</span> ';
		else correct = '';
		if (incorrect) incorrect = '<span class="jquiz-incorrect">-' + incorrect + '</span> ';
		else incorrect = '';
		if (percent > 90)
			var color = 'correct';
		else if (percent < 70)
			var color = 'incorrect';
		else
			var color = 'other';
		percent = ' (<span class="jquiz-'+color+'">' + percent + '%</span>)';
		total = '<span class="jquiz-other">' + total + '</span>';
		return '<span class="'+classes+'">'+intro+correct+incorrect+' / ' + total + percent + "</span>";
	}

	this.buildScore = function() {
		var body = '<section id="' + this.qelement + '-content">';
		body += '<p class="jquiz-scores">';
		body += this.makeScore(this.score, this.out_of, "Total: ", "jquiz-totalscore");
		for (rid in this.results) {
			var result = this.results[rid];
			var correct = result['subscore'];
			var total = result['out_of'];
			// FIXME: need unobtrusivity here!
			body += '<br>' + this.makeScore(correct, total, '<a href="javascript:quiz.goTo('+rid+')">Page #'+(rid-0+1)+':</a> ');
		}
		body += '</p>';
		body += '</section>';
		return body;
	};

	this.show = function(type) {
		this.scored = !!type;
		var html = this.buildHeader() + (type == 0 ? this.buildBody() : this.buildScore());
		var $html = $(html);
		$('#' + this.qelement).html($html);
		pantheum.update($html);
		$html.find('input.autosizeable').autosizeInput();
		var q = this.qelement+'-';
		$html.find('input.dynamic').each(function() {
			var a = $(this).attr('id');
			if (!a.startsWith(q)) return;
			a = a.slice(q.length);
			new dynamicAnswer(this, jQuiz_checkanswer(a)).init();
		});
		this.refocus($html);
	};

	this.showQuestion = function() {
		this.show(0);
	}
	this.showScore = function() {
		this.show(1);
	};

	this.handleSubmit = function(data) {
		if (this.loading) return;
		this.answers = {};
		for (var pid in this.questions[this.current]) {
			var part = this.questions[this.current][pid];
			if ((part[0] == 'input') || (part[0] == 'paragraph')) {
				this.answers[part[1]] = $('#' + this.qelement + '-' + part[1]).val();
			} else if ((part[0] == 'select') || (part[0] == 'matching-row')) {
				this.answers[part[1]] = $('input:radio[name='+this.qelement + '-' + part[1]+']:checked').val();
			} else if ((part[0] == 'matching')) {
				this.answers[part[1]] = [];
				for (vid in part[3]) {
					this.answers[part[1]].push($('input:radio[name='+this.qelement + '-' + part[1]+'-'+vid+']:checked').val());
				}
				this.answers[part[1]] = this.answers[part[1]].join("\n");
			} else continue;
			if (!this.answers[part[1]]) {
				alert('Please answer all questions before submitting!');
				return;
			}
		}
		this.loading = true;
		$('#' + this.qelement + '-submit').attr('disabled', true);
		this.submitQuestion();
	};

	this.handleNext = function(data) {
		if (this.loading) return;
		// Results/Finish/Return button
		if (this.current == this.last - 1) {
			// Results button
			if (!this.scored)
				if (this.active) $.get(this.eurl, $.proxy(this.handleEnd, this));
				else this.showScore();
			// Finish/Return button
			else this.endQuiz();
		// Next button (generate new result)
		} else if (this.questions[this.current+1] === undefined) {
			this.loading = true;
			$('#' + this.qelement + '-next').attr('disabled', true);
			this.getNextQuestion();
		// Next button (show next result/question)
		} else {
			this.current += 1;
			this.showQuestion();
		}
	};

	this.handleBack = function(data) {
		if (this.loading) return; // maybe not necessary, but safer
		if (this.current == 0) {
		} else {
			this.current -= 1;
		}

		this.showQuestion();
	};

	this.handleNextField = function(e) {
		if (e.which == 13 && this.nextable === true) {
			var next = $(':input:eq(' + ($(':input').index(e.target) + 1) + ')').focus();
			if (!next.length) $('#' + this.qelement + '-submit').focus();/**/
		} else this.nextable = true;
	};

	this.handlePage = function(data) {
		if (this.loading) return;
		this.goTo(data.target.value - 1);
	};


	this.bindEvents = function() {
		this.unbindEvents();

		$(document).on('click',  '#' + this.qelement + '-back',   $.proxy(this.handleBack, this));
		$(document).on('click',  '#' + this.qelement + '-submit', $.proxy(this.handleSubmit, this));
		$(document).on('click',  '#' + this.qelement + '-next',   $.proxy(this.handleNext, this));
		$(document).on('change', '#' + this.qelement + '-page',   $.proxy(this.handlePage, this));
		$(document).on('keyup',  '#' + this.qelement + '-content input:text', $.proxy(this.handleNextField, this));
	};

	this.unbindEvents = function() {
		this.loading = false;
		$(document).off('click',  '#' + this.qelement + '-back');
		$(document).off('click',  '#' + this.qelement + '-submit');
		$(document).off('click',  '#' + this.qelement + '-next');
		$(document).off('click',  '#' + this.qelement + '-finish');
		$(document).off('change', '#' + this.qelement + '-page');
		$(document).off('keyup',  '#' + this.qelement + '-content input:text');
	};

	this.start = function(last, type) {
		this.last = last;
		this.current = -1;
		this.getNextQuestion();
		this.bindEvents();
		this.log('start', type, last);
	};

	this.review = function(data) {
		this.questions = data['questions'];
		this.results = data['results'];
		this.last = data['last'];
		this.score = data['score'];
		this.out_of = data['out_of'];
		this.active = !data['completed'];
		this.showQuestion();
		this.bindEvents();
	};

	this.goTo = function(index) {
		this.current = index;
		this.showQuestion();
	};

	this.endQuiz = function() {
		window.location.reload();
	};

	this.handleEnd = function(data) {
		if (data == 'success') {
			this.active = false;
			this.showScore();
			this.log('end');
		} else alert("Error: "+data);
	}

	this.log = function(action, label, value) {
		if (window.ga)
			ga('send', 'event', 'Quiz', action, label, value);
	}
}
