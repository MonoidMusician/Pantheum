<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    global $suid;
    if (!$suid) {
        sro('/Pages/restricted/logged-out.php');
        die();
    }
    sro('/Includes/functions.php');
    sro('/PHP5/quiz/common.php');
    sro('/PHP5/quiz/quiz_types.php');
?>
<header>
    <h1>My Quizzes</h1>
</header>
<article id="quiz">
    <table>
    <tr><th>Select</th><th>Name</th><th>Score/Grade</th><th>Date</th></tr>
    <?php
        global $sql_stmts;
        $quizzes = [];
        sql_getmany($sql_stmts["user_id->quiz_id reversed"], $quizzes, ["i", $suid]);
        foreach ($quizzes as &$q) {
            $q = QUIZ($q);
            ?><tr>
            <td><input name="quizzes" type="radio" value="<?=$q->id()?>"></td>
            <td><?=$q->name()?></td>
            <td class="text-center">
                <?php if ($q->completed()) {
                    $p = $q->percentage();
                    if ($p > 90) $class = "correct";
                    elseif ($p < 75) $class = "incorrect";
                    else $class = "other";
                    echo "<span class='jquiz-$class'>$p%</span>";
                } else echo "INC"; ?>
            </td><td>
                <span class="date" data-date="<?= $q->time_started()?>"></span>
            </td>
            </label><?php
        }
    ?>
    </table>
    <script>
    $('.date').each(function() {
        var $this = $(this);
        $this.text(Date.parse($this.data('date')+" GMT").toString('dddd, MMM d, yyyy, h:mmtt'));
    });
    </script>
    <br>
    <button id="start" onclick="startQuiz();">
        Review
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
        var id = $('input[name=quizzes]:checked').val();
        if (!id) return;
        $.get('/PHP5/quiz/restartQuiz.php?id=' + id, function(data) {
            var msg = data;
            if ((data = $.parseJSON(data)) !== null) {
                quiz.review(data);
            } else {
                alert("Error: "+msg);
            }
        });
    }
</script>
