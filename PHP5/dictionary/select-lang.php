<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/common.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');

	global $sql_stmts;
	$db = defaultDB();
	if (safe_get("lang", $_GET))
	{ $langs = [$_GET["lang"]]; }
	else { $langs = ['la']; }
	?><select><?php
	foreach ($db->langs() as $l) {
		$name = $l;
		sql_getone(sql_stmt("lang_id->#words"), $words, ["s", $l]);
		$c = count($words);
		if ($words < 10) continue;
		sql_getone(sql_stmt("lang_id->lang_dispname"), $name, ["s", $l]);
		?><option <?php
		if (in_array($l, $langs)) {
			?>selected<?php
		}
		?> value="<?= $l ?>" ><?= $name ?></option><?php
	}
	?></select><?php
