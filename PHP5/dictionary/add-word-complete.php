<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/misc.php');
	sro('/PHP5/lib/PHPLang/templates.php');

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

	$definitions = safe_get("definitions", $_GET);
	$connections = safe_get("connections", $_GET);
	$forms = safe_get("forms", $_GET);

	if (!requireRank(3, FALSE)) echo "Insufficient permissions";
	else
	if ($langs and count($langs) == 1 and
	    $names and count($names) == 1 and
	    $sparts and count($sparts) == 1) {
		$w = defaultDB()->searcher()->name($names[0])->spart($sparts[0])->lang($langs[0])->all();
		if (count($w)) exit("Word seems to be already added. <a href='/dictionary.php?lang={$langs[0]}&spart={$sparts[0]}&name={$names[0]}' target='_blank'>See it</a>");
		if (!$definitions or !count($definitions)) exit("Please enter at least one definition");
		sql_exec($sql_stmts["word_lang,word_name,word_spart->new in words"], ["sss", $langs[0],$names[0],$sparts[0]]);
		$w = defaultDB()->searcher()->name($names[0])->spart($sparts[0])->lang($langs[0])->all();
		if (count($w) === 1) {
			$w = $w[0];
			foreach ($attrs as $a) {
				error_log($a);
				$a = explode("=",$a,2);
				if (count($a) === 2) {
					$w->add_attr(ATTR($a[0],$a[1]));
				}
			}
			foreach ($definitions as $d) {
				$dd = DEFINITION(defaultDB(), NULL, $w);
				$dd->set_lang("en");
				$dd->set_value($d);
				$w->add_definition($dd);
			}
			foreach ($connections as $c) {
				list($type,$other,$mutual) = $c;
				if (!$type or !$other) continue;
				$other = intval($other);
				$cc = CONNECTION($w, WORD(defaultDB(),$other), $type);
				$w->add_connection($cc);
				if ($mutual) {
					$cc = CONNECTION(WORD(defaultDB(),$other), $w, $type);
					WORD(defaultDB(),$other)->add_connection($cc);
				}
			}
			$ignore = []; $changes = []; $path = "";
			if ($sparts[0] === "verb") {
				$template = $_GET["conjugation"] . " " . $_GET["voice"];
				if ($_GET["person"] === "impersonal") {
					$ignore[] = "person-1";
					$ignore[] = "person-2";
				} else if ($_GET["person"] === "impersonal-passive") {
					$ignore[] = "person-1/passive";
					$ignore[] = "person-2/passive";
				}
				$w->add_attr(ATTR("conjugation"), $_GET["conjugation"]);
			} else if ($sparts[0] === "noun") {
				$template = $_GET["declension"];
				$path = $_GET["gender"];
				if ($_GET["number"] === "s") {
					$ignore[] = "plural";
				} else if ($_GET["number"] === "pl") {
					$ignore[] = "singular";
				} else if ($_GET["number"] === "pls") {
					$ignore[] = "singular";
					$changes["plural"] = "singular";
				}
				$w->add_attr(ATTR("declension", $_GET["declension"]));
			} else if ($sparts[0] === "adjective") {
				$template = $_GET["adj-decl"];
				if ($_GET["comparison"] === "adj-uncomparable") {
					$ignore[] = "comparative";
					$ignore[] = "superlative";
				}
				$w->add_attr(ATTR("declension", $_GET["adj-decl"]));
			} else if ($sparts[0] === "adverb") {
				if ($_GET["adv-decl"] === "uncomparable") {
					$p = PATH($w);
					$p->set($forms[0]);
					$w->add_path($p);
				} else {
					$suff0 = [
						"irregular" => "",
						"adv-e" => "ē", // ee
						"adv-o" => "ō", // oo
						"adv-um" => "um",
						"adv-iter" => "iter",
						"adv-er" => "er"
					];
					$suff0 = $suff0[$_GET["adv-decl"]];
					$p = PATH($w,"positive");
					$p->set($forms[0].$suff0);
					$w->add_path($p);
					$p = PATH($w,"comparative");
					$p->set($forms[1]."us");
					$w->add_path($p);
					$p = PATH($w,"superlative");
					$p->set($forms[2]."imē"/*imee*/);
					$w->add_path($p);
				}
				exit("success");
			} else {
				$p = PATH($w);
				$p->set($names[0]);
				exit("success");
			}
			$ignore = array_map(function($a) {
				return explode("/", $a);
			}, $ignore);
			// Template: passed by name, spart from word, attr template=true
			$t = defaultDB()->searcher();
			$t = $t->spart($w->speechpart());
			$t = $t->name($template);
			$t = $t->only_with_attr(ATTR("template", "true"));
			$t = $t->all();
			//error_log(var_export(array_map(function($a){return$a->id();},$t),1));
			if (count($t) === 0) exit("Could not find template with name: ".$template);
			elseif (count($t) !== 1) exit("Ambiguous template name (please remove duplicate template(s))");
			if ($path === "common") {
			if (($s=run_template($w, PATH($w,"feminine"), $t[0], $forms, $ignore, $changes, FALSE)) === NULL) {
				if (($s=run_template($w, PATH($w,"masculine"), $t[0], $forms, $ignore, $changes, FALSE)) === NULL) {
					exit("success");
				} else exit("Template did not run successfully: $s");
			} else exit("Template did not run successfully: $s");
			} else
			if (($s=run_template($w, PATH($w,$path), $t[0], $forms, $ignore, $changes, FALSE)) === NULL) {
				exit("success");
			} else exit("Template did not run successfully: $s");
		}
		exit("Could not find word");
	} else exit("Bad \$_GET");
?>
