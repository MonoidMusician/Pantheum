<style>
	form input:not([type="checkbox"]):not([type="radio"]) {
		width: 0px;
		min-width: 1.25em;
		max-width: 25em;
		/*transition: width 0.3s;*/
	}
	#definitions input {
		min-width: 5em;
	}
	#relations li input + input {
		min-width: 9.25em;
	}
	#relations li input {
		min-width: 2.5em;
	}

	.header {
		/*font-weight: normal;*/
	}
	.header.selected {
		color: #aa0000;
		font-weight: bold;
	}
	.header:hover {
		color: #CC3333 !important;
	}

	select {
		font-family: Linux Libertine;
	}

	.present {
		font-weight: bold;
		color: #CC3333;
	}
</style>
<p style="font-size: 2em; color:red">¡¡js failed to load!! (syntax error)</p>
<h1>Panθeũ</h1>
<script>
	var rolling_update = function($e, new_text, interval) {
		var i;
		for (i=0; i<new_text.length; i++) {
			setTimeout((function(i) {
				return (function() {
					var t = $e.text();
					t = t.substr(0,i)+new_text[i]+t.substr(i+1);
					//t[i] = new_text[i];
					$e.text(t);
				});
			})(i), interval*i);
		}
		if (new_text.length < $e.text().length)
			setTimeout(function() {
					var t = $e.text();
					t = t.substr(0,new_text.length);
					$e.text(t);
			}, interval*i);
	};
	$('h1').html('<span>'+$('h1').html().split('').join('</span><span>')+'</span>');
	setInterval(function() {
		var r = function(a) {
			return a[Math.floor(Math.random()*a.length)];
		};
		var r2 = function(a) {
			return $.map(a,r).join("");
		};
		var a = [
			["p","P","π","Π"],
			["A","a","α"],
			["N","n","ν"],
			["TH","th","θ","Θ","ð","Ð","þ","Þ"],
			["E","e","ε"],
			["UM","um","Ũ","ũ"]
		];
		var i = Math.floor(Math.random()*a.length);
		$('h1 span:nth-child('+(i+1)+')').text(r(a[i]));
		//$('h1').text(r2(a));
		/*rolling_update($('h1'), r2(a), 100);
		/*$t = $('h1:visible').hide();
		if (!$('h1:last:visible').length) {
			$t.next().show();
		} else {
			$('h1:first').show();
		}*/
	}, 10000);
</script>
<h2>
	<span class="header selected" id="verb" onclick="show('verb')">Verb</span>
	<span class="header" id="noun" onclick="show('noun')">Noun</span>
	<span class="header" id="adjective" onclick="show('adjective')">Adjective</span>
	<span class="header" id="adverb" onclick="show('adverb')">Adverb</span>
</h2>
<div style="width: calc(100% - 500px); height: 300px; float: right; display:none">
<iframe style="width: 100%; height: 100%"></iframe>
<br>
<button onclick="if ($('iframe')[0].history) $('iframe')[0].history.back()">←</button>
<button onclick="if ($('iframe')[0].history) $('iframe')[0].history.forward()">→</button>
</div>
<form data-action="submit-verb.php">
<div class="verb">
	<select name="conjugation">
		<optgroup label="1st Conjugation">
		<option data-ending0="ō" data-ending1="āre" data-ending2="ī" value="conj-1">1st Conjugation</option>
		<option data-ending0="or" data-ending1="ārī" data-ending2="us sum" value="conj-1-deponent">1st Conjugation Deponent</option>
		<optgroup label="2nd Conjugation">
		<option data-ending0="eō" data-ending1="ēre" data-ending2="ī" value="conj-2">2nd Conjugation</option>
		<option data-ending0="eor" data-ending1="ērī" data-ending2="us sum" value="conj-2-deponent">2nd Conjugation Deponent</option>
		<optgroup label="3rd Conjugation">
		<option data-ending0="ō" data-ending1="ere" data-ending2="ī" value="conj-3">3rd Conjugation</option>
		<option data-ending0="iō" data-ending1="ere" data-ending2="ī" value="conj-3-io">3rd Conjugation i-stem</option>
		<option data-ending0="or" data-ending1="ī" data-ending2="us sum" value="conj-3-deponent">3rd Conjugation Deponent</option>
		<option data-ending0="ior" data-ending1="ī" data-ending2="us sum" value="conj-3-io-deponent">3rd Conjugation Deponent i-stem</option>
		<optgroup label="4th Conjugation">
		<option data-ending0="iō" data-ending1="īre" data-ending2="ī" value="conj-4">4th Conjugation</option>
		<option data-ending0="ior" data-ending1="īrī" data-ending2="us sum" value="conj-4-deponent">4th Conjugation Deponent</option>
	</select>
	<select name="voice">
		<option value="active" data-ending3="us">Active</option>
		<option value="neutral" data-ending3="um">Neutral</option>
		<option value="no-supine" data-ending3="">No supine</option>
	</select>
	<select name="person">
		<option data-ending0="ō" data-ending2="ī" value="personal">Personal</option>
		<option data-ending0="ō" data-ending2="ī" value="impersonal-passive">Impersonal passive</option>
		<option data-ending0="at" data-ending2="it" value="impersonal">Impersonal</option>
	</select>
