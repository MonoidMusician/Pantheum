<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/sql_stmts.php');

$GLOBALS['data_dir'] = '/var/www/Data/';

##
# Database interface.
#
interface _DB
{
	function find_all_words($name=NULL, $lang=NULL, $id=NULL);
	function add_mgr($lang, $speechpart, $depath);
	function get_mgr($lang, $speechpart);
	function add_mgrW($w, $depath);
	function get_mgrW($w);
	function is_lang($lang);
	function langs();
}

include('db_sql.php');
include('db_php.php');
include('db_depaths.php');

function ISDB($obj) {
	return $obj instanceof _DB;
}
function ISSQLDB($obj) {
	return $obj instanceof _SQLDB;
}
function ISPHPDB($obj) {
	return $obj instanceof _PHPDB;
}
sro('/PHP5/lib/PHPLang/depath.php');
sro('/PHP5/lib/PHPLang/word.php');
function DB($sql=FALSE) {
	if ($sql)
		return new _SQLDB();
	else
		return new _PHPDB();
}
global $SQLDB;
$SQLDB = DB(TRUE);
#echo "\$SQLDB:\n";
#var_dump($SQLDB);
function defaultDB(){global$SQLDB;return$SQLDB;}
?>
