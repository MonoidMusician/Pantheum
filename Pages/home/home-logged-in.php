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
        <span data-i18n="msg.welcome">Welcome to the Pantheum, quizzes and a dictionary for Latin, created by Nick Scheel with website design by Alex Scheel.</span><br><br>
        <button data-i18n="link.latin_websites" class="large" onclick="window.location.href='/links.php';">Latin websites</button><br>
        <button data-i18n="link.sentence_viewer" class="large" onclick="window.location.href='/sentence.php';">Sentence Viewer</button><br>
        <button data-i18n="link.dictionary" class="large" onclick="window.location.href='/dictionary.php';">Dictionary</button><br>
        <button data-i18n="link.dictionary" class="large" onclick="window.location.href='/user.php';">Account Settings</button><br>
        <?php if (requireRank(1, FALSE)) { ?>
        <button data-i18n="link.add_words" class="large" onclick="window.location.href='/add_word.php';">Admin Settings</button><br>
        <?php } ?>
        <?php if (requireRank(3, FALSE)) { ?>
        <button data-i18n="link.add_words" class="large" onclick="window.location.href='/add_word.php';">Add words</button><br>
        <?php } ?>
        <br>
        <span data-i18n="msg.loggedin">Thanks for logging in, your answers will be saved.</span>
    </p>
</article>
