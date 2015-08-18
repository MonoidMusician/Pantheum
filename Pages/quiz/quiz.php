<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    sro('/PHP5/quiz/common.php');
    global $sql_stmts, $suid;
?>
<header>
    <h1>Quiz</h1>
</header>
<article id="quiz">
    <?php
        if (requireLoggedIn(FALSE)) {
            ?> Review and resume previous quizzes <a href="quizzes.php">here</a>. <?php
            $quizzes = [];
            sql_getmany($sql_stmts["user_id->quiz_id reversed"], $quizzes, ["i", $suid]);
            foreach ($quizzes as $i => $q) {
                if (QUIZ($q)->completed()) unset($quizzes[$i]);
            }
            if (count($quizzes) == 1) $pl = ""; else $pl = "zes";
            if (count($quizzes)) echo "You have ".count($quizzes)." quiz$pl to complete.";
        } else {
            ?> You are not logged in. Your scores cannot be saved. <?php
        }
    ?>
    <br>
    Quiz me on:
    <?php
        sro('/PHP5/quiz/showQuizTypes.php');
    ?>
    <br>
    Number of questions:
    <input type="number" class="small" id="quiz-number" value="10" min="1">
    <br>
    <button id="start" onclick="startQuiz();">
        Start the Quiz
    </button>
</article>
<script type="text/javascript">
    $(document).on('keyup', '#quiz-number', function(event) {
        if (event.which == 13) {
            startQuiz();
        }
    });
    var quiz = new jQuiz();
    quiz.init('quiz', '/PHP5/quiz/nextQuestion.php', '/PHP5/quiz/submitQuestion.php', '/PHP5/quiz/endQuiz.php');
    function startQuiz() {
        var type = $('input[name=quiz-types]:checked').val();
        var last = $('#quiz-number').val();
        $.get('/PHP5/quiz/startQuiz.php?type=' + encodeURIComponent(type) + '&last=' + last, function(data) {
            if (data == 'no-credit') {
                if (confirm('Warning: your results will not be saved because you are not logged in. Do you want to continue?'))
                    data = 'success';
                else return;
            }
            if (data == 'success') {
                quiz.start(last);
            } else {
                alert("Error: "+data);
            }
        });
    }
</script>
