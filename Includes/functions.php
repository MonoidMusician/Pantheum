<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions/input.php');
    sro('/Includes/functions/code.php');
    sro('/Includes/functions/email.php');
    sro('/Includes/functions/users.php');

    function requireRank($rank, $die=TRUE) {
        global $srank;

        if (!requireLoggedIn($die)) return FALSE;;

        if ($die and $srank > $rank) {
            sro('/Pages/restricted/admin.php');
            die("");
        } else return $srank <= $rank;
    }

    function requireLoggedIn($die) {
        global $sli;

        if ($die and $sli != 'true') {
            sro('/Pages/restricted/logged-out.php');
            die("");
        } else return $sli == 'true';
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

    function hasher($text) {
        return strtolower(hash('sha512', strtolower(hash('whirlpool', strtolower(hash('md5', strtolower(hash('md5', strtolower(hash('whirlpool', strtolower(hash('sha512', $text))))))))))));
    }
?>
