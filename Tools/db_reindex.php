<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
	requireRank(1);

	sro('/PHP5/lib/PHPLang/sql_stmts.php');


	$start = microtime(true);

if ($_GET["type"] === "pron") {
	$name = "pronunciation";
	$column = "pron_id"; $table = $name."s"; $table2 = $table."2";
} elseif ($_GET["type"] === "form") {
	$name = "form";
	$column = "form_id"; $table = $name."s"; $table2 = $table."2";
} elseif ($_GET["type"] === "def") {
	$name = "definition";
	$column = "def_id"; $table = $name."s"; $table2 = $table."2";
}
if (!$name) exit("No valid table supplied");
$table2 = $table;
$e = TRUE;

global $mysqli;
$max_id = NULL;
$_ids = NULL;

if ($table2 != $table) {
	$stmt = $mysqli->prepare("DROP TABLE IF EXISTS $table2");
	sql_exec($stmt, []);
	$stmt->close();
	$stmt = $mysqli->prepare("CREATE TABLE $table2 like $table");
	sql_exec($stmt, []);
	$stmt->close();
	$stmt = $mysqli->prepare("INSERT $table2 SELECT * FROM $table");
	sql_exec($stmt, []);
	$stmt->close();
}

$stmt = $mysqli->prepare("SELECT MAX($column) FROM $table2");
sql_getone($stmt, $max_id, []);
$stmt->close();

$stmt = $mysqli->prepare("SELECT $column FROM $table2");
sql_getmany($stmt, $_ids, []);
$stmt->close();

$safe = $max_id + 1;
$ids = [];
foreach ($_ids as $id) {
	$ids[$id] = $id;
}
foreach ($ids as $id) {
	if($e)echo "$id ";
}
if($e)echo "<br>";
$last = 0;
for ($id=1;$id<=$max_id;$id++) {
	if (!array_key_exists($id,$ids)) {
		if($e)echo "$id missing<br> ";
	} else {
		if ($last !== NULL and $last !== $id-1) {
			$stmt = $mysqli->prepare("UPDATE $table2 SET $column = ? WHERE $column = ?");
			if (!$stmt) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
				echo "\nStatement was: " . var_export($value, 1);
			}
			sql_exec($stmt, ["ii",$last+1,$id]);
			$stmt->close();
			if($e)echo "$id -> ".($last+1)."<br> ";
		}
		$last += 1;
	}
}

echo "1 through $last";


	$time = microtime(true) - $start;
	$time = round($time, 1);
	echo "<br><hr>Took $time seconds";
?>
