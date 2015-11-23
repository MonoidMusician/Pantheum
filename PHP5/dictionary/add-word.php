<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');

	if (!array_key_exists("lang", $_GET) or !(
		$langs = vec_norm(explode(",", $_GET["lang"]), "trim")
		))
		{ $langs = ['la']; }

	if (!array_key_exists("name", $_GET) or !(
		$names = vec_norm(explode(",", $_GET["name"]), "trim")
		))
		{ $names = NULL; }

	if (!array_key_exists("spart", $_GET) or !(
		$sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
		))
		{ $sparts = NULL; }

	if (!array_key_exists("attr", $_GET) or !(
		$attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
		))
		{ $attrs = []; }

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if ($langs and count($langs) == 1 and
	    $names and count($names) == 1 and
	    $sparts and count($sparts) == 1) {
		$w = defaultDB()->searcher()->name($names[0])->spart($sparts[0])->lang($langs[0])->all();
		if (count($w)) {
			echo "Word seems to be already added";
		} else {
			sql_exec(sql_stmt("word_lang,word_name,word_spart->new in words"), ["sss", $langs[0],$names[0],$sparts[0]]);
			$w = defaultDB()->searcher()->name($names[0])->spart($sparts[0])->lang($langs[0])->all();
			if (count($w) === 1) {
				$w = $w[0];
				foreach ($attrs as $a) {
					$a = explode("=",$a,2);
					if (count($a) === 2) {
						$w->add_attr(ATTR($a[0],$a[1]));
					}
				}
			}
			exit("success");
		}
	} else exit("Bad \$_GET");
?>
