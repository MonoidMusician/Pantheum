<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');

$db = defaultDB();
$id2vals = "{"; # for JS
$dependencies = "{";
if (count($_GET)) {
    if (!array_key_exists("lang", $_GET) or !(
        $langs = vec_norm(explode(",", $_GET["lang"]), "trim")
        ))
        { $langs = 'la'; }

    if (!array_key_exists("name", $_GET) or !(
        $names = vec_norm(explode(",", $_GET["name"]), "trim")
        ))
        { $names = NULL; }

    if (!array_key_exists("spart", $_GET) or !(
        $sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
        ))
        { $sparts = NULL; }

    if (!array_key_exists("attr", $_GET) or !(
        $attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
        ))
        { $attrs = []; }

    if (array_key_exists("new", $_GET) and $_GET["new"] === "true") {
        global $sql_stmts;
        if (count($names) === 1 and count($langs) === 1 and count($sparts) === 1) {
            $_name = $names[0]; $_lang = $langs[0]; $_spart = $sparts[0];
            sql_exec($sql_stmts["word_name,word_lang,word_spart->new in words"], ["sss", $_name, $_lang, $_spart]);
        }
    }

    $searcher = $db->searcher();
    if ($names)
        $searcher = $searcher->name($names);
    if ($langs)
        $searcher = $searcher->lang($langs);
    if ($sparts)
        $searcher = $searcher->spart($sparts);
    foreach ($attrs as $attr) {
        $a = NULL;
        if (strpos($attr,"=") === FALSE)
            $a = ATTR($attr);
        else {
            list ($name,$value) = explode("=",$attr,2);
            $a = ATTR($name,$value);
        }
        if ($a !== NULL)
            $searcher = $searcher->only_with_attr($a);
    }
    $list = $searcher->all();
    foreach ($list as $w) {
        $id = $w->id();
        $id2vals .= "$id:[";
        $dependencies .= "$id:[";
        if (array_key_exists("set_$id", $_GET)) {
            list ($path,$val) = explode(":",$_GET["set_$id"],2);
            $path = PATH($w,explode("/", $path));
            $path->set($val);
            $w->add_path($path);
        } else {
            $path = $w->path();
            $val = NULL;
        }
        display_word_entry($w, !$val);?><br><?php
        $_level = array_merge($w->mgr()->simple_keys, $w->mgr()->recursive_keys);
        foreach ($w->mgr()->all_sub_keys as $k) {
            $first_level = in_array($k, $_level);
            $isselector = in_array($k, $w->mgr()->recursive_keys);
            if ($isselector) {
                $dependencies .= "['$k',{";
                $depaths = $w->mgr()->level[$k];
                foreach ($depaths as $_k => $depath) {
                    $dependencies .= "'$_k':{";
                    #var_dump($depath->key2values);
                    $vec = $depath->all_sub_keys;
                    if (!empty($vec)) {
                        $vec = array_map(function($k) use($depath,$w) {
                            $v = $w->mgr()->key2values[$k];
                            $v2 = array_map(function($v) use($depath) {
                                return "'$v', ".(array_key_exists($v, $depath->value2key) ? "true":"false");
                            }, $v);
                            return "'$k':[[".implode("],[", $v2)."]]";
                        }, $vec);
                        $dependencies .= implode(",", $vec);
                    }
                    $dependencies .= "},";
                }
                $dependencies .= "}],";
            }
            $id2vals .= "'$k',";
            ?><span id="word<?= $id ?>-<?= $k ?>" class="select"><?php
                $values = [NULL];
                $values = array_merge($values, $w->mgr()->key2values[$k]);
                #var_dump($values);
                foreach ($values as $v) {
                    ?><div id="word<?= $id ?>-div-<?= $v ?>"><label>
                    <input class="inputlabel" type="radio"
                           name="word<?= $id ?>-<?= $k ?>"
                           id="word<?= $id ?>-div-<?= $v ?>" <?php
                        if ($path->key_value($k) == $v) { ?>checked<?php }
                    ?> value="<?= $v ?>" ><?= $v ?></label></div><?php
                }
            ?></span><?php
        }
        $id2vals .= "],";
        $dependencies .= "],";
        ?><input id="word<?= $id ?>_value" type="text" value="<?= $val ?>" placeholder="Value of inflection" required>
        <button id="word<?= $id ?>_button" onclick="moveloc(<?= $id ?>)">Enter</button>
        <script type="text/javascript">
        $(function() {
            $('#word<?= $id ?>_value').keypress(function(e){if (e.which == 13)moveloc(<?= $id ?>)});
        });
        </script>
        <hr><?php
    }
} else {
    $langs = ["la"]; $names = $sparts = [];
}

