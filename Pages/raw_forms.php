<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');

$db = defaultDB();
global $sql_stmts;
if (count($_GET)) {
    if (!array_key_exists("lang", $_GET))
        $_GET["lang"] = 'la';

    if (!array_key_exists("name", $_GET))
        $_GET["name"] = NULL;

    if (!array_key_exists("spart", $_GET))
        $_GET["spart"] = NULL;

    foreach ($_GET as $k=>$v) {
        if (!startswith($k, "delete_")) continue;
        if ($v != "true") continue;
        $id = explode("_", $k, 2)[1];
        $id = intval($id);
        sql_exec(sql_stmt("form_id->delete from forms"), ["i", $id]);
    }

    $list = $db->find_all_words($_GET["name"], $_GET["lang"], $_GET["spart"]);
    foreach ($list as $w) {
        ?><ul><?php
        display_word_info($w);?><br><?php
        $id = $w->id();
        $w->read_paths();
        foreach ($w->paths() as $path) {
            echo "<li>[".$path->id()."] ($path)".$path->value();
            echo " <button onclick='moveloc(".$path->id().")'>Delete</button><br>";
        }
        ?></ul><hr><?php
    }
} else {
    $_GET["lang"] = "la"; $_GET["name"] = $_GET["spart"] = NULL;
}

?>
Find words:
    <input id="enter-word" type="text" <?php
        if (array_key_exists("name", $_GET))
            echo "value='".$_GET["name"]."'";
    ?> placeholder="By name">
    <select id="enter-lang">
<?php
    foreach ($db->langs() as $l) {
        ?><option <?php
        if ($l == $_GET["lang"]) {
            ?>selected='true' <?php
        }
        ?>><?php
        echo $l;
        ?></option><?php
    }
?>
    </select>
    <select id="enter-spart">
        <option>
<?php
    foreach ($db->sparts() as $s) {
        ?><option <?php
        if ($s == $_GET["spart"]) {
            ?>selected='true' <?php
        }
        ?>><?=$s?></option><?php
    }
?>
    </select>
    <button onclick="moveloc()">Search!</button>

    <script type="text/javascript">
    function getloc(id) {
        var loc = "?", op = "";
        if ($('#enter-word').val()) {
            loc += op + "name=" + encodeURIComponent($('#enter-word').val());
            op = "&";
        }
        if ($('#enter-lang').val()) {
            loc += op + "lang=" + encodeURIComponent($('#enter-lang').val());
            op = "&";
        }
        if ($('#enter-spart').val()) {
            loc += op + "spart=" + encodeURIComponent($('#enter-spart').val());
            op = "&";
        }
        if (id || id === 0) {
            loc += op + "delete_"+id+"=true";
            op = "&";
        }
        if (loc !== "?")
            return loc;
    }
    function moveloc(id) {
        loc = getloc(id);
        if (loc)
            location.href = loc;
    }
    $('#enter-word').keypress(function(e){if (e.which == 13)moveloc()});
    </script>
<?php?>
