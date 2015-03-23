<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
include_once('quiz_types.php');

global $quiz_types;

?><div class="select"><?php
foreach ($quiz_types as $k=>$v) {
    if (!is_array($v) or !array_key_exists("name", $v)) continue;
    ?><label><input name="quiz-types" type="radio" value="<?= $k ?>"
    <?php if ($k===0) echo "checked"; ?>
    ><?php
    echo htmlspecialchars($v["name"]);
    ?></label><?php
}
?></div><?php
?>
