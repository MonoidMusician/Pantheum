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
        <title>Help | Pantheum</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="https://d3js.org/d3.v3.min.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
		<div id="content-wrapper">
	        <section id="content">
	            <?php sro('/Pages/help.php'); ?>
	        </section>
	        <?php sro('/Includes/footer.php'); ?>
		</div>
    </body>
</html>
