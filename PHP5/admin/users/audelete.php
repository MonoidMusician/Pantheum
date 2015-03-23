<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('1');
    
    global $suid, $mysqli;
    
    $uid = cleanInput('/[^0-9]/', $_POST['keyfield']);
    if ("$uid" == "$suid") {
        logEvent("audelete", "current-user", encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'} : $uid == $suid"));
        die("Cannot delete current user.");
    } else {
        if ($uid == '1') {
            logEvent("audelete", "admin-user", encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'} : $uid, $suid"));
            die("Cannot delete user.");
        } else {
            $M_query = "SELECT * FROM users WHERE id='$uid';";
            $M_result = $mysqli->query($M_query) or die("error");
            $M_count = $M_result->num_rows;
            if ($M_count == 1) {
                $M_row = $M_result->fetch_assoc();
                $toAdd = encodeHex("users: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}");
                $M_query1 = "DELETE FROM users WHERE id='$uid'";
                $M_result1 = $mysqli->query($M_query1);
                $M_query2 = "INSERT INTO deleted (fid, data) VALUES ('$uid', '$toAdd')";
                $M_result2 = $mysqli->query($M_query2);
                logEvent("audelete", "success", encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'} : $uid,  $suid, M_query: `$M_query`, M_count: `$M_count`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, toAdd: $toAdd, M_query1: $M_query1, M_query2: $M_query2"));
                print "success";
            } else {
                logEvent("audelete", "no-user", encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'} : $uid,  $suid, M_query: `$M_query`, M_count: `$M_count`"));
                die("No such user");
            }
        }
    }
?>
