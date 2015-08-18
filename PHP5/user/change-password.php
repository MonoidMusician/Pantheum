<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    global $mysqli;
    
    if (!isset($sli) || $sli != 'true') {
        logEvent('pswd-change', 'logged-out', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
        die('6');
    } else {
        $username = cleanInput('/[^a-zA-Z0-9]/', $_POST['u']);
        $password = cleanInput('/[^a-zA-Z0-9]/', $_POST['p']);
        $newpassw = cleanInput('/[^a-zA-Z0-9]/', $_POST['n1']);
        $newpass2 = cleanInput('/[^a-zA-Z0-9]/', $_POST['n2']);
        if (($username != $_POST['u']) || ($username == '') || ($password == '') || ($newpassw == '') || ($newpass2 == '')) {
            logEvent('pswd-change', 'blank-input', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
            die('5');
        }
        if (($newpassw != $newpass2)) {
            logEvent('pswd-change', 'mismatched-passwords', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
            die('4');
        }
        
        $M_query = "SELECT * FROM users WHERE username='$username';";
        $M_result = $mysqli->query($M_query);
        $M_count = $M_result->num_rows;
        
        if ($M_count == 1) {
            $M_row = $M_result->fetch_assoc();
            
            if (strlen($password) != strlen(hash('md5', 'pi'))) {
                $password = strtolower(hash('md5', hasher(hasher($_POST['p'])) . hasher(hasher($username))));
            }
            $password = strtolower(hash('md5', hasher(hasher($M_row['createip'] . $password . $M_row['id']))));
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $current = $ip;
            
            if ($M_row['password'] == $password) {
                if (strlen($newpassw) != strlen(hash('md5', 'pi'))) {
                    $newpassw = strtolower(hash('md5', hasher(hasher($_POST['p'])) . hasher(hasher($username))));
                }
                $newpassw = strtolower(hash('md5', hasher(hasher($M_row['createip'] . $newpassw . $M_row['id']))));
                
                $M_query6 = "UPDATE users SET password='$newpassw' WHERE id='" . $M_row['id'] . "';";
                $M_result6 = $mysqli->query($M_query6);
                
                if ($M_result6) {
                    logEvent('pswd-change', 'success', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_query6: `$M_query6`"));
                    print "success";
                } else {
                    logEvent('pswd-change', 'misc-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_query6: `$M_query6`"));
                    die('1');
                }
            } else {
                logEvent('pswd-change', 'bad-password', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, password: `$password`, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
                die('2');
            }
        } else {
            logEvent('pswd-change', 'no-user', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`"));
            die('3');
        }
    }
?>