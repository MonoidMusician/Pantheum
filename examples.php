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
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
		<div id="content-wrapper">
	        <section id="content">
	            <?php sro('/Pages/examples.php'); ?>
	        </section>
	        <?php sro('/Includes/footer.php'); ?>
		</div>
    </body>
</html>
