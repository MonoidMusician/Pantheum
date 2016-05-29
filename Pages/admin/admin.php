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
<div id="apage">
    <nav id="anav">
    </nav>
    <article id="acontent" class="scrollable">
    </article>
</div>
<div class="clear"></div>
<script type="text/javascript">
    $(function() {
        cpage = new jPage();
        cpage.init('acontent');

        var dpage = "";
        if (location.hash == '') {
            dpage = '#overview';
        } else if ((location.hash == '#user') || (location.hash == '#word')) {
            cpage.setStorage('id', getGET()['id']);
            dpage = location.hash;
        } else {
            dpage = location.hash;
        }

        cpage.setPages([
            ["#overview", "Overview", "Overview | Admin Control Panel | Pantheum ", "/Pages/admin/overview.php", true],
            ["#users", "Users", "Users | Admin Control Panel | Pantheum", "/Pages/admin/users.php", true],
            ["#user", "User", "User | Admin Control Panel | Pantheum", ["/Pages/admin/user.php", ["id"]], false],
            ["#words", "Words", "Words | Admin Control Panel | Pantheum", "/Pages/admin/words.php", true],
            ["#word", "Word", "Word | Admin Control Panel | Pantheum", ["/Pages/admin/word.php", ["id"]], false],
            //["#deleted", "Deleted", "Deleted | Admin Control Panel | Tourneypicks v2", "/Pages/admin/deleted.php", true],
        ], dpage);
        cpage.setNavigation('anav', 'ul');
        cpage.load();
    });
</script>