$id2vals .= "}";
$dependencies .= "}";
#echo($dependencies);
?>
Find words:
    <input id="enter-names" type="text" value="<?= safe_get('name', $_GET) ?>" placeholder="name, ...">
    <span class="select">
<?php
    foreach ($db->langs() as $l) {
        ?><div><label><input type="checkbox" name="enter-lang" <?php
        if ($langs !== NULL and in_array($l, $langs)) {
            ?>checked<?php
        }
        ?> value="<?= $l ?>" ><?= $l ?></label></div><?php
    }
?>
    </span>
    <span class="select">
<?php
    foreach ($db->sparts() as $s) {
        ?><div><label><input type="checkbox" name="enter-spart" <?php
        if ($sparts !== NULL and in_array($s, $sparts)) {
            ?>checked<?php
        }
        ?> value="<?= $s ?>" ><?= $s ?></label></div><?php
    }
?>
    </span>
    <input id="enter-attrs" type="text" value="<?= safe_get('attr', $_GET) ?>" placeholder="attr[=value], ...">
    <button onclick="moveloc()">Search</button>
    <button onclick="moveloc(undefined, true)">Add</button>

    <script type="text/javascript">
    var id2vals = <?= $id2vals ?>;
    var dependencies = <?= $dependencies ?>;
    //alert('hi');
    function getcheckbox(name) {
        var ret=[];
        $('input:checkbox[name='+name+']:checked').each(function() {
            ret.push($(this).val());
        });
        return ret.join();
    }
    function getradio(name) {
        var ret=[];
        $('input:radio[name='+name+']:checked:visible').each(function() {
            ret.push($(this).val());
        });
        return ret ? ret[0] : null;
    }
    function getloc(id, makenew) {
        var loc = "?", op = "", r;
        if ($('#enter-names').val()) {
            loc += op + "name=" + encodeURIComponent($('#enter-names').val());
            op = "&";
        } else if (makenew) return;
        if ($('#enter-attrs').val()) {
            loc += op + "attr=" + encodeURIComponent($('#enter-attrs').val());
            op = "&";
        }
        r = getcheckbox("enter-lang");
        if (r) {
            loc += op + "lang=" + encodeURIComponent(r);
            op = "&";
        } else if (makenew) return;
        r = getcheckbox("enter-spart");
        if (r) {
            loc += op + "spart=" + encodeURIComponent(r);
            op = "&";
        } else if (makenew) return;
        if (!makenew && (id || id === 0) && $('#word'+id+'_value').val()) {
            var path = "", sep = "";
            id2vals[id].forEach(function(key) {
                var r = getradio('word'+id+'-'+key);
                if (!r) return;
                path += sep + r;
                sep = "/";
            });
            loc += op + "set_"+id+"=" + encodeURIComponent(path+":"+$('#word'+id+'_value').val());
            op = "&";
        }
        if (makenew) {
            loc += op + "new=true";
            op = "&";
        }
        if (loc !== "?")
            return loc;
    }
    function moveloc(id, makenew) {
        loc = getloc(id, makenew);
        if (loc)
            location.href = loc;
    }
    $(function(){
        $.each(dependencies, function(id, vals) { $.each(vals, function(_, val) {
            //alert(val);
            var key = val[0];
            var keys = val[1];
            var lastkey = null;
            var ch = false;
            var first = true;
            var perkey = function(key, children) {
                //alert(key);
                //if (!first) alert(ch+" "+children);
                //var ch = el.is(':checked') && el.is(':visible');
                //alert('checked: '+ch+' ('+el.is(':checked')+', '+el.is(':visible')+')');
                $.each(children, function(key, values) {
                    var child = $('#word'+id+'-'+key);
                    if (ch) child.show(); else child.hide();
                    //alert(values);
                    $.each(values, function(_, val) {
                        var child = $('#word'+id+'-div-'+val[0]);
                        if (ch && val[1]) child.show(); else child.hide();
                    });
                });
            };
            var callback = function(target) {
                var key = target.prop('id');
                key = key.replace('word'+id+'-div-', '');
                //alert(key);
                if (key in keys || key === "") {
                    if (lastkey !== null) {
                        ch = false;
                        perkey(lastkey, keys[lastkey]);
                        lastkey = null;
                    }
                    if (key in keys) {
                        ch = true;
                        perkey(key, keys[key]);
                        lastkey = key;
                    }
                }
            };
            $.each(keys, perkey);
            first = false;
            var el = $('input[name="word'+id+'-'+key+'"]');
            $(document).on("change", el, function(event) {
                callback($(event.target));
            });
            if (el.filter(":checked").length != 0) {
                callback(el.filter(":checked"));
            }
        })});
        $('#enter-names').keypress(function(e){if (e.which == 13)moveloc()});
        $('#enter-attrs').keypress(function(e){if (e.which == 13)moveloc()});
    });
    </script>
<?php?>
