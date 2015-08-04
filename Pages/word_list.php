<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/sql_stmts.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/lib/PHPLang/misc.php');

	global $mysqli;

	$w = defaultDB()->searcher()->lang('la')->only_without_attr(ATTR('template'));
	$ws = $w->all();
	?><table class="inflection"><?php
	foreach ($ws as $i => $word) {
		?><tr><td><ol start="<?php
		echo $i+1;
		?>"><li><a class="word-ref format-word-<?php
		echo $word->lang();
		?>" href="dictionary.php?id=<?php
		echo $word->id();
		?>"><?php
		echo $word->name();
		?></ol></td><td style="border-spacing: 0px; padding: 0px;"><?php
		display_definitions($word);
		?></td><td style="border-spacing: 0px; padding: 0px;"><?php
		$infos = [];
		foreach ($word->read_attrs() as $attr) {
			$infos[] = format_attr($attr->tag(), $attr->value());
		}
		echo implode(", ",$infos);
		?></td></tr><?php
		unset($ws[$i]);
	}
	?></table>
	<script>
		æ.format();
	</script>
	<?php
?>
