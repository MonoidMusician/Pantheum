<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
print $sli ? "Logged in" : "Logged out";
?><br><?php
print "User #".$suid.' "'.$suname.'"';
var_dump($_SESSION);
?>
