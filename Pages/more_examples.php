<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');

$query = [];
parse_str($_SERVER["QUERY_STRING"], $query);
$f = function(){
	global $sql_stmts;
	$w = RWORD(defaultDB());
	?>
	<sup>[<?= $w->lang() ?>]</sup>
	<b><?= $w->name() ?></b>
	(<?= $w->speechpart() ?>)
	<br/>
	<?php
};
if (!array_key_exists("n", $query))
	$query["n"] = 1;
$n = $query["n"];
if ($n == 1) $pl = ""; else $pl = "s";
echo "<h3>Showing $n result$pl ";
if ($n < 50) {
?>(<a href="?n=<?= $n+1 ?>">More</a> <?php
}
if ($n > 1) {
?> <a href="?n=<?= $n-1 ?>">Less</a>)<?php
}
echo "</h3>";

for ($i=0;$i<$n;$i+=1)
	$f();
?>
