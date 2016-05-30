<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli;

	if (isset($sli)) {
		if (!hasACL('class', 'R', 'S')) {
			sro('/Pages/restricted/teacher.php');
			die("");
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}
?>
<header>
	<h1>Class Dashboard</h1>
</header>
<div id="cpage">
    <nav id="cnav">
    </nav>
    <section id="ccontent" class="scrollable">
    </section>
</div>
<div class="clear"></div>
<script type="text/javascript">
    $(function() {
        cpage = new jPage();
        cpage.init('ccontent');

        var dpage = "";
        if (location.hash == '') {
            dpage = '#overview';
        } else {
            dpage = location.hash;
        }

        cpage.setPages([
            ["#overview", "Overview", "Overview | Student Panel | Pantheum ", "/Pages/classes/student-overview.php", true],
            ["#quizzes", "Quizzes", "Quizzes | Student Panel | Pantheum", "/Pages/classes/student-quizzes.php", true],
            ["#settings", "Settings", "Settings | Student Panel | Pantheum", "/Pages/classes/student-settings.php", true]
        ], dpage);
        cpage.setNavigation('cnav', 'ul');
        cpage.load();
    });
</script>
