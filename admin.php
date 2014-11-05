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
        <title>Admin | Latin</title>
        <?php sro('/Includes/css.php'); ?>
        <link rel="stylesheet" type="text/css" href="/latin/CSS/admin.css">
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/latin/JS/admin.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/admin.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
</html>
