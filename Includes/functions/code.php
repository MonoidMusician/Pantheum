<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    
    function createCode() {
        if ((!isset($_SESSION['lcode'])) || ($_SESSION['lcode'] == '') || ($_SESSION['lcode'] < (time()-5))) {
            $ct = time();
            $ctext = randomString(32);
            $code = hash('md5', $_SERVER['REMOTE_ADDR'] . $ct);
            setcookie('ctoken', hash('sha512', $ctext . $_SERVER['REMOTE_ADDR'] . $ct), 0, '/');
            $_SESSION['code'] = array($_SERVER['REMOTE_ADDR'], $ct, $ctext, $code);
            $_SESSION['lcode'] = $ct;
            return $code;
        } else {
            return 'xx';
        }
    }

    function checkValidCode($code) {
        if ((isset($_SESSION['code'])) && ($_SESSION['code'] != '')) {
            $ccode = $_COOKIE['ctoken'];
            $realccode = hash('sha512', $_SESSION['code'][2] . $_SESSION['code'][0] . $_SESSION['code'][1]);
            $realcode = $_SESSION['code'][3];
            $expire = $_SESSION['code'][1] + 60*15;
            $ip = $_SESSION['code'][0];
            $ct = time();
            setcookie('ctoken', '', 0, '/');
            $_SESSION['code'] = "";
            if ($ct < $expire) {
                if ($ip == $_SERVER['REMOTE_ADDR']) {
                    if ($ccode == $realccode) {
                        if ($code == $realcode) {
                            return 0;
                        } else {
                            return 4;
                        }
                    } else {
                        return 3;
                    }
                } else {
                    return 2;
                }
            } else {
                return 1;
            }
        } else {
            error_log(json_encode($_SESSION));
            return 5;
        }
    }

    function randomString($length = 15, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $rstring = "";
        for ($i = 0; $i < $length; $i++) {
            $rstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $rstring;
    }
?>
