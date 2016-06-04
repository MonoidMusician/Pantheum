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
		<title>Forms | Latin</title>
		<?php sro('/Includes/css.php'); ?>
		<?php sro('/Includes/js.php'); ?>
	</head>
	<body>
		<?php sro('/Includes/header.php'); ?>
		<div class="column-right width-eighty mobile-width-full no-mobile-margin no-mobile-padding" id="content-wrapper">
	        <section class="no-mobile-margin" id="content">
				<?php sro('/Pages/raw_forms2.php'); ?>
			</section>
			<?php sro('/Includes/footer.php'); ?>
		</div>
	</body>
</html>
