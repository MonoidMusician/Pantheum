<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/misc.php');
	sro('/PHP5/lib/PHPLang/templates.php');

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if (array_key_exists("id",$_GET) and
	    array_key_exists("path",$_GET) and
	    array_key_exists("template",$_GET) and
	    is_numeric($_GET["id"])) {
		$n=0; $arg=[];
		while (array_key_exists($n,$_GET)) {
			$arg[] = $_GET[$n];
			$n+=1;
		}
		if (array_key_exists("ignore", $_GET)) {
			$ignore = explode(",", implode(",", explode(";", $_GET["ignore"])));
		} else $ignore = [];
		$change = [];
		if (array_key_exists("change", $_GET)) {
			$_change = explode(";", $_GET["change"]);
			foreach ($_change as $str) {
				list ($key,$value) = explode(",", $str);
				$change[$key] = $value;
			}
		}
		if (array_key_exists("overwrite", $_GET)) {
			$overwrite = TRUE;
		} else $overwrite = FALSE;
		// Word as passed by id
		$w = WORD(defaultDB(),intval($_GET["id"]));
		// Path as passed as string
		$p = PATH($w, $_GET["path"]);
		// Template: passed by name, spart from word, attr template=true
		$t = defaultDB()->searcher();
		$t = $t->spart($w->speechpart());
		$t = $t->name($_GET["template"]);
		$t = $t->only_with_attr(ATTR("template", "true"));
		$t = $t->all();
		if (count($t) === 0) echo "Could not find template with name: ".$_GET["template"];
		elseif (count($t) !== 1) echo "Ambiguous template name (please remove duplicate template(s))";
		else {
			$t = $t[0];
			if (($s=run_template($w, $p, $t, $arg, $ignore, $change, $overwrite)) === NULL) {
				exit("success");
			} else exit("Template did not run successfully: $s");
		}
	} else exit("\$_GET was invalid (".var_export($_GET,1).")");
?>
