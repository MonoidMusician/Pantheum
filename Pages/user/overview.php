<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<h2 data-i18n="overview">Overview</h2>

<span data-i18n>Username</span>: <?= $suname ?>
<br>
<span data-i18n>Rank</span>: <?= getNamedRank($srank) ?>
