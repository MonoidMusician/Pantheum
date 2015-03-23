<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<header>
    <h1>Welcome</h1>
</header>
<article>
    <p>
        Welcome to Sassy's Online Latin Quiz, created by Nick Scheel and website design by Alex Scheel.<br><br>
        <button class="large" onclick="window.location.href='/links.php';">Latin websites</button><br>
        <button class="large" onclick="window.location.href='/dictionary.php';">Dictionary</button><br>
        <button class="large" onclick="window.location.href='/sentence.php';">Sentence Viewer</button><br>
        <?php if (requireRank(3, FALSE)) { ?>
        <button class="large" onclick="window.location.href='/add_word.php';">Add words</button><br>
        <?php } ?><br>

        Thanks for logging in, your scores will be saved.
    </p>
</article>
