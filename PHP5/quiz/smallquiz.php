<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/lib/PHPLang/string.php');

global $mysqli;
$actual_link = "http://52.3.75.179/PHP5/quiz/smallquiz.php$_SERVER[REQUEST_URI]";

$la = safe_get("lang",$_GET);
if (!$la) $la = "la";

$db = defaultDB();
$s = $db->searcher();
$s->stmt .= "
	WHERE word_id IN (
		SELECT word_id FROM definitions
		WHERE def_lang = 'en'
		AND def_type IS NULL
	)
	AND word_lang = '$la'
	AND word_id NOT IN (
		SELECT word_id FROM attributes
		WHERE attr_tag = 'template' OR attr_tag = 'hidden'
	)";
$s->args = [];
$word = $s->rand();
$query = $mysqli->prepare("
	SELECT DISTINCT def_id FROM definitions
	WHERE def_lang = 'en'
	AND def_type IS NULL
	AND word_id = (?)
	ORDER BY rand()
	LIMIT 1
");
$res0 = NULL;
sql_getmany($query, $res0, ["i", $word->id()]);
$query->close();
if (!$res0) exit('An error occurred');
$query = $mysqli->prepare("
	SELECT DISTINCT def_id FROM definitions
	WHERE def_lang = 'en'
	AND def_type IS NULL
	AND word_id != (?)
	AND def_value != ''
	AND def_value != (?)
	AND word_id in (
		SELECT word_id FROM words WHERE word_lang = '$la'
	)
	AND word_id NOT IN (
		SELECT word_id FROM attributes
		WHERE attr_tag = 'template' OR attr_tag = 'hidden'
	)
");
$res1 = NULL;
sql_getmany($query, $res1, ["is", $word->id(), definition($db, $res0[0])->value()]);
$query->close();
if (!$res1) exit('An error occurred');
$res1 = choose_n_unique($res1, 4);
$res = array_merge($res0, $res1);

$options = [];
foreach ($res as &$r) $options[] = str_replace("\n",", ",definition($db, $r)->value());
$correct = $options[0];
shuffle($options);
$indices = array_keys($options);
shuffle($indices);
$correct_index = array_values(array_filter($indices, function($i)use($options,$correct){
	return ($options[$i] == $correct);
}))[0];
?><div id="pantheumsmallquiz">
<h3 style="margin-top:0px;margin-bottom:0.2em;">What does <?= display_word_name($word) ?> mean?</h3>
<div style="margin-bottom:0.2em;font-size:90%;" id="pantheumsmallquiz-time">
	<a href="javascript:void(0)">
	<span>&nbsp;</span>
	</a>
</div>
<span id="pantheumsmallquiz-answer" class="select select-bordered">
	<label><input name="pantheumsmallquiz" class="inputlabel" type="radio" value="<?= $indices[0] ?>" required><?= $options[$indices[0]] ?></label><br>
	<label><input name="pantheumsmallquiz" class="inputlabel" type="radio" value="<?= $indices[1] ?>" required><?= $options[$indices[1]] ?></label><br>
	<label><input name="pantheumsmallquiz" class="inputlabel" type="radio" value="<?= $indices[2] ?>" required><?= $options[$indices[2]] ?></label><br>
	<label><input name="pantheumsmallquiz" class="inputlabel" type="radio" value="<?= $indices[3] ?>" required><?= $options[$indices[3]] ?></label><br>
	<label><input name="pantheumsmallquiz" class="inputlabel" type="radio" value="<?= $indices[4] ?>" required><?= $options[$indices[4]] ?></label>
</span>
<script>
var pantheumsmallquiz = (function() {
	if (la_ipa) la_ipa.format();
	$('#pantheumsmallquiz-answer input').change(function() {
		$('#pantheumsmallquiz-answer input').attr('disabled', true);
		$('#pantheumsmallquiz-answer input[value=<?= $correct_index ?>]').parent().css('color', 'orange').css('font-weight', 'bold');
		var correct = ($(this).val() == <?= $correct_index ?>);
		$(this).parent().css('color', correct ? 'green' : 'red').css('font-weight', 'bold');
		if (pantheumsmallquiz.handler)
			pantheumsmallquiz.handler(correct);

		var html_data, countdown_finished = false;
		$.get("<?= $actual_link ?>")
		.success(function(data) {
			if (countdown_finished)
				$('#pantheumsmallquiz').replaceWith(data);
			else html_data = data;
		});

		$('#pantheumsmallquiz-time a').show();
		$('#pantheumsmallquiz-time span').countdown(Date.now()+5*1000)
		.on('update.countdown', function(event) {
			$(this).html(event.strftime('Next word in <span>%S</span> second%!S...'));
		}).on('finish.countdown', function(event) {
			if (html_data)
				$('#pantheumsmallquiz').replaceWith(html_data);
			else $(this).html('Loading next word...');
		});
	});
	$('#pantheumsmallquiz-time a').on('click', function() {
		$('#pantheumsmallquiz-time span').trigger('finish.countdown');
	});
	return pantheumsmallquiz ? pantheumsmallquiz : {
		handler: undefined
	};
})();
</script>
</div>
