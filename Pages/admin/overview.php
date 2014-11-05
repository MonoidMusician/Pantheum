<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    global $mysqli;
?>
<h2>Overview</h2>
<h3>Stats:</h3>
<p>
    Users: 
        <?php
            $M_query1 = 'SELECT COUNT(*) FROM users;';
            $M_result1 = $mysqli->query($M_query1);
            $M_row1 = $M_result1->fetch_row();
            echo $M_row1[0];
        ?>
    <br>
    Words: 
        <?php
            $M_query2 = 'SELECT COUNT(*) FROM words;';
            $M_result2 = $mysqli->query($M_query2);
            $M_row2 = $M_result2->fetch_row();
            echo $M_row2[0];
        ?>
    <br> 
    Log Events:
        <?php
            $M_query3 = 'SELECT COUNT(*) FROM logs;';
            $M_result3 = $mysqli->query($M_query3);
            $M_row3 = $M_result3->fetch_row();
            echo $M_row3[0];
        ?>
    <br>
    Deleted:
        <?php
            $M_query4 = 'SELECT COUNT(*) FROM deleted;';
            $M_result4 = $mysqli->query($M_query4);
            $M_row4 = $M_result4->fetch_row();
            echo $M_row4[0];
        ?>
</p>