</div>
<div class="noun">
	<select name="declension">
		<optgroup label="1st Declension">
		<option data-ending0="a" data-ending1="æ" value="decl-1">1st Declension</option>
		<option data-ending0="ē" data-ending1="ēs" value="decl-1-e">Grecian -ē</option>
		<option data-ending0="ēs" data-ending1="æ" value="decl-1-es">Grecian -ēs</option>
		<option data-ending0="ās" data-ending1="æ" value="decl-1-as">Grecian -ās</option>
		<optgroup label="2nd Declension">
		<option data-ending0="us" data-ending1="ī" value="decl-2">2nd Declension</option>
		<option data-ending0="er" data-ending1="rī" value="decl-2-er">2nd Declension -er, -rī</option>
		<option data-ending0="r" data-ending1="rī" value="decl-2-r">2nd Declension -r, -rī</option>
		<option data-ending0="os" data-ending1="ī" value="decl-2-os">Grecian -ŏs</option>
		<option data-ending0="um" data-ending1="ī" value="decl-2-neuter">2nd Declension Neuter</option>
		<option data-ending0="on" data-ending1="ī" value="decl-2-on-neuter">Grecian -on (Neuter)</option>
		<optgroup label="3rd Declension">
		<option data-ending0="" data-ending1="is" value="decl-3">3rd Declension</option>
		<option data-ending0="" data-ending1="is" value="decl-3-i">3rd Declension i-stem</option>
		<option data-ending0="" data-ending1="eos" value="decl-3-eos">Grecian -eos</option>
		<option data-ending0="" data-ending1="is" value="decl-3-neuter">3rd Declension Neuter</option>
		<option data-ending0="" data-ending1="is" value="decl-3-i-neuter">3rd Declension i-stem Neuter</option>
		<optgroup label="4th Declension">
		<option data-ending0="us" data-ending1="ūs" value="decl-4">4th Declension</option>
		<option data-ending0="ū" data-ending1="ūs" value="decl-4-neuter">4th Declension Neuter</option>
		<option data-ending0="ō" data-ending1="ūs" value="decl-4-o">Grecian -ō</option>
		</optgroup>
		<option data-ending0="ēs" data-ending1="eī" value="decl-5">5th Declension</option>
		<option data-ending0="" data-ending1="" value="indeclineable">Indeclineable</option>
	</select>
	<select name="gender">
		<option value="masculine">Masculine</option>
		<option value="feminine">Feminine</option>
		<option value="neuter">Neuter</option>
		<option value="common">Common</option>
	</select>
	<select name="number">
		<option data-ending0="a" data-ending1="æ" value="spl">Singular and plural</option>
		<option data-ending0="a" data-ending1="æ" value="s">Singulare tantum</option>
		<option data-ending0="æ" data-ending1="ārum" value="pls">Quasi plurale</option>
		<option data-ending0="æ" data-ending1="ārum" value="pl">Plurale tantum</option>
	</select>
</div>
<div class="adjective">
	<select name="adj-decl">
		<option data-ending0="us" data-ending1="a" data-ending2="um" value="adjective-12">1st/2nd Declension</option>
		<option data-ending0="" data-ending1="" data-ending2="is" value="adjective-3">3rd Declension of two terminations</option>
		<option data-ending0="" data-ending1="is" data-ending2="e" value="adjective-3-3">3rd Declension of three terminations</option>
	</select>
	<select name="comparison">
		<option value="adj-comparable">Comparable</option>
		<option value="adj-uncomparable">Uncomparable</option>
	</select>
