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
    $master = safe_get("master",$_GET) == "true" and quiz_auth();
?>
<header>
    <h1>My Quizzes <?php if (!$master and quiz_auth()) echo "<a href='?master=true'>master</a>" ?></h1>
</header>
<article id="notice">
    <h2>Sorry!</h2>
    <p>Due to programmer incompetency, your quizzes as of November 7th were accidentally deleted and cannot be recovered. Your quizzes after that date should be shown below:
    <p style="font-size: 50%;">I'll make sure it doesn't happen again.
</article>
<article id="quiz">
    <table style="border-spacing: 8px 2px;">
    <tr><th>Select</th>
    <?php if ($master) echo "<th>UserName</th>"; ?>
    <th>Name</th><th>Score/Grade</th><th>Date</th></tr>
    <?php
        global $sql_stmts;
        $quizzes = [];
        $quizzes_data = [];
        if ($master) sql_getmany($sql_stmts["all quizzes"], $quizzes, []);
        else sql_getmany($sql_stmts["user_id->quiz_id reversed"], $quizzes, ["i", $suid]);
        foreach ($quizzes as &$q) {
            $q = QUIZ($q);
            $quizzes_data[$q->id()] = $q->completed();
            ?><tr>
            <td><input name="quizzes" type="radio" value="<?=$q->id()?>"></td>
            <?php
                if ($master) echo "<td>".$q->username()."</td>";
            ?>
            <td><?=$q->name()?></td>
            <td class="text-center">
                <?php if ($q->completed()) {
                    $p = $q->percentage();
                    if ($p > 90) $class = "correct";
                    elseif ($p < 75) $class = "incorrect";
                    else $class = "other";
                    echo "<span class='jquiz-$class'>$p%</span>";
                } else {
                    $data = $q->data();
                    echo "INC (".count($data["results"])."/".$data["last"].")";
                } ?>
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
        var from = d3.time.format("%Y-%m-%d %X GMT%Z");
        var to = d3.time.format("%A, %B %-d, %Y, at %-I:%M %p");
        var time = from.parse($this.data('date')+" GMT-0000");
        $this.text(to(time));
    });
    </script>
    <br>
    <button id="start" onclick="startQuiz();">
        Review/Resume
    </button>
    <button id="delete" onclick="deleteQuiz();" disabled>
        Delete
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
    var quizzes_data = <?= json_encode($quizzes_data) ?>;
    $('input[name=quizzes]').on('change', function() {
        var $this=$(this), val=$this.val();
        if (!val in quizzes_data) {
            $('#start').text("Review/Resume");
            $('#delete').attr('disabled', true);
        } else if (quizzes_data[val]) {
            $('#start').text("Review");
            $('#delete').removeAttr('disabled');
        } else {
            $('#start').text("Resume");
            $('#delete').removeAttr('disabled');
        }
    });
    function startQuiz() {
        var id = $('input[name=quizzes]:checked').val();
        if (!id) return;
        $.get('/PHP5/quiz/restartQuiz.php?id=' + id, function(data) {
            var msg = data;
            try {
                data = $.parseJSON(data);
                quiz.review(data);
            } catch(e) {
                alert("Error: "+msg);
            }
        });
    }
    function deleteQuiz() {
        var id = $('input[name=quizzes]:checked').val();
        if (!id) return;
        $.get('/PHP5/quiz/deleteQuiz.php?id=' + id, function(data) {
            if (data == "success") {
                messageTip("Successfully deleted the quiz");
                $('input[name=quizzes][value='+id+']').parents("tr").remove();
            } else {
                alert("Error: "+data);
            }
        });
    }
</script>
