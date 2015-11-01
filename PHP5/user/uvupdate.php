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
	$stmt = $mysqli->prepare("UPDATE user_vocab SET stage = ? WHERE id = ?");
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	} else
	if (!$stmt->bind_param("si", $stage, $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	} else
	if (!$stmt->execute()) {
		echo "Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error;
	} else $stmt->close();
/**/
?>
