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
            ["#overview",    "<span data-i18n='Overview'>Overview</span>",       "Overview | Settings | Latin",    "/Pages/user/overview.php",    true],
            ["#account",     "<span data-i18n='Account'>Account</span>",         "Account | Settings | Latin",     "/Pages/user/account.php",     true],
            ["#preferences", "<span data-i18n='Preferences'>Preferences</span>", "Preferences | Settings | Latin", "/Pages/user/preferences.php", true],
            ["#vocab",       "<span data-i18n='Vocab'>Vocab</span>",             "Vocab | Settings | Latin",       "/Pages/user/vocab.php",       true],
            ["#quizzes",     "<span data-i18n='Quizzes'>Quizzes</span>",         "Quizzes | Settings | Latin",     "/Pages/user/quizzes.php",     true],
        ], dpage);
        cpage.setNavigation('unav', 'ul');
        cpage.setBasepath('');
        cpage.load();
    });
</script>
