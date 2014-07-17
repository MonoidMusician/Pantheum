<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    global $mysqli;
    
    requireRank('0');
    
    $tid = cleanInput('/[^0-9]/', $_POST['keyfield']);
    
    $M_query = "SELECT * FROM tournaments WHERE id='$tid';";
    $M_result = $mysqli->query($M_query);
    $M_count = $M_result->num_rows;
    if ($M_count == 1) {
        $M_row = $M_result->fetch_assoc();
        $toAdd = encodeHex("tournaments: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}");
        $M_query1 = "DELETE FROM tournaments WHERE id='$tid'";
        $M_result1 = $mysqli->query($M_query1);
        $M_query2 = "INSERT INTO deleted (fid, data) VALUES ('$tid', '$toAdd')";
        $M_result2 = $mysqli->query($M_query2);
        logEvent("atdelete", "success", encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'} : $tid,  $suid, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, toAdd: $toAdd, M_query1: $M_query1, M_query2: $M_query2"));
        print "success";
    } else {
        logEvent("atdelete", "no-tournament", encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'} : $tid,  $suid, M_query: `$M_query`, M_count: `$M_count`"));
        die("No such tournament.");
    }
?>
