<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    if (!isLoggedIn()) {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}

	global $suid;
	$level = 'S';
	if (isset($_GET['uid']) && $suid != $_GET['uid']) {
		$level = 'E';
	}

	if (!hasACL('user_password', 'R', $level)) {
		sro('/Pages/restricted/admin.php');
		die("$level");
	}
?>
<h2 data-i18n="account">Account</h2>
<h3 data-i18n="change_password">Change Password</h3>
<form action="#account" method="POST" id="passwords">
<input id="password" type="password" placeholder="Current password" required><br>
<input id="new" type="password" placeholder="New password" required><br>
<input id="confirm" type="password" placeholder="Repeat" required><br>
<button id="submit" data-i18n="change_password">Change password</button><p id="password-error"><br>
</form>
<script>
    $(function() {
        $(document).on('submit', '#passwords', function(event) {
            console.log("<?= $suname ?>");
            changePassword("<?= $suname ?>", $('#password').val(), $('#new').val(), $('#confirm').val(), '#password-error');
            event.preventDefault();
        });
    });
</script>
