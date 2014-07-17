<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    global $mysqli;
    
    requireRank('0');
    
    $M_query = "SELECT * FROM tournaments;";
    $M_result = $mysqli->query($M_query);
    while ($M_row = $M_result->fetch_assoc()) {
        $id = $M_row['id'];
        $title = $M_row['title'];
        $location = $M_row['location'];
        $organ = $M_row['organization'];
        $short = $M_row['short_description'];
        $privacy = ucfirst($M_row['privacy']);
        $sub = ucfirst($M_row['subscription']);
        
        print "$id{(,)}$title{(,)}$location{(,)}$organ{(,)}$short{(,)}$privacy{(,)}$sub{[,]}";
    }
?>
