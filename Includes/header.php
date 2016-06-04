<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
?>
		<header class="mobile-only padding-top-ten-pixels padding-left-twenty-pixels text-white" id="mobile-menu">
			<h3><span id="mobile-site-menu-button" class="oi padding-right-ten-pixels" data-glyph="menu" aria-hidden="true"></span> <span id="mobile-site-title">Pantheum</span></h3>
			<script>
				mobile_menu_visible = false;
				var menu = document.getElementById("mobile-site-menu-button");
				menu.addEventListener("touchstart", toggleDrawer);

				function toggleDrawer() {
					var drawer = document.getElementById("drawer-navigation");
					var content = document.getElementById("content-wrapper");
					mobile_menu_visible = !mobile_menu_visible;
					if (mobile_menu_visible) {
						drawer.classList.remove("desktop-only");
						content.classList.add("destkop-only");
					} else {
						drawer.classList.add("desktop-only");
						content.classList.remove("destkop-only");
					}
				}
			</script>
		</header>
		<header id="drawer-navigation" class="desktop-only global-nav column-left width-twenty mobile-width-full mobile-column-center">
			<nav class="global-nav mobile-width-full text-center">
				<ul class="global-nav mobile-width-full">
					<li class="global-nav mobile-width-full"><a data-i18n="nav.home" class="global-nav" href="<?php print rgd('/index.php'); ?>">Home</a></li>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.quiz" class="global-nav" href="<?php print rgd('/quiz.php'); ?>">Quiz</a></li>
<?php
	global $sli, $suname, $srank;
	if ((!isset($sli)) || ($sli != true)) {
?>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.login" class="global-nav" href="<?php print rgd('/help.php'); ?>">Help</a></li>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.login" class="global-nav" href="<?php print rgd('/login.php'); ?>">Login</a></li>
<?php
	} else {
?>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.settings" class="global-nav" href="<?php print rgd('/user.php'); ?>">Settings</a></li>

<?php
		if (hasACL('teacher_panel', 'R', 'S') || hasACL('class', 'R', 'S')) {
?>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.classes" class="global-nav" href="<?php print rgd('/classes.php'); ?>">Classes</a></li>
<?php
		}
?>
<?php
		if (hasACL('admin_panel', 'R', 'S')) {
?>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.admin" class="global-nav" href="<?php print rgd('/admin.php'); ?>">Admin</a></li>
<?php
		}
?>
					<li class="global-nav mobile-width-full"><a data-i18n="nav.logout" class="global-nav" href="<?php print rgd('/PHP5/logout.php'); ?>">Logout</a></li>
<?php
	}
?>
			</nav>
		</header>
