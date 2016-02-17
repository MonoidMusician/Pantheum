<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
include_once('quiz_types.php');
global $quiz_types;

$id = array_key_exists("id",$_GET) ? $_GET["id"] : NULL;
if ($id === NULL) exit("Bad id");
$q = QUIZ($id);
if (!$q->is_authorized()) exit("You are not the correct user");
if (!$q->completed()) {
	$type = $q->type();
	if (!array_key_exists($type, $quiz_types))
		die("This quiz cannot be resumed anymore because it does not exist.");
}
echo json_encode(QUIZ($id)->data());
?>
