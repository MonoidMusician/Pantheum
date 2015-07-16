<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    global $mysqli;

    if ((isset($sli)) && ($sli == 'true')) {
        logEvent('signup', 'logged-in', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
        die('1');
    } else {
        $username = cleanInput('/[^a-zA-Z0-9]/', $_POST['u']);
        $password = cleanInput('/[^a-zA-Z0-9]/', $_POST['p']);
        $cpassword = cleanInput('/[^a-zA-Z0-9]/', $_POST['c']);
        $email = cleanInput('/[^a-zA-Z0-9\@\.\_\-]/', $_POST['e']);
        $code = cleanInput('/[^a-zA-Z0-9]/', $_POST['v']);
        $vc = checkValidCode($code);
        if ($vc == 0) {
            if (($username == $_POST['u']) && ($username != '') && ($password != '') && ($cpassword != '') && ($email != '') && ($code != '') && (count($_POST) == 5)) {
                $M_query = "SELECT * FROM users WHERE username='$username';";
                $M_result = $mysqli->query($M_query);
                $M_count = $M_result->num_rows;
                
                if ($M_count == 0) {
                    $M_querye = "SELECT * FROM users WHERE email='$email';";
                    $M_resulte = $mysqli->query($M_querye);
                    $M_counte = $M_resulte->num_rows;
                    if ($M_counte == 0) {
                        if ($password == $cpassword) {
                            $time = time()-60*3;
                            $ip = $_SERVER['REMOTE_ADDR'];
                            $M_query1 = "SELECT COUNT(*) FROM logs WHERE ip='$ip' AND script='signup' AND type!='success' AND type!='logged-in' AND type!='exists' AND time>$time;";
                            $M_result1 = $mysqli->query($M_query1);
                            $M_row1 = $M_result1->fetch_array();
                            
                            error_log(json_encode( $M_row1[0] ));
                            
                            if ((isset($M_row1[0])) && ($M_row1[0] <= 10)) {
                                if (strlen($password) != strlen(hash('md5', 'pi'))) {
                                    $password = strtolower(hash('md5', hasher(hasher($_POST['p'])) . hasher(hasher($username))));
                                }
                                
                                $id = '';
                                $join = time();
                                $seccode = hash('sha256', "$username$ip$createip");
                                
                                $M_query2 = "INSERT INTO users (username, password, email, createip, joindate, multisession, seccode, rank) VALUES ('$username', '$password', '$email', '$cip', '$join', 'f', '$seccode,$join', 'n')";
                                $M_result2 = $mysqli->query($M_query2);
                                if ($M_result2) {
                                    $M_query3 = "SELECT * FROM users WHERE username='$username' AND email='$email' AND createip='$cip' AND joindate='$join';";
                                    $M_result3 = $mysqli->query($M_query3);
                                    if ($M_result3) {
                                        $M_row3 = $M_result3->fetch_assoc();
                                        $password = strtolower(hash('md5', hasher(hasher($M_row3['createip'] . $password . $M_row3['id']))));
                                        $M_query4 = "UPDATE users SET password='$password' WHERE username='$username' AND email='$email' AND createip='$cip' AND joindate='$join' AND id='" . $M_row3['id'] . "';";
                                        $M_result4 = $mysqli->query($M_query4);
                                        if ($M_result4) {
                                            $sendResult = sendEmailTo("Tournament - Validation", $email, "Tournament - Please Validate Your Account", '<h3>Validation</h3><p>To validate your account, click here:<br><a href="https://localhost/latin/validation.php?id=' . $seccode . '">https://localhost/latin/validation.php?id=' . $seccode . '</a><br><br>If you received this email in error, you can safely disregard this email and no further communications will be sent to you by us.<br><br>Thanks,<br>The Tournament Team');
                                            if ($sendResult == 0) {
                                                logEvent('signup', 'success', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`, M_query1: `$M_query1`, M_query2: `$M_query2`, M_query3: `$M_query3`, M_query4: `$M_query4`, seccode: $seccode"));
                                                print "success";
                                            } else {
                                                logEvent('signup', 'email-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`, M_query1: `$M_query1`, M_query2: `$M_query2`, M_query3: `$M_query3`, M_query4: `$M_query4`, sendResult: $sendResult"));
                                                die('8');
                                            }
                                        } else {
                                            logEvent('signup', 'modify-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`, M_query1: `$M_query1`, M_query2: `$M_query2`, M_query3: `$M_query3`, M_query4: `$M_query4`"));
                                            die('7');
                                        }
                                    } else {
                                        logEvent('signup', 'select-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`, M_query1: `$M_query1`, M_query2: `$M_query2`, M_query3: `$M_query3`"));
                                        die('7');
                                    }
                                } else {
                                    logEvent('signup', 'create-error', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`, M_query1: `$M_query1`, M_query2: `$M_query2`"));
                                    die('7');
                                }
                            } else {
                                logEvent('signup', 'spamming', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`, M_query1: `$M_query1`"));
                                die('6');
                            }
                        } else {
                            logEvent('signup', 'password-mismatch', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_querye: '`$M_querye`"));
                            die('5');
                        }
                    } else {
                        $M_rowe = $M_resulte->fetch_assoc();
                        logEvent('signup', 'exists-email', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}, M_querye: `$M_querye`, M_rowe: ['" . implode("','", array_keys($M_rowe)) . "'], {'" . implode("', '", $M_rowe) . "'}"));
                        die('9');
                    }
                } else {
                    $M_row = $M_result->fetch_assoc();
                    logEvent('signup', 'exists-username', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}, M_query: `$M_query`, M_row: ['" . implode("','", array_keys($M_row)) . "'], {'" . implode("', '", $M_row) . "'}"));
                    die('3');
                }
            } else {
                logEvent('signup', 'blank-input', encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
                die('4');
            }
        } else {
            logEvent('signup', 'invalid-code-' . $vc, encodeHex("SESSION: ['" . implode("','", array_keys($_SESSION)) . "'], {'" . implode("', '", $_SESSION) . "'}, POST: ['" . implode("','", array_keys($_POST)) . "'], {'" . implode("', '", $_POST) . "'}"));
            die('2');
        }
    }
?>
