<?php
@ini_set("output_buffering", "Off");
@ini_set('implicit_flush', 1);
@ini_set('zlib.output_compression', 0);

	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
	requireRank(1);

	// ACLs are in the format: RW, where R and W are either N - none, S - Self,
	// or E - everyone. E.g., grades: SN would denote read only access of grades
	// to only the current user.

	$M_result = $mysqli->query("SELECT * FROM classes;");
	while ($M_row = $M_result->fetch_assoc()) {
		$n = $M_row['class_name'];
		$v = $M_row['hidden'];
		$t = $M_row['teacher_id'];

		$mysqli->query("INSERT INTO class (name, description, visible) VALUES ('$n', '$n', '$v');");

		$M_result2 = $mysqli->query("SELECT * FROM class WHERE name='$n';");
		$M_row2 = $M_result2->fetch_assoc();

		$i = $M_row2['id'];

		$mysqli->query("INSERT INTO class_acls (user_id, class_id, grades, create_quiz, add_users, take_quiz) VALUES ('$t', '$i', 'EE', 'EE', 'EE', 'SS');");
	}

	$M_result = $mysqli->query("SELECT * FROM users;");
	while ($M_row = $M_result->fetch_assoc()) {
		$u = $M_row['id'];
		$r = $M_row['rank'];
		$c = $M_row['class'];

		echo "$u $r $c\n";
		echo json_encode($M_row);
		echo "\n\n";

		if ("$r" == '1') {
			$mysqli->query("INSERT INTO acls (user_id, admin_panel, add_words, teacher_panel, class_settings, user_settings, user_password) VALUES ($u, 'EE', 'EE', 'EE', 'EE', 'EE', 'EE');");
		} else if ("$r" == '4') {
			$mysqli->query("INSERT INTO acls (user_id, admin_panel, add_words, teacher_panel, class_settings, user_settings, user_password) VALUES ($u, 'NN', 'NN', 'NN', 'SS', 'SS', 'SS');");
		} else {
			echo "Unknown rank";
		}

		if ($c != null) {
			$M_result4 = $mysqli->query("SELECT * FROM class_acls WHERE user_id='$u';");
			if ($M_row4 = $M_result4->fetch_assoc()) {
				echo "User is a teacher.";
			} else {
				$M_result2 = $mysqli->query("SELECT class_name FROM classes WHERE class_id='$c';");
				$M_row2 = $M_result2->fetch_assoc();

				$n = $M_row2['class_name'];

				$M_result3 = $mysqli->query("SELECT * FROM class WHERE name='$n';");
				$M_row3 = $M_result3->fetch_assoc();

				$i = $M_row3['id'];

				$mysqli->query("INSERT INTO class_acls (user_id, class_id, grades, create_quiz, add_users, take_quiz) VALUES ('$u', '$i', 'SN', 'NN', 'NN', 'SS');");
			}
		}
	}
?>
