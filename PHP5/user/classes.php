<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/sql_stmts.php');

global $mysqli;

$stmt = $mysqli->prepare("
	SELECT class_id, class_name FROM classes
	WHERE NOT hidden
");
if (!$stmt) {
	_die("Prepare failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error);
	return $result;
}
$stmt->bind_result($id, $name);
if (!$stmt->execute()) {
	_die("Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error);
	return $result;
}
while ($stmt->fetch()) {
	?> <label><input name="signupclass" type="radio" value="<?= $id ?>"><?= $name ?></label><br> <?php
}
?>
