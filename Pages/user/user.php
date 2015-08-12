<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<header id="uheader">
    <h2 id="utitle">User Control Panel</h2>
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
        cpage.init('ucontent');
        cpage.setPages([
            ["#overview",    "Overview",    "Overview | User Control Panel | Latin",    "/Pages/user/overview.php",    true],
            ["#account",     "Account",     "Account | User Control Panel | Latin",     "/Pages/user/account.php",     true],
            ["#preferences", "Preferences", "Preferences | User Control Panel | Latin", "/Pages/user/preferences.php", true],
            ["#vocab",       "Vocab",       "Vocab | User Control Panel | Latin",       "/Pages/user/vocab.php",       true],
            ["#quizzes",     "Quizzes",     "Quizzes | User Control Panel | Latin",     "/Pages/user/quizzes.php",     true],
        ], dpage);
        cpage.setNavigation('unav', 'ul');
        cpage.setBasepath('');
        cpage.load();
    });
</script>
