<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');

	function search_GET($limit=50,&$max_size=NULL) {
		$db = defaultDB();
		if (!array_key_exists("lang", $_GET) or !(
			$langs = vec_norm(explode(",", $_GET["lang"]), "trim")
			))
			{ $langs = ['la']; }

		if (!array_key_exists("name", $_GET) or !(
			$names = vec_norm(explode(",", $_GET["name"]), "trim")
			))
			{ $names = NULL; }

		if (!array_key_exists("form", $_GET) or !(
			$forms = vec_norm(explode(",", $_GET["form"]), "trim")
			))
			{ $forms = NULL; }

		if (!array_key_exists("spart", $_GET) or !(
			$sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
			))
			{ $sparts = NULL; }

		if (!array_key_exists("attr", $_GET) or !(
			$attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
			))
			{ $attrs = []; }

		if (!array_key_exists("id", $_GET) or !(
			$ids = vec_norm(explode(",", $_GET["id"]), "trim")
			))
			{ $ids = NULL; }

		$no_definitions = safe_get("no_definitions", $_GET) === "true";
		$no_templates = !(safe_get("show_templates", $_GET) === "true");
		$start = intval(safe_get("start",$_GET));
		$_ = $limit;
		$limit = intval(safe_get("limit",$_GET));
		if ($limit <= 0) $limit = 5;
		if ($limit > $_) $limit = $_;

		if ($ids === NULL) {
			$searcher = $db->searcher();
			if ($names)
				$searcher = $searcher->name($names);
			if ($forms)
				$searcher = $searcher->form($forms);
			if ($langs)
				$searcher = $searcher->lang($langs);
			if ($sparts)
				$searcher = $searcher->spart($sparts);
			if ($no_definitions)
				$searcher = $searcher->no_definitions();
			if ($no_templates) $attrs[] = "!template";
			foreach ($attrs as $attr) {
				if (!$attr) continue;
				$a = NULL;
				if ($reverse = (substr($attr, 0, 1) === "!")) {
					$attr = substr($attr, 1);
				}
				if (strpos($attr,"=") === FALSE)
					$a = ATTR($attr);
				else {
					list ($name,$value) = explode("=",$attr,2);
					$a = ATTR($name,$value);
				}
				if ($a !== NULL) {
					if (!$reverse)
						$searcher = $searcher->only_with_attr($a);
					else
						$searcher = $searcher->only_without_attr($a);
				}
			}
			$max_size = $searcher->max_size();
			$list = $searcher->limit($start, $limit)->all("name");
			if (count($list) === $max_size)
				$max_size = NULL;
		} else {
			$max_size = NULL;
			$list = [];
			foreach ($ids as $id)
				$list[] = WORD(defaultDB(), intval($id));
		}
		return $list;
	}
?>
