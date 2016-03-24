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
        <title>Dictionary | Pantheum</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <?php sro('/Includes/jsx.php'); ?>
        <script type="text/javascript" src="/JS/lib/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="/JS/jWord.js"></script>
        <script type="text/javascript" src="/JS/autocompletions.js"></script>
        <?php sro('/Includes/jsmodel.php'); ?>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/dictionary.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
        <?php sro('/Includes/messages.php'); ?>
    </body>
</html>
