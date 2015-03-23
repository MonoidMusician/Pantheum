<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
        <header class="global-nav">
            <nav class="global-nav">
                <ul class="global-nav">
                    <li class="global-nav"><a class="global-nav" href="<?php print rgd('/index.php'); ?>">Home</a></li>
                    <li class="global-nav"><a class="global-nav" href="<?php print rgd('/quiz.php'); ?>">Quiz</a></li>
<?php
    global $sli, $suname, $srank;
    if ((!isset($sli)) || ($sli != true)) {
?>
                    <li class="global-nav"><a class="global-nav" href="<?php print rgd('/login.php'); ?>">Login</a></li>
<?php
    } else {
?>
                    <li class="global-nav"><a class="global-nav" href="<?php print rgd('/settings.php'); ?>">Settings</a></li>
<?php
        if ($srank == '1') {
?>
                    <li class="global-nav"><a class="global-nav" href="<?php print rgd('/admin.php'); ?>">Admin</a></li>
<?php
        }
?>
                    <li class="global-nav"><a class="global-nav" href="<?php print rgd('/PHP5/logout.php'); ?>">Logout</a></li>
<?php
    }
?>
            </nav>
        </header>
