<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<h2>Overview</h2>

Username: <?= $suname ?>
<br>
Rank: <?= getNamedRank($srank) ?>
