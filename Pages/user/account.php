<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<h2>Account</h2>
<h3>Change Password</h3>
<form action="#account" method="POST" id="passwords">
<input id="password" type="password" placeholder="Current password" required><br>
<input id="new" type="password" placeholder="New password" required><br>
<input id="confirm" type="password" placeholder="Repeat" required><br>
<button id="submit">Change password</button><p id="password-error"><br>
</form>
<h3>Change Email</h3>
<form id="#passwords">
<input id="signupemail" class="signup" type="email" placeholder="Email"><br>
<button id="submit2">Change email</button><p id="email-error"><br>
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
