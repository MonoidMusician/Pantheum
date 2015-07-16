<?php
		require_once('/var/www/config.php');
		sro('/Includes/mysql.php');
		sro('/Includes/session.php');
		sro('/Includes/functions.php');

		sro('/PHP5/lib/PHPLang/db.php');
		sro('/PHP5/lib/PHPLang/misc.php');
		sro('/PHP5/lib/PHPLang/templates.php');
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
</head><body><?php
		function add_word($name, $spart) {
			global $sql_stmts;
			$w = defaultDB()->searcher()->name($name)->spart($spart)->lang("eo")->all();
			if (count($w)) {
				echo "Word seems to be already added: $name, $spart<br>";
				if (count($w) === 1) {
					$w = $w[0];
					if ($w->read_paths()) echo "- Had inflection already<br>";
					else return $w;
				} else echo "- More than one found, continuing<br>";
			} else {
				echo "Adding word: $name, $spart<br>";
				sql_exec($sql_stmts["word_lang,word_name,word_spart->new in words"], ["sss", "eo",$name,$spart]);
				$w = defaultDB()->searcher()->name($name)->spart($spart)->lang("eo")->all();
				if (count($w) === 1) {
					$w = $w[0];
					return $w;
				}
			}
		}
		function get_template($spart) {
			return safe_get(0,defaultDB()->searcher()->spart($spart)->name($spart)->only_with_attr(ATTR("template", "true"))->all());
		}
		function run_template2($word,$t,$arg,$definition) {
			if (!$ignore) $ignore = [];
			if ($word !== NULL and $definition) {
				$d = DEFINITION(defaultDB(), NULL, $word);

				$d->set_lang("en");
				$d->set_value($definition);
				$d = $word->add_definition($d);
			}
			if ($word !== NULL and $t !== NULL)
				run_template($word,"",$t,$arg,[],[],FALSE);
		}
		run_template2(add_word("Angla","adjective"), get_template('adjective'), ["Angl"], "English");
		
		run_template2(add_word("ĝuste","adverb"), NULL, ["ĝust"], "Precisely");
		run_template2(add_word("ĉar","conjunction"), NULL, ["ĉar"], "Since");
		run_template2(add_word("Aeropago","noun"), get_template('noun'), ["Aeropag"], "Areopagus");
		run_template2(add_word("ĵuri","verb"), get_template('verb'), ["ĵur"], "Swear (jud.)");
		run_template2(add_word("ĉiam","preposition-conjunction"), NULL, ["ĉiam"], "Always");
		echo "DONE!!";
