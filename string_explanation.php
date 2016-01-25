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
		<title>String library | Pantheum</title>
		<?php sro('/Includes/css.php'); ?>
		<link rel="stylesheet" type="text/css" href="/CSS/sentence.css">
		<?php sro('/Includes/js.php'); ?>
		<script type="text/javascript" src="https://d3js.org/d3.v3.min.js"></script>
		<script type="text/javascript" src="/JS/lib/jquery.autocomplete.js"></script>
		<script type="text/javascript" src="/JS/lib/damerau-levenshtein.js"></script>
		<script type="text/javascript" src="/JS/combo.js"></script>
		<script type="text/javascript" src="/JS/parser.js"></script>
	</head>
	<body>
		<?php sro('/Includes/header.php'); ?>
		<section id="content">
			<?php sro('/Pages/string_explanation.php'); ?>
		</section>
		<?php sro('/Includes/footer.php'); ?>
	</body>
</html>
