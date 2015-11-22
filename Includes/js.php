<?php
    require_once('/var/www/config.php');
    sro('/Includes/session.php');
    global $sudata;
?>
        <script>
            var pantheum = {udata:<?= $sudata ? $sudata : 'null' ?>,_private:{}};
        </script>
<?php
    if (!array_key_exists("devel",$_GET) or $_GET["devel"] != "false") {
?>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-69525223-1', 'auto');
            ga('send', 'pageview');
        </script>
<?php
    }
?>
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
        <script type="text/javascript" src="/JS/lib/jquery.qtip.min.js"></script>
        <script type="text/javascript" src="/JS/view.js"></script>
        <script>
            pantheum.lang = function() {
                return pantheum.udata && pantheum.udata["language"]
                    ? pantheum.udata["language"]
                    : 'en';
            };
            pantheum._private.i18nload = function(err, t) {
                if (err) console.log(err);
                pantheum.update();
            };
            i18n.init({
                fallbackLng: 'en'
            });
            i18n.setLng(pantheum.lang(), pantheum._private.i18nload);
            i18n.translatable = function(s) {
                var s2 = i18n.t(s);
                if (!s2) s2 = s;
                return '<span data-i18n="'+s+'">'+s2+'</span>';
            };
            pantheum.update = function(element) {
                if (!element) element = 'body';
                var $e = $(element);
                var lang = pantheum.lang();
                $e.i18n();
                $e.find('[data-i18n]').removeClass(function(index, css) {
                    return (css.match(/(^|\s)format-word-\S+/g) || []).join(' ');
                }).addClass('format-word-'+lang).attr('data-original-word0', '');
                la_ipa.format($e);
                $e.find('[title]:not(abbr):not(.actionable):not(.select2-selection__rendered)').qtip({
                    style:{
                        classes:"qtip-light"
                    },
                    position:{
                        my:"center left",
                        at:"center right"
                    },
                    hide: {
                        fixed: true,
                        delay: 100,
                    }
                });
                $e.find('abbr[title]').qtip({
                    style:{
                        classes:"qtip-light qtip-abbr"
                    },
                    position:{
                        at:"top center",
                        my:"bottom center",
                        adjust: {y:5},
                    },
                    hide: {
                        fixed: true,
                        delay: 100,
                    }
                });
                $e.find('.actionable[title]').qtip({
                    style:{
                        classes:"qtip-light qtip-actionable"
                    },
                    position:{
                        my:"top center",
                        at:"bottom center",
                        adjust: {y:-5},
                    },
                    hide: {
                        fixed: true,
                        delay: 100,
                    }
                });
                return $e;
            };
        </script>

