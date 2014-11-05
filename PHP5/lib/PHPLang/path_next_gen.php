<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

class PathNextGen implements Countable
{
    public $tree = [];
    public $soil;
    
	function __construct($init, $path)
	{
	    if (ISWORD($init))
	    {
	        
	    }
	}
}
