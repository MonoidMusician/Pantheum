<?php
	require_once('/var/www/config.php');
	sro('/Includes/session.php');
	global $sudata;
?>
		<script>var pantheum = {
			_private:{},
			user:{administrator:true},
			api_path: '/PHP5/dictionary/',
			view: {},
			model: {},
			udata:<?= $sudata ? $sudata : 'null' ?>,
		};</script>
		<script type="text/javascript" src="/JS/pantheum.js"></script>
<?php
	if (!array_key_exists("devel",$_GET) or $_GET["devel"] != "false") {
?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-69525223-1', 'auto');
			ga('send', 'pageview');
		</script>
<?php
	}
?>
		<script type="text/javascript" src="/JS/lib/jquery.js"></script>
		<script type="text/javascript" src="/JS/lib/jCanvas.js"></script>
		<script type="text/javascript" src="/JS/lib/jTable.js"></script>
		<script type="text/javascript" src="/JS/lib/md5.js"></script>
		<script type="text/javascript" src="/JS/lib/sha512.min.js"></script>
		<script type="text/javascript" src="/JS/lib/whirlpool.min.js"></script>
		<script type="text/javascript" src="/JS/lib/jForm.js"></script>
		<script type="text/javascript" src="/JS/artwork.js"></script>
		<script type="text/javascript" src="/JS/login.js"></script>
		<script type="text/javascript" src="/JS/lib/jSettings.js"></script>
		<script type="text/javascript" src="/JS/url.js"></script>
		<script type="text/javascript" src="/JS/lib/util.js"></script>
		<script type="text/javascript" src="/JS/lib/jPage.js"></script>
		<script type="text/javascript" src="/JS/lib/compat.js"></script>
		<script type="text/javascript" src="/JS/lib/unorm.js"></script>
		<script type="text/javascript" src="/JS/lib/la_ipa.js"></script>
		<script type="text/javascript" src="/JS/lib/i18next.js"></script>
		<script type="text/javascript" src="/JS/lib/jquery.autosize.input.min.js"></script>
		<script type="text/javascript" src="/JS/lib/jstorage.min.js"></script>
		<script type="text/javascript" src="/JS/lib/jquery.qtip.min.js"></script>
		<script type="text/javascript" src="/JS/lib/select2.min.js"></script>
		<script type="text/javascript" src="/JS/lib/html.sortable.min.js"></script>
		<script type="text/javascript" src="/JS/view.js"></script>
		<script>$(pantheum.init)</script>

