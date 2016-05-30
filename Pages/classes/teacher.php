<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	global $sli;

	if (isset($sli)) {
		if (!hasACL('teacher_panel', 'R', 'S')) {
			sro('/Pages/restricted/teacher.php');
			die("");
		}
	} else {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}
?>
<header>
	<h1>Class Settings</h1>
</header>
<div id="tpage">
    <nav id="tnav">
    </nav>
    <section id="tcontent" class="scrollable">
    </section>
</div>
<div class="clear"></div>
<script type="text/javascript">
    $(function() {
        cpage = new jPage();
        cpage.init('tcontent');

        var dpage = "";
        if (location.hash == '') {
            dpage = '#overview';
        } else if ((location.hash == '#user') || (location.hash == '#quiz')) {
            cpage.setStorage('id', getGET()['id']);
            dpage = location.hash;
        } else {
            dpage = location.hash;
        }

        cpage.setPages([
            ["#overview", "Overview", "Overview | Teacher Panel | Pantheum ", "/Pages/class/teacher-overview.php", true],
            ["#students", "Students", "Students | Teacher Panel | Pantheum", "/Pages/class/teacher-students.php", true],
            ["#student", "Student", "Student | Teacher Panel | Pantheum", ["/Pages/class/teacher-student.php", ["id"]], false],
	    ["#addstudents", "Add Students", "Add Students | Teacher Panel | Pantheum", "/Pages/class/teacher-add-students.php", false],
            ["#quizzes", "Quizzes", "Quizzes | Teacher Panel | Pantheum", "/Pages/class/teacher-quizzes.php", true],
	    ["#createquiz", "Create Quiz", "Create Quiz | Teacher Panel | Pantheum", "/Pages/class/teacher-create-quiz.php", false],
            ["#quiz", "Quiz", "Quiz | Teacher Panel | Pantheum", ["/Pages/class/teacher-quiz.php", ["id"]], false],
	    ["#analytics", "Analytics", "Analytics | Teacher Panel | Pantheum", "/Pages/class/teacher-analytics.php", true]
        ], dpage);
        cpage.setNavigation('tnav', 'ul');
        cpage.load();
    });
</script>
