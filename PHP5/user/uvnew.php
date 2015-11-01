<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	requireLoggedIn(TRUE);

	global $mysqli, $suid;

	print_r($_GET);
	print_r($_POST);

/*
	$stmt = $mysqli->prepare("INSERT INTO user_vocab (user_id, stage, name, speechpart, definition) VALUES (?,?,?,?,?)");
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	} else
	if (!$stmt->bind_param("iisss", $suid, $stage, $name, $speechpart, $definition)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	} else
	if (!$stmt->execute()) {
		echo "Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error;
		return $result;
	} else $stmt->close();
/**/
?>
