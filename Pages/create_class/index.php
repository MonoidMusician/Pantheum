<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli, $suid;

	if (isset($sli)) {
		if (!hasACL('teacher_panel', 'W', 'S')) {
			sro('/Pages/restricted/teacher.php');
			die("");
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}
?>
<header>
	<h1>New Class</h1>
</header>
<article>
	<label for="class-name">Name: </label><input name="class-name" id="class-name" type="text" placeholder="Name"><br>
	<label for="class-desc">Description: </label><input name="class-desc" id="class-desc" placeholder="Description"><br>
	<label for="class-school">School: </label><input name="class-school" id="class-school" type="text" placeholder="School"><br>
	<br>
	<button onclick="handleCreateClass();">Create</button>
	<p id="class-error" class="error">&nbsp;</p>
</article>
<script>
	function handleCreateClass() {
		var n = $('#class-name').val();
		var d = $('#class-desc').val();
		var s = $('#class-school').val();

		if (n == '') {
			$('#class-error').html("Error: cannot leave name blank.");
			return;
		} else if (d == '') {
			$('#class-error').html("Error: cannot leave description blank.");
			return;
		} else if (s == '') {
			$('#class-error').html("Error: cannot leave school blank.");
			return;
		}

		$('#class-error').html("Creating class...");

		$.post("/PHP5/classes/create.php", { n: n, d: d, s: s }, function(raw) {
			var data = JSON.parse(raw)['result'];
			if (data == 'success') {
				window.location.href = '/classes.php?class=' + JSON.parse(raw)['class'];
			} else {
				$('#class-error').html("Error: " + data);
			}
		});
	}
</script>
