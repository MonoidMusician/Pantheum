<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    global $mysqli, $sudata;
    
    if (!isset($sli) || $sli != 'true') {
        logEvent('udata-set', 'logged-out', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
        die('4');
    } else {
        $key = cleanInput('/[^a-zA-Z0-9_]/', $_POST['key']);
        $value = $_POST['value'];
        if (($key != $_POST['key']) || ($key == '')) {
            logEvent('udata-set', 'blank-input', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
            die('3');
        }
        
        $M_query = "SELECT * FROM users WHERE username='$suname';";
        $M_result = $mysqli->query($M_query);
        $M_count = $M_result->num_rows;
        
        if ($M_count == 1) {
            $M_row = $M_result->fetch_assoc();

            $udata = $M_row['udata'];
            if ($udata === null || $udata === "null")
                $udata = "{}";
            $udata = json_decode($udata,true);
            $udata[$key] = $value;
            $udata = json_encode($udata);
            
            $M_query6 = "UPDATE users SET udata=? WHERE id=?;";
            $stmt = $mysqli->prepare($M_query6);
            $stmt->bind_param("si", $udata, $M_row["id"]);
            $M_result6 = $stmt->execute();
            
            if ($M_result6) {
                logEvent('udata-set', 'success', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_query6: `$M_query6`"));
                $sudata = $_SESSION["udata"] = $udata;
                print "success";
            } else {
                logEvent('udata-set', 'misc-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_query6: `$M_query6`"));
                die('1');
            }
        } else {
            logEvent('udata-set', 'no-user', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`"));
            die('2');
        }
    }
?>
