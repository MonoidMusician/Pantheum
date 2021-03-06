<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/quiz/common.php');
sro('/PHP5/quiz/quiz_types.php');
sro('/PHP5/quiz/QuizType.php');

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

$id = safe_get("id",$_GET);

?>
<br>
<div class="select quiz-category-select"><?php
$i = 0;
foreach ($cat as $k=>$vs) {
	?><input id="quiz-category<?= $i ?>" name="quiz-category" type="radio" value="<?= $k ?>"
	<?php
		if ($id===NULL?$k==="All":$k === safe_get("category",safe_get($id,$quiz_types)))
			echo "checked ";
		if ($k === "All") {
			$onclick = '$("[name=quiz-types]").parent().show();update_tabs();';
			echo "onclick='$onclick'";
			$k = "Any category";
		} else {
			$expr = "[name=quiz-types][value=\\\"".implode("\\\"], [name=quiz-types][value=\\\"", $vs)."\\\"]";
			$onclick = '$("[name=quiz-types]").parent().hide();$("'.$expr.'").parent().show();update_tabs();';
			echo "onclick='$onclick'";
		}
	?>
	><label for="quiz-category<?= $i ?>" data-i18n="quiz.category.<?= $k ?>"><?= htmlspecialchars($k);?></label><?php
	$i += 1;
}
?></div><br><?php
$first = TRUE;
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
		if ($n === "auto")
			if (is_callable($v["options"])) error_log("Warning: n_questions=auto does not work with deferred options (key $k)");
			else $n = count($v["options"]);
		$onclick = 'var prev_val = $("#quiz-number").val();$("#quiz-number").val(Math.abs('.$n.')).attr("disabled",'.$n.' > 0)';
	} else $onclick = 'if ($("#quiz-number").attr("disabled"))$("#quiz-number").removeAttr("disabled").val(prev_val);else prev_val = $("#quiz-number").val()';
	$onclick .= ';$("#selections").toggle(!!$("#selections ul").hide().filter("#'.$k.'-selections").show().length);';
	echo "onclick='$onclick'"; ?>
	><?php
	echo htmlspecialchars($v["name"]);
	?></label><?php
}
?></div>


<script>
var prev_val = 10;
function update_tabs() {
	$('#quiz-type-selection br').remove();
	var i = 0;
};
update_tabs();
$(function(){
	var $categories = $('input[name=quiz-category] + label');
	$(document).on('mousedown', function() {
		$categories.bind('mouseover',function() {
			$(this).trigger('mousedown');
		});
	}).on('mouseup', function() {
		$categories.unbind('mouseover');
	});
	$categories.addClass('unselectable').on('mousedown', function() {
		$(this).trigger('click');
	});
	$('input[name=quiz-types]:checked').click();
	var max_height = 0, max_width = 0, min_height = Infinity;
	var popstate = false;
	$('input[name=quiz-types]').parent().each(function() {
		var w = $(this).width()+2;
		if (w > max_width) max_width = w;
	}).on('click', function() {
		if (popstate) return;
		var id = $(this).find('input').val(), loc = "quiz.php";
		if (id !== "random") loc += "?id="+id;
		if (window.location.href != loc && (!window.history.state || window.history.state.id != id))
			window.history.pushState({id:id}, "", loc);
	});
	window.history.replaceState({id:$('input[name=quiz-types]:checked').val()}, "");
	window.addEventListener('popstate', function(event) {
		if (!event.state) return;
		popstate = true;
		if (!event.state || !event.state.id)
			$('input[name=quiz-types]:first').click();
		else $('input[name=quiz-types][value='+event.state.id+']').click();
		popstate = false;
	});
	$('input[name=quiz-types]').parent().css('width', max_width + 'px');
	var container = $('#quiz-type-selection');
	container.css('max-height', container.height() + 10 + 'px');
	//container.css('width', (max_width*3 + 20) + 'px');
	$(function() {
		container.css('width', '100%');
		var orig = $('input[name=quiz-category]:checked');
		$('input[name=quiz-category]:not(:first)').each(function() {
			var $this=$(this);
			$this.click();
			update_tabs();
			var h = container.height()+10;
			if (h > max_height) max_height = h;
			if (h < min_height) min_height = h;
		});
		container
			.css('height', max_height + 'px')
			.css('min-height', min_height + 'px')
			.css('resize', 'vertical')
			.css('width', '');
		orig.click();
	});
});
</script><?php
?>
