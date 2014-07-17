<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('0');
    
    print_r($_GET);
    print_r($_POST);
?>
