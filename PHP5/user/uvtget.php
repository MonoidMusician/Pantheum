<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	requireLoggedIn(TRUE);

	global $mysqli, $suid;

	$stmt = $mysqli->prepare("SELECT id, stage, name, speechpart, definition FROM user_vocab WHERE user_id = ?");
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	} else
	if (!$stmt->bind_param("i", $suid)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	} else
	if (!$stmt->execute()) {
		echo "Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error;
	} else {
		$id = $name = $stage = $speechpart = $definition = NULL;
		$stmt->bind_result($id, $stage, $name, $speechpart, $definition);
		while ($stmt->fetch()) {
			print "$id{(,)}$stage{(,)}$name{(,)}$speechpart{(,)}$definition{[,]}";
		}
		$stmt->close();
	}
?>
