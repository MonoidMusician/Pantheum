<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Playground | Latin</title>
		<link rel="stylesheet" type="text/css" href="/CSS/react.css">
		<link rel="stylesheet" type="text/css" href="/Images/open-iconic/font/css/open-iconic.css">
		<?php sro('/Includes/js.php'); ?>
		<script type="text/javascript" src="/JS/build/model.js"></script>
		<script type="text/javascript" src="/JS/build/react.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.24/browser.min.js"></script>
		<script type="text/babel" src="/JS/react/view.js"></script>
		<script type="text/babel" src="/JS/react/attributes.js"></script>
		<script type="text/babel" src="/JS/react/dictionary.js"></script>
	</head>
	<body>
		<section id="content">
			<?php sro('/Pages/playground.php'); ?>
		</section>
	</body>
</html>
