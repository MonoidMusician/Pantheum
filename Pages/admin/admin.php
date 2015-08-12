<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank(1);
?>
<header id="aheader">
    <h2 id="atitle">Admin Control Panel</h2>
</header>
<div id="apage" class="scrollable">
    <nav id="anav">
    </nav>
    <article id="acontent">
    </article>
</div>
<script type="text/javascript">
    $(function() {
        var dpage = "";
        if (location.hash == '') {
            dpage = '#overview';
        } else if ((location.hash == '#user') || (location.hash == '#word')) {
            location.hash = location.hash + 's';
            dpage = location.hash;
        } else {
            dpage = location.hash;
        }
        
        var cpage = new jPage();
        cpage.init('acontent');
        cpage.setPages([
            ["#overview", "Overview", "Overview | Admin Control Panel | Latin", "/Pages/admin/overview.php", true],
            ["#users", "Users", "Users | Admin Control Panel | Latin", "/Pages/admin/users.php", true],
            ["#user", "User", "User | Admin Control Panel | Latin", ["/Pages/admin/user.php", ["id"]], false],
            ["#words", "Words", "Words | Admin Control Panel | Latin", "/Pages/admin/words.php", true],
            ["#word", "word", "Word | Admin Control Panel | Latin", ["/Pages/admin/word.php", ["id"]], false],
            ["#logs", "Logs", "Logs | Admin Control Panel | Latin", "/Pages/admin/logs.php", true],
            ["#hexer", "Hexer", "Hexer | Admin Control Panel | Latin", "/Pages/admin/hexer.php", true],
            ["#deleted", "Deleted", "Deleted | Admin Control Panel | Latin", "/Pages/admin/deleted.php", true],
        ], dpage);
        cpage.setNavigation('anav', 'ul');
        cpage.setBasepath('');
        cpage.load();
    });
</script>
