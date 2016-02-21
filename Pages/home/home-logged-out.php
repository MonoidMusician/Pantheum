<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<header>
    <h1 data-i18n="msg.Welcome">Welcome</h1>
</header>
<article>
    <p>
        <span data-i18n="msg.welcome">Welcome to the Pantheum, quizzes and a dictionary for Latin, created by Nick Scheel with website design by Alex Scheel.</span> <a data-i18n="link.need_help" href="/help.php">Need help?</a><br><br>
        <button data-i18n="link.latin_websites" class="large" onclick="window.location.href='/links.php';">Latin websites</button><br>
        <button data-i18n="link.sentence_viewer" class="large" onclick="window.location.href='/sentence.php';">Sentence Viewer</button><br>
        <button data-i18n="link.dictionary" class="large" onclick="window.location.href='/dictionary.php';">Dictionary</button><br>

        Please <a href="/login.php">log in</a> to save scores.
    </p>
</article>
