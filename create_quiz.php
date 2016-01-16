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
		<title>Quiz Creator | Latin</title>
		<?php sro('/Includes/css.php'); ?>
		<?php sro('/Includes/js.php'); ?>
		<script type="text/javascript" src="/JS/jQuiz.js"></script>
		<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
		<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
	</head>
	<body>
		<?php sro('/Includes/header.php'); ?>
		<section id="content">
			<?php sro('/Pages/quiz/create_quiz.php'); ?>
		</section>
		<?php sro('/Includes/footer.php'); ?>
		<?php sro('/Includes/messages.php'); ?>
	</body>
</html>
