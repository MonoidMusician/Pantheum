<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	if (!isLoggedIn()) {
		die('{"result": "Must be logged in."}');
	}

	if (!hasACL('teacher_panel', 'W', 'S')) {
		die('{"result": "Must be a teacher to access."}');
	}

	$name = cleanInput('prepared', $_POST['n']);
	$description = cleanInput('prepared', $_POST['d']);
	$school = cleanInput('prepraed', $_POST['s']);

	$query = $mysqli->prepare("INSERT INTO classes (nam, description, school) VALUES (?, ?, ?);");
	$query->bind_param("sss", $name, $description, $school);
	if ($query->execute()) {
		print '{"result": "success"}';
	} else {
		die('{"result": "Unable to create class."}');
	}
?>
