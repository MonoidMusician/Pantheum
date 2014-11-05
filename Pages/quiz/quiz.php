<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<header>
    <h1>Quiz</h1>
</header>
<article id="quiz">
    Type of quiz: 
    <?php
        sro('/PHP5/quiz/showQuizTypes.php');
    ?>
    <br>
    Number of questions:
    <input class="small" id="quiz-number" value="10">
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
    quiz.init('quiz', '/latin/PHP5/quiz/nextQuestion.php', '/latin/PHP5/quiz/submitQuestion.php');
    function startQuiz() {
        var type = $('input[name=quiz-types]:checked').val();
        $.get('/latin/PHP5/quiz/startQuiz.php?type=' + encodeURIComponent(type), function(data) {
            if (data == 'success') {
                quiz.start($('#quiz-number').val());
            } else {
                alert(data);
            }
        });
    }
</script>