</div>
<div class="adverb">
	<select name="adv-decl">
		<optgroup label="1st/2nd Declension">
		<option data-ending0="ē" data-ending1="us" data-ending2="imē" value="adv-e">-ē</option>
		<option data-ending0="ō" data-ending1="us" data-ending2="imē" value="adv-o">-ō</option>
		<option data-ending0="um" data-ending1="us" data-ending2="imē" value="adv-um">-um</option>
		<optgroup label="3rd Declension">
		<option data-ending0="iter" data-ending1="us" data-ending2="imē" value="adv-iter">-iter</option>
		<option data-ending0="er" data-ending1="us" data-ending2="imē" value="adv-er">-er</option>
		</optgroup>
		<option data-ending0="" data-ending1="" data-ending2="" value="uncomparable">Uncomparable</option>
		<option data-ending0="" data-ending1="" data-ending2="" value="adv-irregular">Irregular</option>
	</select>
</div>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'ā');$(last_active_element).next().next().trigger('click.repl')">ā</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'ē');$(last_active_element).next().next().trigger('click.repl')">ē</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'ī');$(last_active_element).next().next().trigger('click.repl')">ī</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'ō');$(last_active_element).next().next().trigger('click.repl')">ō</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'ū');$(last_active_element).next().next().trigger('click.repl')">ū</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'ȳ');$(last_active_element).next().next().trigger('click.repl')">ȳ</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'æ');$(last_active_element).next().next().trigger('click.repl')">æ</a>
<a href="javascript:void(0)" onclick="insertAtCaret(last_active_element, 'œ');$(last_active_element).next().next().trigger('click.repl')">œ</a>
[<a id="clearall" href="javascript:void(0)" onclick="$('input').each(function(){$(this).val('').trigger('keydown')});$('#definitions input').not(':first').parent().remove();$('#forms span:visible').first().trigger('click.repl');$('#wiktionary, #perseus').removeAttr('href')">clear all</a>]
 →
<a id="wiktionary" target="_blank">Wiktionary</a>,
<a id="perseus" target="_blank">Lewis & Short</a>
[<a id="perseus1" target="_blank">1</a>]
[<a id="perseus2" target="_blank">2</a>]
<div id="forms">
<div class="verb">
	<span class="present"></span>
	<input id="verb0" name="verb0" type="text"><span id="verb_ending0">ō,</span><span></span>
	<input id="verb1" name="verb1"><span id="verb_ending1">āre,</span><span></span>
	<input id="verb2" name="verb2"><span id="verb_ending2">ī,</span><span></span>
	<input id="verb3" name="verb3"><span id="verb_ending3">us</span><span></span>
</div> 
<div class="noun">
	<span class="present"></span>
	<input id="noun0"><span id="noun_ending0">a,</span><span></span>
	<input id="noun1"><span id="noun_ending1">æ</span><span></span>
</div>
<div class="adjective">
	<span class="present"></span>
	<input id="adjective0"><span id="adjective_ending0">us,</span><span></span>
	<input id="adjective1"><span id="adjective_ending1">a,</span><span></span>
	<input id="adjective2"><span id="adjective_ending2">um,</span><span></span>
	<input id="adjective3"><span id="adjective_ending3">or,</span><span></span>
	<input id="adjective4"><span id="adjective_ending4">imus</span><span></span>
</div>
<div class="adverb">
	<span class="present"></span>
	<input id="adverb0"><span id="adverb_ending0">ē,</span><span></span>
	<input id="adverb1"><span id="adverb_ending1">us,</span><span></span>
	<input id="adverb2"><span id="adverb_ending2">imē</span><span></span>
