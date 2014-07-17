<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('0');
    
    global $mysqli;
    
    $M_query = "SELECT * FROM users;";
    $M_result = $mysqli->query($M_query);
    while ($M_row = $M_result->fetch_assoc()) {
        $id = $M_row['id'];
        $username = $M_row['username'];
        $email = $M_row['email'];
        $rank = 'None';
        switch ($M_row['rank']) {
            case '0':
                $rank = 'Admin';
                break;
            case '1':
                $rank = 'Moderator';
                break;
            case '2':
                $rank = 'Pro';
                break;
            case '3':
                $rank = 'Creator';
                break;
            case '4':
                $rank = 'User';
                break;
            case 'b0':
                $rank = 'Banned&nbsp;Admin';
                break;
            case 'b1':
                $rank = 'Banned&nbsp;Moderator';
                break;
            case 'b2':
                $rank = 'Banned&nbsp;Pro';
                break;
            case 'b3':
                $rank = 'Banned&nbsp;Creator';
                break;
            case 'b4':
                $rank = 'Banned&nbsp;User';
                break;
            case 'bn':
                $rank = 'Banned&nbsp;Account';
                break;
            case 'n':
                $rank = 'Not&nbsp;Approved';
                break;
        }
        
        print "$id{(,)}$username{(,)}$rank{(,)}$email{[,]}";
    }
?>
