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
        <title>Add words | Latin</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/JS/lib/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="/JS/lib/jquery.autosize.input.min.js"></script>
        <script type="text/javascript" src="/JS/addword_data.js"></script>
        <script type="text/javascript" src="/JS/jWord.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/add_word.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
    <div class="floater-message">
    <a>Javascript did not successfully load. Some/all functionality may not be available.</a>
    </div>
    <script type="text/javascript">
        $('.floater-message').hide();
        messageTip = (function() {
            var timer = null;
            function callback() {
                $('.floater-message').hide();
                if (timer !== null)
                    clearTimeout(timer);
            }
            function messageTip(msg, delay) {
                if (delay === undefined) delay = 2300;
                callback();
                $('.floater-message').show();
                $('.floater-message a').text(msg);
                if (delay !== null)
                    timer = setTimeout(callback, delay);
            }
            $('.floater-message').on("click", callback);
            return messageTip;
        })();
    </script>
</html>
