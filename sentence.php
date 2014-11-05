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
        <title>Sentence | Latin</title>
        <?php sro('/Includes/css.php'); ?>
        <link rel="stylesheet" type="text/css" href="/latin/CSS/sentence.css">
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/sentence.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
</html>
