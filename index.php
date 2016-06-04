<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    if (array_key_exists("lang", $_GET))
        $lang = "?lang=$_GET[lang]";
    else $lang = "";
?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>Home | Pantheum</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/JS/lib/jquery.countdown.min.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
		<div id="content-wrapper">
	        <section id="content">
	            <?php sro('/Pages/home.php'); ?>
	            <article class="secondary">
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
	        </section>
			<?php sro('/Includes/footer.php'); ?>
		</div>
    </body>
</html>