</div>
</div>
<br>
<script>
	if ((function(){
		var check = false;
		(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
		return check;
	})()) $('iframe').parent().remove();
</script>
<div>
	Definitions:<br>
	<ul id="definitions" style="padding-top: 0px"><li><input placeholder="primary"></ul>
</div>
<div>
	Relations:<br>
	<ul id="relations" style="padding-top: 0px"><li><input placeholder="type"><input placeholder="word" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><label><input type="checkbox">Mutual</label></ul>
</div>
CLC stage(s): <input placeholder="stage, …" style="width: 80px">
<br>
<button id="submit" type="button">Submit</button>
</form>
<script>
$('p').text('js load error (runtime)');
var lock_url = true;
$('iframe').on('load',function() {
	if (lock_url) return;
	lock_url = true;
	$this=$(this);
	$this.attr('src', $this.attr('src')+'#Latin');
});
$('.present').attr('title','This word already exists!');
// autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
$('#forms input')
	.attr('autocomplete', 'off')
	.attr('autocorrect', 'off')
	.attr('autocapitalize', 'off')
	.attr('spellcheck', 'false');
var lock_link = false;
$('#wiktionary,#perseus').on('click', function() {
	if (lock_link) return;
	var $t = $(this), t = this;
	if ($t.attr('href')) return;
	lock_link = true;
	setTimeout(function() {
		lock_link = false;
		if ($t.attr('href'))
			t.click(); // jQuery does not follow the link
	}, 2);
});
var transform = æ.mix(
	æ.deASCIIize,
	æ.x
);
Plugins.AutosizeInput.getDefaultOptions().space = 6;
var relation_autocomplete = {
	//lookup: names,
	serviceUrl: '/PHP5/dictionary/get-info-json.php',
	params: {
		"lang":"la",
	},
	paramName: "name",
	deferRequestBy: 150,
	noCache: true,
	transformResult: function(response) {
		response = JSON.parse(response);
		return {suggestions: response};
	},
	minChars: 1,
};
$('#definitions input, #forms input, #relations input:not([type=checkbox])').autosizeInput().last().autocomplete(relation_autocomplete, 1);
var last_active_element;
$('input').on('blur', function () {
	last_active_element = this;
});

function insertAtCaret(txtarea, text) {
	var scrollPos = txtarea.scrollTop;
	var caretPos = txtarea.selectionStart;

	var front = (txtarea.value).substring(0, caretPos);
	var back = (txtarea.value).substring(txtarea.selectionEnd, txtarea.value.length);
	txtarea.value = front + text + back;
	caretPos = caretPos + text.length;
	txtarea.selectionStart = caretPos;
	txtarea.selectionEnd = caretPos;
	txtarea.focus();
	txtarea.scrollTop = scrollPos;
	$(txtarea).trigger("keyup");
}
var add_definition, add_relation;
var lock = false;
$('#definitions input').on('keyup', add_definition = function(e) {
	//if (e.which == 8) $('#definitions input').last().trigger('focus');
	if (lock || e.which != 13) return; lock=true; // hack
	//alert(e.which);
	$('#definitions input').filter(function() {
		return !$(this).val();
	}).parent().remove();
	$('#definitions').append('<li><input placeholder="secondary">').on('keyup', add_definition);
	$('#definitions input').first().attr('placeholder','primary');
	setTimeout(function() {
		$('#definitions input').last().trigger('focus').autosizeInput();
		lock = false;
	}, 1);
});
$('#relations input').on('keyup', add_relation = function(e) {
	//if (e.which == 8) $('#definitions input').last().trigger('focus');
	if (lock || e.which != 13) return; lock=true; // hack
	//alert(e.which);
	$('#relations input:not([type=checkbox])').filter(function() {
		return !$(this).val();
	}).parent().remove();
	$('#relations').append('<li><input placeholder="type"><input placeholder="word" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><label><input type="checkbox">Mutual</label>').on('keyup', add_relation);
	setTimeout(function() {
		updater('#relations input:nth-last-child(2)', [], []);
		$('#relations input:nth-last-child(2)').autosizeInput().prev().trigger('focus').autosizeInput();
		$('#relations input:nth-last-child(2)').autocomplete(relation_autocomplete);
	}, 1);
});

function show(type) {
	var types = ['verb', 'noun', 'adjective', 'adverb'];
	$.each(types, function (i, t) {
		if (type != t) {
			$('#' + t).removeClass('selected');
			$('.' + t).hide();
		} else {
			$('#' + t).addClass('selected');
			$('.' + t).show();
		}
	});
	$('form').data('action', 'submit-'+type+'.php');
	$(last_active_element).blur();
	last_active_element = $('input:visible')[0];
	$('#clearall').trigger('click');
}

var lock_blur = false;
$('#forms input').on('blur.repl', function(){
	var $this = $(this), $par = $this.parent(), val, suffix, slug, nomen, type, w;
	if (!$this.is(':visible')) return;
	if (!lock_blur) {
		if (!$par.children('input').val()) {
			$('iframe').parent().hide();
			$('.present').text('');
			return;
		}
		lock_blur = true;
		setTimeout(function() {
			if (lock_blur)
				$par.children('input').trigger('blur.repl');
			lock_blur = false;
		}, 0);
		return;
	}
	$this.hide();
	suffix = $this.next().hide().text().trim();
	var comma = suffix.endsWith(",");
	if (comma) suffix = suffix.slice(0,-1);
	val = $.map($this.val().split(/\//g), function(a){
		a = a.trim()+suffix;
		if (slug === undefined) nomen = slug = a;
		return a;
	}).join("/").normalize('NFKD') + (comma?',':'');
	while (slug.endsWith(",")) slug = slug.slice(0,-1).trim();
	if (!$this.prev().length || $this.prev().is('.present')) {
		slug = æ.ASCIIize(slug.normalize('NFKD')), w = 'wiktionary.org/wiki/'+slug;
		$('#wiktionary').attr('href','http://en.'+w+'#Latin');
		$('iframe').parent().show();
		if ($('iframe').attr('src') != w && $('iframe').attr('src') != w+'#Latin')
		{ $('iframe').attr('src', 'http://en.m.'+w); lock_url = false; }
		$('#perseus').attr('href','http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry='+slug);
		$('#perseus1').attr('href','http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry='+slug+'1');
		$('#perseus2').attr('href','http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry='+slug+'2');
		type = (
			$par.is(".verb") ? "verb" :
			$par.is(".noun") ? "noun" :
			$par.is(".adjective") ? "adjective" :
			$par.is(".adverb") ? "adverb" :
			$par.is(".preposition") ? "preposition" :
			$par.is(".conjunction") ? "conjunction" :
			"unknown"
		);
		$.get("/PHP5/dictionary/word-exists.php",{'lang':'la','spart':type,'name':nomen})
		.done(function(data) {
			if (data == "present" || data == "absent")
			{ $('.present').text({"present":"⚠","absent":""}[data]); }
			else alert("Word lookup failed: "+data);
		});
	}
	$this.next().next().show().text(val).on('click.repl', function() {
		var $t = $(this);
		if (!lock_blur) {
			lock_blur = true;
			$this.parent().children('span:visible').trigger('click.repl');
			lock_blur = false;
			$t.prev().prev().focus();
			return;
		}
		$t.hide(); $t.prev().show().prev().show();
	});
}).on('focus.repl', function(){lock_blur=false});

var lock_updater = false;
var updater = function (target, fields_strict, fields_loose) {
	$(target).on('keyup', function () {
		var $this = $(this);
		if ($this.parents('#forms').length && !$this.prev().length)
			$('#wiktionary, #perseus').removeAttr('href');
		$('.present').text(''); messageTip(false);
		if (lock_updater) return; lock_updater = true;
		var val, orig = val = $this.val();
		val = transform(val);
		if (val && val != orig)
			$this.val(val).trigger('keyup');
		lock_updater = false;
		$.each(fields_strict, function (i, field) {
			$(field).val(val).trigger('keydown');
		});
		var ignore = [];
		$.each(fields_loose, function (i, field_l) {
			//if(ignore.length) alert("length "+ignore);
			//if ($.inArray(field_l[0], ignore) > -1) return;
			var $field = $(field_l[0]),
				val2 = $field.val(),
				val3, match;
			if (field_l[1] === true) {
				match = true;
			} else if (typeof field_l[1] == "function") {
				match = field_l[1](val2);
			} else {
				//if (field_l[1] == "iō".normalize("NFKD")) alert(val.hexEncode())
				match = val.endsWith(field_l[1]) && (!val2 || val2.endsWith(field_l[2]) || val2.endsWith(field_l[1]));
			}
			if (!val) match = true;
			/*alert(val3);alert($field.length);
			alert((val.endsWith(field_l[1])?" T":" F"));
			alert(val2);
			alert( (! val2 || val2.endsWith(field_l[2])?"T":"F") );/**/
			if (match) {
				ignore.push(field_l[0]);
				//alert("pushed "+ignore.length+" ("+field_l[1]+")");
				if (typeof field_l[2] == "function") {
					val3 = $.map(val.split(/\//g), function(v){return v ? field_l[2](v) : ""}).join("/");
				} else if (field_l[1] === true) {
					val3 = $.map(val.split(/\//g), function(v){return v ? v + field_l[2] : ""}).join("/");
				} else {
					val3 = $.map(val.split(/\//g), function(v){return v ? v.substr(0, v.length - field_l[1].length) + field_l[2] : ""}).join("/");
				}
				val3 = transform(val3);
				$field.val(val3).trigger('keydown');
			}
		});
	});
};
var verb_endings = [
	['#verb2', '', 'āv'.normalize('NFKD')],
	['#verb3', '', 'āt'.normalize('NFKD')]
];
updater('#verb0', ['#verb1'], verb_endings);
updater('#verb1', ['#verb0'], verb_endings);
var deponent = [[],[]];
updater('#verb2', deponent[0], []);
updater('#verb3', deponent[1], []);
var noun_endings = [ ['#noun1','','']];
var noun_endings3 = [
	['#noun1','iō'.normalize('NFKD'),'iōn'],
	['#noun1','or','ōr'],
	['#noun1','en','in'],
	['#noun1','is',''],
	['#noun1','ex','ic'],
	['#noun1','eps','ip'],
	['#noun1','\u0304ns','nt'],
	['#noun1','es','it'],
	['#noun1','ōs'.normalize('NFKD'),'ōd'],
	['#noun1','os','or'],
	['#noun1','us','er'],
	['#noun1','ōx'.normalize('NFKD'),'ōc'],
	['#noun1','e',''],
];
updater('#noun0', [], noun_endings);

var comparison = [''];
var adjective_comparison = [
	
];
var adjective_endings = [
	[['#adjective1','#adjective2'], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]],
	[['#adjective0','#adjective2'], []],
	[['#adjective1','#adjective0'], []]
];
var adjective_endings12 = [
	[['#adjective1','#adjective2'], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]],
	[['#adjective0','#adjective2'], []],
	[['#adjective1','#adjective0'], []]
]
var adjective_endings3 = [
	[['#adjective1','#adjective2'], [
		['#adjective3',true,'i'],
		['#adjective3','is','i'],
		['#adjective2', '\u0304ns', 'nt'],
		['#adjective1', 'is', 'e'],
		['#adjective2', 'is', ''],
		['#adjective4', true, function(a){
			if (a.endsWith("is")) a = a.slice(0,-2);
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]],
	[[], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]],
	[[], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]]
];
var adjective_endings33 = [
	[['#adjective1','#adjective2'], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]],
	[['#adjective2'], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]],
	[['#adjective1'], [
		['#adjective3',true,'i'],
		['#adjective4', true, function(a){
			if (a.endsWith("er"))
				return a+"r";
			if (a.endsWith("r"))
				return a.slice(0,-1)+"err";
			if (a.endsWith("il"))
				return a+"l";
			return a+"iss";
		}]
	]]
];
updater('#adjective0', adjective_endings[0][0], adjective_endings[0][1]);
updater('#adjective1', adjective_endings[1][0], adjective_endings[1][1]);
updater('#adjective2', adjective_endings[2][0], adjective_endings[2][1]);
updater('#adverb0', [], [
	['#adverb1', '', 'i'],
	['#adverb2', true, function(a){
		if (a.endsWith("er"))
			return a+"r";
		if (a.endsWith("r"))
			return a.slice(0,-1)+"err";
		if (a.endsWith("il"))
			return a+"l";
		return a+"iss";
	}]
]);
updater('#forms input, #relations input:last', [], []);

function do_visible(name, numbers, show) {
	if (show === true) show = "inline";
	if (show === false) show = "none";
	$.each(numbers, function(j,number) {
		$('#'+name+number).css('display',show);
		$('#'+name+"_ending"+number).css('display',show);
		$('#'+name+"_ending"+number+" + *").css('display',show);
	});
}
$('select').on('change', function () {
	var $t = $(this),
		name = $t.val();
	if (!$(this).prev().text().trim()) $('#clearall').trigger('click');
	//alert(name);
	if (name in changes)
		$.each(changes[name], function (i, j) {
			var e = $t.parent().find('option[value="' + i + '"]');
			$.each(j, function (attr, val) {
				e.data(attr, val);
			});
		});
	var sel = $t.find(':selected'),
		ending = $t.parent(), type;
	ending = '#' + (type = (
		ending.is(".verb") ? "verb" :
		ending.is(".noun") ? "noun" :
		ending.is(".adjective") ? "adjective" :
		ending.is(".adverb") ? "adverb" :
		ending.is(".preposition") ? "preposition" :
		ending.is(".conjunction") ? "conjunction" :
		"unknown"
	)) + "_ending";
	if (name.endsWith("-neuter")) $("select[name=gender]").val("neuter");
	else if (name.startsWith("decl-1")) $("select[name=gender]").val("feminine");
	else if (name.startsWith("decl-2")) $("select[name=gender]").val("masculine");
	else if (name.startsWith("decl-4")) $("select[name=gender]").val("masculine");
	else if (name.startsWith("decl-5")) $("select[name=gender]").val("feminine");
	else if (name == "indeclineable") $("select[name=gender]").val("neuter");
	if (name.startsWith("conj-1")) {
		verb_endings[0][1] = verb_endings[1][1] = "";
		verb_endings[0][2] = "āv";
		verb_endings[1][2] = "āt";
	} else if (name.startsWith("conj-2")) {
		verb_endings[0][1] = "";
		verb_endings[0][2] = "u";
		verb_endings[1][1] = true;
		verb_endings[1][2] = function(a){
			if(a.endsWith("r"))
				return a+"it";
			if(a.endsWith("m") || a.endsWith("l"))
				return a+"ēt"
			return a+"t"
		};
	} else if (name.startsWith("conj-3")) {
		verb_endings[0][1] = verb_endings[1][1] = true;
		verb_endings[0][2] = function(a){
			if (a.match(/n[dc]$/i))
				return a.replace(/n([dc])$/i,'\u0304$1');
			if (a.length > 1 && a[a.length-1] == "c")
				return a.slice(0,-1)+"x";
			if (a.match(/(?:[aeiouyæœ]|ā|ē|ī|ō|ū|ȳ)[dt]$/i))
				return a.slice(0,-1)+"s";
			// reduplication (rare)
			/*if (a.match(/^[a-dfgj-np-tvxz](?:[aeiouyæœ]|ā|ē|ī|ō|ū|ȳ)[a-dfgj-np-tvxz]/i))
				return a.substr(0,2)+a;/**/
			return a;
		};
		verb_endings[1][2] = function(a){
			if (a.substr(-2) == "rr")
				return a.slice(0,-2)+"rs";
			if (a.match(/nd|nc/i))
				return a.replace(/nd|nc/i,'\u0304s');
			if (a.match(/(?:[aeiouyæœ]|ā|ē|ī|ō|ū|ȳ)[dt]/i))
				return a.slice(0,-1)+"s";
			if (a.match(/(?:[aeiouyæœ]|ā|ē|ī|ō|ū|ȳ)c$/i))
				return a.slice(0,-1)+"ct";
			if (a.length > 1 && a[a.length-1] == "n")
				return a+"x";
			return a+"it";
		};
	} else if (name.startsWith("conj-4")) {
		verb_endings[0][1] = verb_endings[1][1] = "";
		verb_endings[0][2] = "īv";
		verb_endings[1][2] = "īt";
	} else if (name.startsWith("adv-")) {
		$('#adverb1,#adverb2,#adverb_ending1,#adverb_ending2').show();
	} else if (name == "uncomparable") {
		$('#adverb1,#adverb2,#adverb_ending1,#adverb_ending2').hide();
	} else if (name == "adj-uncomparable") {
		do_visible(type,[3,4],false);
//		$('#adjective3,#adjective4,#adjective_ending3,#adjective_ending4,#adjective_ending3 + *,#adjective_ending4 + *').hide();
	} else if (name == "adj-comparable") {
		do_visible(type,[3,4],true);
		//$('#adjective3,#adjective4,#adjective_ending3,#adjective_ending4').show();
	} else if (name == "indeclineable") {
		$('#noun1,#noun_ending1').hide();
		$('#noun1').val('');
	} else if (name.startsWith("decl-3")) {
		noun_endings.length = 1;
		noun_endings.push.apply(noun_endings, noun_endings3);
	} else if (name.startsWith("decl-")) {
		noun_endings.length = 1;
	} else if (name == "adjective-3") {
		var dest = adjective_endings;
		var orig = adjective_endings3;
		for (var i in orig) {
			if (orig[i] == null) continue;
			for (var j in orig[i]) {
				if (orig[i][j] == null) continue;
				dest[i][j].length = 0;
				dest[i][j].push.apply(dest[i][j],orig[i][j]);
			}
		}
	} else if (name == "adjective-3-3") {
		var dest = adjective_endings;
		var orig = adjective_endings33;
		for (var i in orig) {
			if (orig[i] == null) continue;
			for (var j in orig[i]) {
				if (orig[i][j] == null) continue;
				dest[i][j].length = 0;
				dest[i][j].push.apply(dest[i][j],orig[i][j]);
			}
		}
	} else if (name == "adjective-12") {
		var dest = adjective_endings;
		var orig = adjective_endings12;
		for (var i in orig) {
			if (orig[i] == null) continue;
			for (var j in orig[i]) {
				if (orig[i][j] == null) continue;
				dest[i][j].length = 0;
				dest[i][j].push.apply(dest[i][j],orig[i][j]);
			}
		}
	} else if (name == "no-supine") {
		$('#verb3,#verb_ending3,#verb_ending3 + *').hide();
	} else if (name == "active" || name == "neutral") {
		$('#verb3,#verb_ending3').show();
	}
	if (name.startsWith("conj-")) {
		deponent[0].length = deponent[1].length = 0;
		if (name.endsWith("-deponent")) {
			deponent[0].push("#verb3");
			deponent[1].push("#verb2");
			verb_endings[0][2] = verb_endings[1][2];
		}
	}
	var l = function(n) {
		var d = sel.data('ending'+n);
		if (d === undefined) d = $(ending+n).text();
		while (d.endsWith(',')) d = d.slice(0,-1);
		n+=1;
		var v = $(ending+n).is(':visible') || $(ending+n).next().is(':visible');
		return d + (v?',':'');
	};
	$(ending + '0').text(l(0));
	$(ending + '1').text(l(1));
	$(ending + '2').text(l(2));
	$(ending + '3').text(l(3));
	$(last_active_element).next().next().trigger('click.repl');
	$(last_active_element).trigger('blur.repl');
});

show('verb');

var dict = new jWord();
dict.init('dictionary', '/PHP5/quiz/get-entries.php', '/PHP5/quiz/set-path.php', '#enter-names');
$('#submit').on('click', function() {
	setTimeout(function() {
		var get = {'definitions':[],'connections':[],'forms':[],'attr':''};
		$('input:not(:last):visible').each(function() {
			$this = $(this);
			if ($this.val())
				if ($this.attr('name'))
					get[$this.attr('name')] = $this.val();
				else if ($this.parents('#definitions').length)
					get['definitions'].push($this.val());
		});
		$('#relations :input:first').each(function() {
			$this = $(this);
			var k = $this.val();
			var n = $this.next().val();
			var m = $this.next().next().find('input').is(':checked');
			get['connections'].push([k,n,m]);
		});
		$('#forms > div:visible input').each(function() {
			get['forms'].push($(this).val().split("/").join("\n"));
		});
		$('select:visible').each(function() {
			get[$(this).attr('name')] = $(this).val();
		});
		$.each($('input:last').val().split(","), function(_,a) {
			a = a.trim();
			if (a) {
				if (get['attr']) get['attr'] += ',';
				get['attr'] += 'clc-stage='+a;
			}
		});
		get['spart'] = $('#forms > div:visible').attr('class');
		get['name'] = $('#forms > div:visible input + span + span').first().text().split("/")[0].trim();
		while (get['name'].endsWith(",")) get['name'] = get['name'].slice(0,-1).trim();
		console.log(get);
		messageTip("Trying to add word...");
		$.get(dict.api_path+'add-word-complete.php', get)
		.done(function(data) {
			if (data == "success")
			{ successTip("Word successfully added. <a href='dictionary.php?name="+get['name']+"' target='_blank'>See it</a>"); }
			else errorTip("Word could not be created: "+data);
		});
	}, 1);
});

$('p').remove();
</script>
