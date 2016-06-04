<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    if (array_key_exists("lang", $_GET))
        $lang = "?lang=$_GET[lang]";
    else $lang = "";
?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>Home | Pantheum</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/JS/lib/jquery.countdown.min.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
		<div class="column-right width-eighty mobile-width-full no-mobile-margin no-mobile-padding" id="content-wrapper">
	        <section class="no-mobile-margin" id="content">
	            <?php sro('/Pages/home.php'); ?>
	        </section>
			<?php sro('/Includes/footer.php'); ?>
		</div>
    </body>
</html>
