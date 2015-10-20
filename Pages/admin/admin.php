<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank(1);
?>
<header id="aheader">
    <h2 id="atitle" data-i18n>Admin Control Panel</h2>
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
        cpage.init('acontent', pantheum._private.i18nload);
        cpage.setPages([
            ["#overview", "<span data-i18n='Overview'>Overview</span>", "Overview | Admin Control Panel | Latin", "/Pages/admin/overview.php", true],
            ["#users", "<span data-i18n='Users'>Users</span>", "Users | Admin Control Panel | Latin", "/Pages/admin/users.php", true],
            ["#user", "<span data-i18n='User'>User</span>", "User | Admin Control Panel | Latin", ["/Pages/admin/user.php", ["id"]], false],
            ["#words", "<span data-i18n='Words'>Words</span>", "Words | Admin Control Panel | Latin", "/Pages/admin/words.php", true],
            ["#word", "<span data-i18n='Word'>Word</span>", "Word | Admin Control Panel | Latin", ["/Pages/admin/word.php", ["id"]], false],
            ["#logs", "<span data-i18n='Logs'>Logs</span>", "Logs | Admin Control Panel | Latin", "/Pages/admin/logs.php", true],
            ["#hexer", "<span data-i18n='Hexer'>Hexer</span>", "Hexer | Admin Control Panel | Latin", "/Pages/admin/hexer.php", true],
            ["#deleted", "<span data-i18n='Deleted'>Deleted</span>", "Deleted | Admin Control Panel | Latin", "/Pages/admin/deleted.php", true],
        ], dpage);
        cpage.setNavigation('anav', 'ul');
        cpage.setBasepath('');
        cpage.load();
    });
</script>
