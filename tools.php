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
		<title>Tools | Latin</title>
		<link rel="stylesheet" type="text/css" href="/CSS/d3.compose.css">
		<link rel="stylesheet" type="text/css" href="/CSS/calendar.css">
		<?php sro('/Includes/css.php'); ?>
		<?php sro('/Includes/js.php'); ?>
		<script type="text/javascript" src="/JS/jQuiz.js"></script>
		<script type="text/javascript" src="/JS/lib/d3.min.js"></script>
		<script type="text/javascript" src="/JS/lib/d3.chart.js"></script>
		<script type="text/javascript" src="/JS/lib/d3.compose-all.js"></script>
		<script type="text/javascript" src="/JS/lib/suncalc.js"></script>
		<script type="text/javascript" src="/JS/lib/calendar.js"></script>
	</head>
	<body>
		<?php sro('/Includes/header.php'); ?>
		<div class="column-right width-eighty mobile-width-full no-mobile-margin no-mobile-padding" id="content-wrapper">
	        <section class="no-mobile-margin" id="content">
				<?php sro('/Pages/tools.php'); ?>
			</section>
			<?php sro('/Includes/footer.php'); ?>
			<?php sro('/Includes/messages.php'); ?>
		</div>
	</body>
</html>
