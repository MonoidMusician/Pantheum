<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('1');
    
    global $mysqli;
    
    $M_query = "SELECT * FROM users;";
    $M_result = $mysqli->query($M_query);
    while ($M_row = $M_result->fetch_assoc()) {
        $id = $M_row['id'];
        $username = $M_row['username'];
        $email = $M_row['email'];
        
        $rank = getNamedRank($M_row['rank']);
        
        print "$id{(,)}$username{(,)}$rank{(,)}$email{[,]}";
    }
?>
