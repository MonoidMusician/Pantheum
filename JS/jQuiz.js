Plugins.AutosizeInput.getDefaultOptions().space = 30;

function jQuiz() {
	this.questions = [];
	this.results = [];
	this.answers = {};
	/*this.stats = [];*/
	this.score = 0;
	this.out_of = 0;
	this.active = true;
	this.current = 0;
	this.next = 0;
	this.last = 1;
	this.qelement = 'quiz';
	this.gurl = '';
	this.surl = '';
	this.eurl = '';

	this.init = function(qelement, gurl, surl, eurl) {
		this.qelement = qelement;
		this.gurl = gurl;
		this.surl = surl;
		this.eurl = eurl;
	};

	this.getNextQuestion = function() {
		$.ajaxSetup({async:false});
		$.get(this.gurl, $.proxy(this.handleQuestion, this));
		$.ajaxSetup({async:true});
	};

	this.submitQuestion = function() {
		$.ajaxSetup({async:false});
		$.post(this.surl, this.answers, $.proxy(this.handleResponse, this));
		$.ajaxSetup({async:true});
	};

	this.handleQuestion = function(data) {
		if (!data || data[0] != '[' || data[data.length-1] != ']')
			return alert('Error: '+data);
		this.questions[this.next] = jQuery.parseJSON(data);
		this.current = this.next;
		this.next += 1;
		this.displayQuestion();
	};

	this.handleResponse = function(data) {
		if (data != '') {
			var result = jQuery.parseJSON(data);
			this.results[this.current] = result;
			this.score += 0-(-result["score"]);
			this.out_of += 0-(-result["out_of"]);
			this.displayQuestion();
		}
	};

	this.buildHeader = function() {
		var header = '<section id="' + this.qelement + '-top">';

		if ((this.current - 1) >= 0) {
			header += '<button id="' + this.qelement + '-back">Back</button>';
		} else {
			header += '<button id="' + this.qelement + '-back" class="disabled">Back</button>';
		}

		header += '<span>' + (this.current+1) + ' / ' + this.last + '</span>';

		if ((this.current == (this.next-1)) && (this.results[this.current] == undefined)) {
			header += '<button id="' + this.qelement + '-submit">Submit</button>';
		} else if (this.current == this.last - 1) {
			if (this.active)
				header += '<button id="' + this.qelement + '-next">Finish</button>';
			else
				header += '<button id="' + this.qelement + '-next">Return</button>';
		} else {
			header += '<button id="' + this.qelement + '-next">Next</button>';
		}

		header += '</section>';

		return header;
	}

	this.parsePart = function(part) {
		var result = '';

		if (part[0] == 'wrap') {
			//pass
		} else if (part[0] == 'text') {
			result = '<span class="jquiz-text">' + part[1] + '</span>';
		} else if (part[0] == 'help') {
			if (this.results[this.current] != undefined) {
				var result = this.results[this.current];
				var correct = result['subscore'];
				var total = result['out_of'];
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
				var score = '<br><span class="jquiz-score">You got '+correct+incorrect+' / ' + total + percent + "</span>";
			} else var score = '';
			result = '<span class="jquiz-help">' + part[1] + '</span><br>'+score+'<br>';
		} else if (part[0] == 'html') {
			result = part[1];
		} else {
			if (this.results[this.current] == undefined) {
				if (part[0] == 'input') {
					result = '<input class="autosizeable" type="text" id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] + '" title="' + part[3] + '">';
				} else if (part[0] == 'paragraph') {
					result = '<textarea id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] + '" title="' + part[3] + '" style="font-family:Linux Libertine;"></textarea>';
				} else if (part[0] == 'select') {
					//result += '<select id="' + this.qelement + '-' + part[1] + '" title="' + part[2] + '">';
					result += '<span id="' + this.qelement + part[1] + '" class="select select-bordered">';
					for (var oid in part[3]) {
						var option = part[3][oid];
						result += '<label>';
						result += '<input class="inputlabel" type="radio"';
						result += 'name="'+this.qelement + '-' + part[1]+'"';
						result += 'value="'+option+'" required>';
						result += option;
						result += '</label><br>';
						//result += '<option>' + option + '</option>';
					}
					result += '</span>';
					//result += '</select>';
				} else if (part[0] == 'matching-row') {
					for (var oid in part[3]) {
						var option = part[3][oid];
						result += '<td><label>';
						result += '<input class="inputlabel" type="radio"';
						result += 'name="'+this.qelement + '-' + part[1]+'"';
						result += 'value="'+option+'" required>';
						result += (parseInt(oid)+1)+'.';
						result += '</label></td>';
						//result += '<option>' + option + '</option>';
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
						for (var oid in part[4]) {
							var option = part[4][oid];
							result += '<td><label>';
							result += '<input class="inputlabel" type="radio"';
							result += 'name="'+this.qelement + '-' + part[1]+'-'+vid+'"';
							result += 'value="'+option+'" required>';
							result += (parseInt(oid)+1)+'.';
							result += '</label></td>';
							//result += '<option>' + option + '</option>';
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
					result += this.results[this.current][part[1]][2];
				}
				if (i > 2) result += ')' + wrap['machine'][1];
			}
		}

		return result;
	};

	this.buildBody = function() {
		var body = '<section id="' + this.qelement + '-content">';
		body += '<p type="jquiz-question">';
		for (var pid in this.questions[this.current]) {
			var part = this.questions[this.current][pid];
			body += this.parsePart(part);
		}
		body += '</p>';
		body += '</section>';

		return body;
	}

	this.displayQuestion = function() {
		var html = this.buildHeader() + this.buildBody();
		var $html = $(html);
		$('#' + this.qelement).html($html);
		æ.format($html);
		$html.find('input.autosizeable').autosizeInput();
	};

	this.handleSubmit = function(data) {
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
		this.submitQuestion();
	};

	this.handleNext = function(data) {
		if (this.current == this.last - 1) {
			this.endQuiz();
		} else if (this.current == (this.next-1)) {
			this.getNextQuestion();
		} else {
			this.current += 1;
			this.displayQuestion();
		}
	};

	this.handleBack = function(data) {
		if (this.current == 0) {
		} else {
			this.current -= 1;
		}

		this.displayQuestion();
	};


	this.bindEvents = function() {
		this.unbindEvents();

		$(document).on('click', '#' + this.qelement + '-back', $.proxy(this.handleBack, this));
		$(document).on('click', '#' + this.qelement + '-submit', $.proxy(this.handleSubmit, this));
		$(document).on('click', '#' + this.qelement + '-next', $.proxy(this.handleNext, this));
	};

	this.unbindEvents = function() {
		$(document).off('click', '#' + this.qelement + '-back');
		$(document).off('click', '#' + this.qelement + '-submit');
		$(document).off('click', '#' + this.qelement + '-next');
		$(document).off('click', '#' + this.qelement + '-finish');
	};

	this.start = function(last) {
		this.last = last;
		this.getNextQuestion();
		this.bindEvents();
	};

	this.review = function(data) {
		this.questions = data['questions'];
		this.results = data['results'];
		this.last = data['last'];
		this.score = data['score'];
		this.out_of = data['out_of'];
		this.active = (this.results[this.last - 1] == undefined);
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
		this.displayQuestion();
		this.bindEvents();
	};

	this.endQuiz = function() {
		if (!this.active) {
			location.reload();
			return;
		}
		$.ajaxSetup({async:false});
		$.get(this.eurl, $.proxy(this.handleEnd, this));
		$.ajaxSetup({async:true});
	};

	this.handleEnd = function(data) {
		if (data == 'success') {
			location.reload();
		} else alert("Error: "+data);
	}

}
