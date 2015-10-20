<?php
    require_once('/var/www/config.php');
    sro('/Includes/session.php');
    global $sudata;
?>
        <script>
            var pantheum = {udata:<?= $sudata ? $sudata : 'null' ?>,_private:{}};
        </script>
        <script type="text/javascript" src="/JS/lib/jquery.js"></script>
        <script type="text/javascript" src="/JS/lib/jCanvas.js"></script>
        <script type="text/javascript" src="/JS/lib/jTable.js"></script>
        <script type="text/javascript" src="/JS/lib/md5.js"></script>
        <script type="text/javascript" src="/JS/lib/sha512.min.js"></script>
        <script type="text/javascript" src="/JS/lib/whirlpool.min.js"></script>
        <script type="text/javascript" src="/JS/lib/jForm.js"></script>
        <script type="text/javascript" src="/JS/artwork.js"></script>
        <script type="text/javascript" src="/JS/login.js"></script>
        <script type="text/javascript" src="/JS/lib/jSettings.js"></script>
        <script type="text/javascript" src="/JS/url.js"></script>
        <script type="text/javascript" src="/JS/lib/util.js"></script>
        <script type="text/javascript" src="/JS/lib/jPage.js"></script>
        <script type="text/javascript" src="/JS/lib/compat.js"></script>
        <script type="text/javascript" src="/JS/lib/unorm.js"></script>
        <script type="text/javascript" src="/JS/lib/la_ipa.js"></script>
        <script type="text/javascript" src="/JS/lib/i18next.js"></script>
        <script type="text/javascript" src="/JS/lib/jquery.autosize.input.min.js"></script>
        <script type="text/javascript" src="/JS/lib/jstorage.min.js"></script>
        <script type="text/javascript" src="/JS/view.js"></script>
        <script>
            pantheum._private.i18nload = function(err, t) {
                $('body').i18n();
            };
            i18n.init({
                fallbackLng: 'en'
            });
            i18n.setLng(
                pantheum.udata && pantheum.udata["language"]
                ? pantheum.udata["language"]
                : 'en', pantheum._private.i18nload
            );
        </script>

