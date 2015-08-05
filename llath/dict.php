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

if (array_key_exists("data",$_POST) and $_POST["data"]) {
    $data = trim($_POST["data"]);
    if (startsWith($data, "<thead>") and endsWith($data, "</tbody>")) {
        $safe = strip_tags($data, "<div><thead></thead><tbody><tr><td><span><input></input></span></td></tr></tbody></div>");
        if ($data == $safe) {
            file_put_contents("dict.html", $safe);
        } else error_log("unsafe html");
    } else error_log("invalid html");
}
?><html><head>
    <meta charset="utf-8">
<title>Llath: Dictionary</title>
<script>
if (window.location.href.endsWith("dict.html"))
    window.location.href = window.location.href.replace(/html$/, "php");
</script>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="main.css">
<link rel="stylesheet" type="text/css" href="jquery-ui.css">
<script src="dict.js"></script>
</head>
<body>
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
}
?>
</content>
</body></html>
