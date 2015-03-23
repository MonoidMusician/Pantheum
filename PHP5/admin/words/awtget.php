<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    sro('/PHP5/lib/PHPLang/display.php');
    
    requireRank('1');
    
    global $mysqli;
    
    $M_query = "SELECT * FROM words;";
    $M_result = $mysqli->query($M_query);
    while ($M_row = $M_result->fetch_assoc()) {
        $id = $M_row['word_id'];
        $name = $M_row['word_name'];
        $spart = format_spart($M_row['word_spart']);
        
        print "$id{(,)}$name{(,)}$spart{[,]}";
    }
?>
