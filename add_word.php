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
		<title>Add words | Latin</title>
		<?php sro('/Includes/css.php'); ?>
		<?php sro('/Includes/js.php'); ?>
		<script type="text/javascript" src="/JS/lib/jquery.autocomplete.js"></script>
		<script type="text/javascript" src="/JS/addword_data.js"></script>
		<script type="text/javascript" src="/JS/jWord.js"></script>
	</head>
	<body>
		<?php sro('/Includes/header.php'); ?>
		<div id="content-wrapper">
	        <section id="content">
				<?php sro('/Pages/add_word.php'); ?>
			</section>
			<?php sro('/Includes/footer.php'); ?>
			<?php sro('/Includes/messages.php'); ?>
		</div>
	</body>
</html>
