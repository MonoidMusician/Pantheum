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
?>
<?php
foreach ($quiz_types as $id=>$quiz_type) {
	$options = safe_get("user_selections", $quiz_type);
	if (!$options) continue;
	?><ul id="<?= $id ?>-selections"><?php
	foreach ($options as $optionid => $option) {
		$name = safe_get("name", $option);
		if (!$name) $name = $optionid;
		echo "<li>".$name.": ";
		$opts = safe_get("values", $option);
		if ($opts) {
			?><select data-selection="<?= $optionid ?>"><?php
			foreach ($opts as $optid => $opt) {
				if (is_integer($optid)) $optid = $opt;
				?><option value="<?= $optid ?>"><?= $opt ?></option><?php
			}
			?></select><?php
		}
	}
	?></ul><?php
}
?>


