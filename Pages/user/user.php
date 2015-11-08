<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<header id="uheader">
    <h2 id="utitle" data-i18n>Settings</h2>
</header>
<div id="upage" class="scrollable">
    <nav id="unav">
    </nav>
    <article id="ucontent">
    </article>
</div>
<script type="text/javascript">
    $(function() {
        var dpage = "";
        if (location.hash == '') {
            dpage = '#overview';
        } else {
            dpage = location.hash;
        }
        
        var cpage = new jPage();
        cpage.init('ucontent', pantheum._private.i18nload);
        cpage.setPages([
            ["#overview",    i18n.translatable('Overview'),    "Overview | Settings | Latin",    "/Pages/user/overview.php",    true],
            ["#account",     i18n.translatable('Account'),     "Account | Settings | Latin",     "/Pages/user/account.php",     true],
            ["#preferences", i18n.translatable('Preferences'), "Preferences | Settings | Latin", "/Pages/user/preferences.php", true],
            ["#vocab",       i18n.translatable('Vocab'),       "Vocab | Settings | Latin",       "/Pages/user/vocab.php",       true],
            //["#quizzes",     i18n.translatable('Quizzes'),     "Quizzes | Settings | Latin",     "/Pages/quiz/quizzes.php",     true],
        ], dpage);
        cpage.setNavigation('unav', 'ul');
        cpage.setBasepath('');
        cpage.load();
    });
</script>
