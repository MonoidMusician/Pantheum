<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
include_once('quiz_types.php');
global $quiz_types;

$type = array_key_exists("type",$_GET) ? $_GET["type"] : NULL;
if ($type !== NULL
        and array_key_exists($type, $quiz_types)
        and is_array($quiz_types[$type])
        and array_key_exists("options", $quiz_types[$type])
        and is_array($quiz_types[$type]["options"])) {
    $_SESSION["quiz_type"] = $type;
    print "success";
} else {
    if (array_key_exists($type, $quiz_types)
        and is_array($quiz_types[$type])
        and array_key_exists("name", $quiz_types[$type]))
        $type = $quiz_types[$type]["name"];
    print "Cannot find the type of quiz: \"$type\", or it was not valid";
}
?>
