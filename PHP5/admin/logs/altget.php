<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    global $mysqli;
    
    requireRank('1');
    
    $M_query = "SELECT * FROM logs;";
    $M_result = $mysqli->query($M_query);
    while ($M_row = $M_result->fetch_assoc()) {
        $id = $M_row['id'];
        $ip = $M_row['ip'];
        $time = $M_row['time'];
        $uid = $M_row['userid'];
        $script = $M_row['script'];
        $type = $M_row['type'];
        $content = $M_row['content'];
        
        print "$id{(,)}$ip{(,)}$time{(,)}$uid{(,)}$script{(,)}$type{(,)}$content{[,]}";
    }
?>
