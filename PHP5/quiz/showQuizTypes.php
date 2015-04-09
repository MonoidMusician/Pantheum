<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/quiz/common.php');
include_once('quiz_types.php');

global $quiz_types;
$quizzes = [];
$first = TRUE;
?><div class="select"><?php
foreach ($quiz_types as $k=>$v) {
    if (!is_array($v) or !array_key_exists("name", $v)) continue;
    ?><label><input name="quiz-types" type="radio" value="<?= $k ?>"
    <?php if ($first) echo "checked"; $first=FALSE; ?>
    <?php
    if (safe_get("n_questions",$v)) {
        $n = $v["n_questions"];
        if ($n === "auto") $n = count($v["options"]);
        $onclick = 'var prev_val = $("#quiz-number").val();$("#quiz-number").val('.$n.').attr("disabled","true")';
    } else $onclick = 'if ($("#quiz-number").attr("disabled"))$("#quiz-number").removeAttr("disabled").val(prev_val);else prev_val = $("#quiz-number").val()';
    echo "onclick='$onclick'"; ?>
    ><?php
    echo htmlspecialchars($v["name"]);
    ?></label><br><?php
}
?></div><script>var prev_val = 10;</script><?php
?>
