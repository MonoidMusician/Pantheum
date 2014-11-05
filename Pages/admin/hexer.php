<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<h2>Hexer</h2>
<div id="adminhexer">
    <textarea id="ahplain" placeholder="plain" cols="80" rows="20"></textarea>
    <br>
    <button class="jsettings-button" onclick="ahDoHex();">Hex</button><br>
    <button class="jsettings-button" onclick="ahDoUnHex();">Un-Hex</button><br>
    <textarea id="ahhex" placeholder="hex" cols="80" rows="20"></textarea>
</div>
