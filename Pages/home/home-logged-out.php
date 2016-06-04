<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<header>
    <h1 data-i18n="msg.Welcome">Welcome</h1>
</header>
<article class="width-half column-left mobile-width-full">
    <p>
        <span data-i18n="msg.welcome">Welcome to the Pantheum, quizzes and a dictionary for Latin, created by Nick Scheel with website design by Alex Scheel.</span> <a data-i18n="link.need_help" href="/help.php">Need help?</a><br><br>
        <button data-i18n="link.latin_websites" class="large" onclick="window.location.href='/links.php';">Latin websites</button><br>
        <button data-i18n="link.sentence_viewer" class="large" onclick="window.location.href='/sentence.php';">Sentence Viewer</button><br>
        <button data-i18n="link.dictionary" class="large" onclick="window.location.href='/dictionary.php';">Dictionary</button><br>

        Please <a href="/login.php">log in</a> to save scores.
    </p>
</article>
<article class="secondary width-half column-right mobile-width-full">
    <h1>
        <span data-i18n="quick_quiz">Quick quiz</span>
        <?php sro('/PHP5/dictionary/select-lang.php'); ?>
    </h1>
    <?php sro('/PHP5/quiz/smallquiz.php'); ?>
</article>
<script>
$(function() {
    var h1 = $('#pantheumsmallquiz').parent().find('h1');
    h1.find('select').on('change', function() {
        var lang = $(this).val();
        $.get('/PHP5/quiz/smallquiz.php?lang='+lang)
        .success(function(data) {
            $('#pantheumsmallquiz').replaceWith(data);
        });
    });
    h1 = h1.find('span');
    pantheumsmallquiz.handler = function(correct) {
        if (!$('#smallquiz-correct').length) {
            //h1.find('select').remove();
            h1.html(h1.html() + ' (<span id="smallquiz-correct">0</span> for <span id="smallquiz-total">0</span>)');
        }
        if (correct) $('#smallquiz-correct').text($('#smallquiz-correct').text() - (-1));
        $('#smallquiz-total').text($('#smallquiz-total').text() - (-1));
    };
});
</script>
