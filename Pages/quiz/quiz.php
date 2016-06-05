<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
	sro('/PHP5/quiz/common.php');
	global $sql_stmts, $suid;
?>
<header>
	<h1 data-i18n>Quiz</h1>
</header>
<article id="quiz">
	<?php
		if (requireLoggedIn(FALSE)) {
		?> <span data-i18n="quiz.loggedin">Review and resume previous quizzes</span> <a href="quizzes.php" data-i18n="ui.here">here</a>. <?php
			$quizzes = [];
			sql_getmany(sql_stmt("user_id->quiz_id reversed"), $quizzes, ["i", $suid]);
			foreach ($quizzes as $i => $q) {
				if (QUIZ($q)->completed()) unset($quizzes[$i]);
			}
			if (count($quizzes) == 1) $pl = ""; else $pl = "zes";
			if (count($quizzes)) echo "You have ".count($quizzes)." quiz$pl to complete.";
		} else {
		?> <span data-i18n="quiz.loggedout">You are not logged in. Your scores cannot be saved.</span> <?php
		}
	?>
	<br><br>
	<span data-i18n="quiz.subject">Quiz me on</span>:
	<?php sro('/PHP5/quiz/showQuizTypes.php'); ?>
	<div id="selections">
		<span>Options</span>:
		<?php sro('/PHP5/quiz/showQuizOptions.php'); ?>
	</div><div>
		Score answers per <select id="quiz-mode">
			<option value="question" selected>question</option>
			<option value="page">page</option>
			<!--<option value="quiz">quiz</option>-->
		</select>
	</div>
	<span data-i18n="quiz.number_of_questions">Number of questions</span>:
	<input type="number" class="small" id="quiz-number" value="10" min="1" max="100">
	<br>
	<button id="start" onclick="startQuiz();" data-i18n="quiz.start">
		Start the Quiz
	</button>
</article>
<script type="text/javascript">
	$(document).on('keyup', '#quiz-number', function(event) {
		if (event.which == 13) startQuiz();
	});
	var quiz = new jQuiz();
	quiz.init('quiz', '/PHP5/quiz/nextQuestion.php', '/PHP5/quiz/submitQuestion.php', '/PHP5/quiz/endQuiz.php', '/PHP5/quiz/checkQuestion.php');
	var quiz_lock = false;
	function startQuiz() {
		if (quiz_lock) return; else quiz_lock = true;
		var type = $('input[name=quiz-types]:checked').val();
		var last = $('#quiz-number').val();
		var mode = $('#quiz-mode').val();
		var post = {
			type: type, mode: mode, last: last
		};
		$('#selections').find('select, input').filter(':visible').each(function() {
			var $this = $(this), id = $this.attr('data-selection'), value = $this.val();
			if (!id.startsWith('selected-')) id = 'selected-' + id;
			post[id] = value;
		});
		$.post('/PHP5/quiz/startQuiz.php', post, function(data) {
			data = data.trim();
			quiz_lock = false;
			var id, i;
			if (data == 'no-credit') {
				if (confirm('Warning: your results will not be saved because you are not logged in. Do you want to continue?')) {
					data = 'success';
				} else {
					return;
				}
			}
			if (!isNaN(i = parseInt(data))) {
				id = i; data = 'success';
			}
			console.log(id);
			if (data == 'success') {
				quiz.start(last, type, mode, id);
			} else {
				alert("Error: "+data);
			}
		});
	}
</script>
