<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('1');
    
    $uid = cleanInput('/[^0-9]/', $_GET['uid']);
    
    global $mysqli;
    $fl = time() . ',*';
    $M_query = "UPDATE users SET forcelogout='$fl' WHERE id='" . $uid . "';";
    error_log($M_query);
    $M_result = $mysqli->query($M_query);
    if ($M_result) {
        logEvent('users', 'force-logout', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, GET: ['" . implode("','", array_keys($_GET)) . "'], {'" . implode("', '", $_GET) . "'}, M_query: `$M_query`"));    
        print "success";
    } else {
        die($mysqli->error);
    }
?>
