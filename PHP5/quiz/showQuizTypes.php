<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/quiz/common.php');
include_once('quiz_types.php');

global $quiz_types;
$cat = ["All"=>[]];
foreach ($quiz_types as $id=>$q) {
	if (!is_array($q) or !array_key_exists("name", $q)) continue;
	$cat["All"][] = $id;
	$k = safe_get("category",$q);
	if (!$k or $k === "All") continue;
	if (!array_key_exists($k,$cat)) $cat[$k] = [];
	$cat[$k][] = $id;
}
?><div class="select"><?php
foreach ($cat as $k=>$vs) {
	?><label><input name="quiz-category" type="radio" value="<?= $k ?>"
	<?php
		if ($k === "All") {
			$onclick = '$("[name=quiz-types]").parent().show();update_tabs();';
			echo "checked onclick='$onclick'";
			$k = "Any category";
		} else {
			$expr = "[name=quiz-types][value=\\\"".implode("\\\"], [name=quiz-types][value=\\\"", $vs)."\\\"]";
			$onclick = '$("[name=quiz-types]").parent().hide();$("'.$expr.'").parent().show();update_tabs();';
			echo "onclick='$onclick'";
		}
	?>
	><?= htmlspecialchars($k);?></label><?php
}
?></div><br><?php
$first = TRUE;
$id = safe_get("id",$_GET);
$predicate = function($q) use(&$first,$id) {
	if ($id===NULL) {
		if ($first) {
			$first=FALSE;
			return TRUE;
		}
		return $first;
	}
	return $q===$id;
}
?><div id="quiz-type-selection" style="overflow: auto; -webkit-overflow-scrolling: touch;" class="select"><?php
foreach ($quiz_types as $k=>$v) {
	if (!is_array($v) or !array_key_exists("name", $v)) continue;
	?><label><input name="quiz-types" type="radio" value="<?= $k ?>"
	<?php if ($predicate($k)) echo "checked"; $first=FALSE; ?>
	<?php
	if (safe_get("n_questions",$v)) {
		$n = $v["n_questions"];
		if ($n === "auto") $n = count($v["options"]);
		$onclick = 'var prev_val = $("#quiz-number").val();$("#quiz-number").val('.$n.').attr("disabled","true")';
	} else $onclick = 'if ($("#quiz-number").attr("disabled"))$("#quiz-number").removeAttr("disabled").val(prev_val);else prev_val = $("#quiz-number").val()';
	echo "onclick='$onclick'"; ?>
	><?php
	echo htmlspecialchars($v["name"]);
	?></label><?php
}
?></div><script>
var prev_val = 10;
function update_tabs() {
	$('#quiz-type-selection br').remove();
	var i = 0;
	/*$('#quiz-type-selection label:visible:not(:last)').each(function() {
		i += 1;
		if (!(i % 3)) $(this).after('<br>');
	});/**/
};
update_tabs();
$(function(){
	$('input[name=quiz-types]:checked').click();
	var max_height = 0, max_width = 0;
	$('input[name=quiz-types]').parent().each(function() {
		var w = $(this).width()+2;
		if (w > max_width) max_width = w;
	});
	$('input[name=quiz-types]').parent().css('width', max_width + 'px');
	var container = $('#quiz-type-selection');
	//container.css('width', (max_width*3 + 20) + 'px');
	$(function() {
	$('input[name=quiz-category]:not(:first)').each(function() {
		var $this=$(this);
		$this.click();
		update_tabs();
		var h = container.height()+10;
		if (h > max_height) max_height = h;
	});
	container.css('height', max_height + 'px').css('resize', 'vertical');
	$('input[name=quiz-category]:first').click();
	});
});
</script><?php
?>
