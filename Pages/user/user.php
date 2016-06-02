<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    if (!isLoggedIn()) {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}

	global $suid;
	$level = 'S';
	if (isset($_GET['uid']) && $suid != $_GET['uid']) {
		$level = 'E';
	}

	if (!hasACL('user_settings', 'R', $level) && !hasACL('user_password', 'R', $level)) {
		sro('/Pages/restricted/admin.php');
		die("");
	}
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
        cpage.init('ucontent', pantheum.update.bind(undefined, '#uheader, #upage'));
        cpage.setPages([
            ["#overview",    i18n.translatable('Overview'),    "Overview | Settings | Latin",    "/Pages/user/overview.php",    true],
			<?php
				if (hasACL('user_password', 'R', $level)) {
			?>
            		["#account",     i18n.translatable('Account'),     "Account | Settings | Latin",     "/Pages/user/account.php",     true],
			<?php
				}
			?>
			<?php
				if (hasACL('user_settings', 'R', $level)) {
			?>
	            ["#preferences", i18n.translatable('Preferences'), "Preferences | Settings | Latin", "/Pages/user/preferences.php", true],
	            ["#vocab",       i18n.translatable('Vocab'),       "Vocab | Settings | Latin",       "/Pages/user/vocab.php",       true],
	            ["#quizzes",     i18n.translatable('Quizzes'),     "Quizzes | Settings | Latin",     "/Pages/quiz/quizzes.php",     true],
			<?php
				}
			?>
        ], dpage);
        cpage.setNavigation('unav', 'ul');
        cpage.setBasepath('');
        cpage.load();
    });
</script>
