<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions/input.php');
    sro('/Includes/functions/code.php');
    sro('/Includes/functions/email.php');
    sro('/Includes/functions/users.php');

    function requireRank($rank) {
        global $srank;
        if ($srank != $rank) {
            sro('/Pages/restricted/admin.php');
            die("");
        }
    }
    
    function requireLoggedIn() {
        global $sli;
        
        if ($sli != 'true') {
            sro('/Pages/restricted/logged-out.php');
            die("");
        }
    }
    
    function canCreate() {
        global $srank;
        if ($srank <= 2) {
            return true;
        }
        return false;
    }
    
    function requireCreate() {
        global $srank;
        if ($srank >= 2) {
            sro('/Pages/restricted/admin.php');
            die("");
        }
    }
    
    function logEvent($script, $type, $content) {
        global $suid, $mysqli;
        $ip = $_SERVER['REMOTE_ADDR'];
        $ctime = time();
        $userid = '';
        if ((isset($suid)) && ($suid != '')) {
            $userid = $suid;
        }
        
        $M_query = "INSERT INTO logs (ip, time, userid, script, type, content) VALUES ('$ip', '$ctime', '$userid', '$script', '$type', '$content');";
        $M_result = $mysqli->query($M_query);
    }
    
    function checkBlacklist($ip) {
        global $mysqli;
        $M_query = "SELECT * FROM blacklist WHERE ip='$ip' ORDER BY id DESC LIMIT 1;";
        $M_result = $mysqli->query($M_query);
    }
    
    function hasher($text) {
        return strtolower(hash('sha512', strtolower(hash('whirlpool', strtolower(hash('md5', strtolower(hash('md5', strtolower(hash('whirlpool', strtolower(hash('sha512', $text))))))))))));
    }
?>
