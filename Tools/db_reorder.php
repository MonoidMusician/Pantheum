<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
	requireRank(1);
?>

<!DOCTYPE html>
<html>
	<head>
		<?php sro('/Includes/head.php'); ?>
	</head>
	<body>

<?php
	sro('/PHP5/lib/PHPLang/sql_stmts.php');


	$start = microtime(true);

$name = "word";
$column = "word_id"; $table = $name."s";
$order = "word_lang, word_name";
$table2 = $table."2";
$table2 = $table;
$e = TRUE;

global $mysqli;
$max_id = NULL;
$ids = NULL;
$names = NULL;

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

$stmt = $mysqli->prepare("SELECT $column FROM $table2 ORDER BY $order");
sql_getmany($stmt, $ids, []);
$stmt->close();

$stmt = $mysqli->prepare("SELECT word_name FROM $table2 ORDER BY $order");
sql_getmany($stmt, $names, []);
$stmt->close();

$safe = $max_id + 1;
if ($safe < 1000) $safe = 1000;
echo $max_id.",".$safe."<br>";
foreach ($names as $id => $name) {
	//if($e)echo ($id+1)." “".$name."”<br>";
}
if($e)echo "<br>";
$last = 0;

function move($old,$new,$table2,$column) {
	global $mysqli;
	$stmt = $mysqli->prepare("UPDATE $table2 SET $column = ? WHERE $column = ?");
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	sql_exec($stmt, ["ii",$new,$old]);
	$stmt->close();
}
function move2($old,$new,$table2,$column,$safe) {
	global $mysqli;
	move($old,$safe,$table2,$column);
	$stmt = $mysqli->prepare("UPDATE $table2 SET $column = $column + 1 WHERE $column >= ? AND $column < ?");
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	sql_exec($stmt, ["ii",$new,$old]);
	$stmt = $mysqli->prepare("UPDATE $table2 SET $column = ? WHERE $column = ?");
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	sql_exec($stmt, ["ii",$new,$safe]);
	$stmt->close();
}
var_dump($ids);

/*for ($new=1;$new<=count($ids);$new++) {
	$old = $ids[$new-1];
	if ($new !== $old) {
		if ($new > $old) echo "promote: ";
		if (($s = array_search($new,$ids)) !== FALSE) {
			$ids[$s] = $old;
			echo "$old ~> $new<br> ";
			move2($old,$new,$table2,$column,$safe);
			/*move($new,$safe,$table2,$column);
			move($old,$new,$table2,$column);
			move($safe,$old,$table2,$column);* /
		} else {
			echo "$old --> $new<br> ";
			move($old,$new,$table2,$column);
		}
	} else echo "$old in place<br>";
}*/
$stmt = $mysqli->prepare("UPDATE $table2 SET $column = $column+?");
if (!$stmt) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
sql_exec($stmt, ["i",$safe]);
$stmt->close();
for ($new=1;$new<=count($ids);$new++) {
	$old = $ids[$new-1]+$safe;
	move($old,$new,$table2,$column);
}
var_dump($ids);


$stmt = $mysqli->prepare("SELECT word_id,word_name FROM $table2 ORDER BY word_id");
$stmt->execute();

$id = $name = NULL;
$stmt->bind_result($id,$name);
while($stmt->fetch()) {
	if($e)echo "$id “".$name."”<br>";
}
$stmt->close();

	$time = microtime(true) - $start;
	$time = round($time, 1);
	echo "<br><hr>Took $time seconds";
?>

	</body>
</html>
