<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php sro('/Includes/head.php'); ?>
        <title>Dictionary | Latin</title>
        <?php sro('/Includes/css.php'); ?>
        <?php sro('/Includes/js.php'); ?>
        <script type="text/javascript" src="/latin/JS/jWord.js"></script>
        <script type="text/javascript" src="/latin/JS/lib/jquery.autocomplete.js"></script>
    </head>
    <body>
        <?php sro('/Includes/header.php'); ?>
        <section id="content">
            <?php sro('/Pages/dictionary2.php'); ?>
        </section>
        <?php sro('/Includes/footer.php'); ?>
    </body>
    <div class="floater-message">
    <a>Javascript did not successfully load. Some/all functionality may not be available.</a>
    </div>
    <div class="floater-message"><a></div>
    <script type="text/javascript">
        $('.floater-message').hide();
        messageTip = (function() {
            var timer = null;
            function callback() {
                $('.floater-message').hide();
                if (timer !== null)
                    clearTimeout(timer);
            }
            function messageTip(msg) {
                callback();
                $('.floater-message').show();
                $('.floater-message a').text(msg);
                timer = setTimeout(callback, 2300);
            }
            $('.floater-message').on("click", callback);
            return messageTip;
        })();
    </script>
</html>
