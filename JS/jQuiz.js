Plugins.AutosizeInput.getDefaultOptions().space = 30;

function randomcase(string) {
	return string.replace(/\w/g, function(i){return Math.random() > 0.5 ? i.toUpperCase() : i;});
}

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
	this.next = 0;
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
		this.questions[this.next] = jQuery.parseJSON(data);
		this.current = this.next;
		this.next += 1;
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

		if ((this.current == (this.next-1)) && (this.results[this.current] == undefined)) {
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
					result = '<input tabindex="'+(++this.tabindex)+'"'+(part[4]!='en'?ncorrect:ycorrect)+' class="autosizeable" type="text" id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] + '" title="' + part[3] + '">';
				} else if (part[0] == 'paragraph') {
					result = '<textarea tabindex="'+(++this.tabindex)+'" id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] + '" title="' + part[3] + '" style="font-family:Linux Libertine;"></textarea>';
				} else if (part[0] == 'select') {
					//result += '<select id="' + this.qelement + '-' + part[1] + '" title="' + part[2] + '">';
					result += '<span id="' + this.qelement + part[1] + '" class="select select-bordered">';
					var tabin = ' tabindex="'+(++this.tabindex)+'"';
					for (var oid in part[3]) {
						var option = part[3][oid];
						result += '<label>';
						result += '<input'+tabin+' class="inputlabel" type="radio"';
						result += 'name="'+this.qelement + '-' + part[1]+'"';
						result += 'value="'+option+'" required>';
						result += option;
						result += '</label><br>';
						//result += '<option>' + option + '</option>';
						//var tabin = '';
					}
					result += '</span>';
					//result += '</select>';
				} else if (part[0] == 'matching-row') {
					var tabin = ' tabindex="'+(++this.tabindex)+'"';
					for (var oid in part[3]) {
						var option = part[3][oid];
						result += '<td><label>';
						result += '<input'+tabin+' class="inputlabel" type="radio"';
						result += 'name="'+this.qelement + '-' + part[1]+'"';
						result += 'value="'+option+'" required>';
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
						var tabin = ' tabindex="'+(++this.tabindex)+'"';
						for (var oid in part[4]) {
							var option = part[4][oid];
							result += '<td><label>';
							result += '<input'+tabin+' class="inputlabel" type="radio"';
							result += 'name="'+this.qelement + '-' + part[1]+'-'+vid+'"';
							result += 'value="'+option+'" required>';
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
		æ.format($html);
		$html.find('input.autosizeable').autosizeInput();
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
		if (this.current == this.last - 1) {
			if (!this.scored)
				$.get(this.eurl, $.proxy(this.handleEnd, this));
			else this.endQuiz();
		} else if (this.current == (this.next-1)) {
			this.loading = true;
			$('#' + this.qelement + '-next').attr('disabled', true);
			this.getNextQuestion();
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

	this.start = function(last) {
		this.last = last;
		this.getNextQuestion();
		this.bindEvents();
		this.log('start', undefined, last);
	};

	this.review = function(data) {
		this.questions = data['questions'];
		this.results = data['results'];
		this.last = data['last'];
		this.score = data['score'];
		this.out_of = data['out_of'];
		this.active = !data['completed'];
		if (this.active) {
			for (var i=0;i++;i<this.last) {
				if (this.results[i] == undefined) {
					this.next = i; break;
				}
			}
			if (this.questions[this.next] != undefined) {
				this.next += 1;
			}
		}
		this.showQuestion();
		this.bindEvents();
	};

	this.goTo = function(index) {
		this.current = index;
		this.showQuestion();
	};

	this.endQuiz = function() {
		if (!this.active || 1) {
			location.reload();
			return;
		}
		$.get(this.eurl, $.proxy(this.handleEnd2, this));
	};

	this.handleEnd = function(data) {
		if (data == 'success') {
			this.showScore();
			this.log('end');
		} else alert("Error: "+data);
	}

	this.handleEnd2 = function(data) {
		if (data == 'success') {
			location.reload();
		} else alert("Error: "+data);
	}

	this.log = function(action, label, value) {
		if (window.ga)
			ga('send', 'event', 'Quiz', action, label, value);
	}
}
