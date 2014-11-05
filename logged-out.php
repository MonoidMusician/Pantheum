<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>Logged Out | Latin</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/logged-out.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
</html>
