<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

global $suid;

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

?>
<html><head>
    <meta charset="utf-8">
<title>Llath: Dictionary</title>
<script>
if (window.location.href.endsWith("dict.html"))
    window.location.href = window.location.href.replace(/html$/, "php");
</script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>
$(function() {
    var status = $('#status');
    status.on('click', function() {
        $(this).hide();
    });
    if (status.text() === "" || status.text() === "loading...")
        $('#status').hide();
});
</script>
<script src="/JS/lib/md5.js"></script>
<script src="/JS/lib/sha512.min.js"></script>
<script src="/JS/lib/whirlpool.min.js"></script>
<script src="/JS/login.js"></script>
<script src="jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="main.css">
<link rel="stylesheet" type="text/css" href="jquery-ui.css">
<script src="dict.js"></script>
</head>
<body>
<?php

if (array_key_exists("data",$_POST) and $_POST["data"]) {
    if ($suid == 14) {
        $data = trim($_POST["data"]);
        if (startsWith($data, "<thead>") and endsWith($data, "</tbody>")) {
            $safe = strip_tags($data, "<div><thead></thead><tbody><tr><td><span><input><br></br></input></span></td></tr></tbody></div>");
            if ($data == $safe) {
                file_put_contents("dict.html", $safe);
                ?><div id="status" class="success">success</div><?php
            } else {
                ?><div id="status" class="error">unsafe html</div><?php
            }
        } else {
            ?><div id="status" class="error">invalid html</div><?php
        }
    } else {
        ?><div id="status" class="error">not logged in</div><?php
    }
} else {
    ?><div id="status">loading...</div><?php
}
?>
<content>
<h1>Ļaþ: ictionar</h1>

<table id="dict">
<?php
echo file_get_contents("dict.html");
?>
</table>
<?php
if ($suid == 14) {
    ?><button id="save">Save</button><?php
} else {
    ?><button id="showlogin">Login</button><?php
}
?>
</content>
<content id="login" style="display: none">
<?php sro('/Pages/login/login.php') ?>
</content>
</body></html>
