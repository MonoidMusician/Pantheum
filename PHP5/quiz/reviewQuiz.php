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
if ($id !== NULL and
    QUIZ($id)->is_authorized()) {
	echo json_encode(QUIZ($id)->data());
} else {
	if (array_key_exists($type, $quiz_types) and
	    is_array($quiz_types[$type]) and
	    array_key_exists("name", $quiz_types[$type]))
		$type = $quiz_types[$type]["name"];
	print "Cannot find the type of quiz: \"$type\", or it was not valid, or you are not the correct user";
}
?>
