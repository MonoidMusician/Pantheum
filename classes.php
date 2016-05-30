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
		<title>Classes | Latin</title>
		<?php sro('/Includes/css.php'); ?>
		<link rel="stylesheet" type="text/css" href="/CSS/admin.css">
		<?php sro('/Includes/js.php'); ?>
	</head>
	<body>
		<?php sro('/Includes/header.php'); ?>
		<section id="content">
			<?php sro('/Pages/classes.php'); ?>
		</section>
		<?php sro('/Includes/footer.php'); ?>
	</body>
</html>
