<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<h2>Account</h2>
<h3>Change Password</h3>
<input id="new" type="password" placeholder="New password" required><br>
<input id="confirm" type="password" placeholder="Repeat" required><br>
<input id="password" type="password" placeholder="Current password" required><br>
<button id="submit">Change password</button><p id="password-error"><br>
