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
        Welcome to the Pantheum, quizzes and a dictionary for Latin, created by Nick Scheel with website design by Alex Scheel.<br><br>
        <button class="large" onclick="window.location.href='/links.php';">Latin websites</button><br>
        <button class="large" onclick="window.location.href='/sentence.php';">Sentence Viewer</button><br>
        <button class="large" onclick="window.location.href='/dictionary.php';">Dictionary</button><br>

        Please <a href="/login.php">log in</a> to save scores.
    </p>
</article>
