<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>Quiz | Latin</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/JS/jQuiz.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/quiz/quizzes.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
        <?php sro('/Includes/messages.php'); ?>
    </body>
</html>
