<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>404 | Pantheum</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <h1>404</h1>
            <p>Error: file not found.</p>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
</html>
