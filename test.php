<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>Test | Pantheum</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/JS/jWord.js"></script>
        <script type="text/javascript" src="/JS/lib/jquery.autocomplete.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
<?php
$w = WORD(defaultDB(), 212);
$w->read_paths();
$values4 = $w->path()->iterate("case");
$values3 = $w->path()->iterate("gender");
$values2 = $w->path()->iterate("number");

$i=0;
$get_question = function($word) use(&$i) {
    return '<input id="quiz-answer'.$i++.'" placeholder="Enter form" title="undefined" type="text" style="width: 120px;">';
};
do_table(
    $w,NULL,NULL,$values2,$values3,$values4,
    "format_value",
    $get_question,
    NULL, NULL,
    0
);
?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
</html>
