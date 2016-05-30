<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli, $suid;

	if (isset($sli)) {
		if (!hasACL('teacher_panel', 'R', 'S')) {
			sro('/Pages/restricted/teacher.php');
			die("");
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}
?>
<header>
	<h1>Select a Class</h1>
</header>
<article>
	<select id="select-class">
		<?php
			$c = getClasses($suid);

			foreach ($c as $a) {
				echo "<option value=" . $a['id'] . ">" . $a['name'] . "</option>";
			}
		?>
	</select>
	<button onclick="window.location.href='/classes.php?class=' + $('#select-class').val();">View Class</button>
	<?php
		if (hasACL('teacher_panel', 'R', 'S')) {
	?>
			<p>--OR--</p>
			<button>Create Class</button>
	<?php
		}
	?>
</article>
