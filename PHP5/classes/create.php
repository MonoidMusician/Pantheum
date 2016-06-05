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
	$school = cleanInput('prepared', $_POST['s']);

	$query = $mysqli->prepare("SELECT id FROM class WHERE name=?");
	if (!$query) {
		die('{"result": "Unable to create class."}');
	}
	$query->bind_param("s", $name);
	if (!$query->execute()) {
		die('{"result": "Unable to create class."}');
	}
	$id = false;
	$query->bind_result($id);
	$query->fetch();
	if ($id !== false) {
		die('{"result": "This class already exists."}');
	}


	$query = $mysqli->prepare("INSERT INTO class (name, description, school) VALUES (?, ?, ?);");

	if (!$query) {
		die('{"result": "Unable to create class."}');
	}

	$query->bind_param("sss", $name, $description, $school);
	if ($query->execute()) {
		$query = $mysqli->prepare("SELECT id FROM class WHERE name=? AND description=? AND school=?");
		if (!$query) {
			die('{"result": "Unable to create class."}');
		}
		$query->bind_param("sss", $name, $description, $school);
		if (!$query->execute()) {
			die('{"result": "Unable to create class."}');
		}
		$query->bind_result($id);
		if (!$query->fetch()) {
			die('{"result": "Unable to create class."}');
		}

		print '{"result": "success", "class": ' . $id . '}';
	} else {
		die('{"result": "Unable to create class."}');
	}
?>
