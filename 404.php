<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
            sro('/Includes/head.php');
            sro('/Includes/css.php');
        ?>
        <?php
            sro('/Includes/js.php');
        ?>
        
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <div id="content">
            <h2>404</h2>
            <p>
                Error! File not found.
            </p>
        </div>
        <?php sro('/Includes/footer.php'); ?>
    </body>
</html>
