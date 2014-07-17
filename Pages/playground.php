<header>
    <h1>NO TOUCHY!</h1>
</header>
<article>
    <p>
        <!--<?php
            $a = [
                "hello" => "hi",
                "2" => 1,
            ];
            echo $a["hello"];
        ?>-->
        <!--<ul><?php
            foreach ($a as $key => $value) {
                ?><li><?php
                echo "\$a[\"$key\"] equals \"$value\"";
                ?></li><?php
            }
        ?></ul>-->
        <?php
        require_once('/var/www/latin/config.php');
        $cases = ["nominative", "accusative", "dative", "ablative",
                  "vocative", "genitive", "locative"];
        sro('/PHP5/phplang/word_types.php');
        sro('/PHP5/phplang/tuple.php');
        ?><pre><?php
        print_r($types);
        print_r(tuple::fill_array(
            $types["Noun"]->attribs,
            ["nominative", "decl-1"]
        ));
        ?></pre><?php
        ?>
    </p>

    <p>
        <p>Simple table with header</p>
        <table>
            <tr>
                <th>Attribute</th>
                <th>Possible Values</th>
            </tr>
            <?php
                foreach ($types["Noun"]->attribs as $attrib => $values) {
                ?>
                <tr>
                    <td><?=$attrib?></td>
                    <td><?= implode(", ", $values)?></td>
                </tr>
                <?php
                }
            ?>
        </table>
    </p>
</article>

<style type="text/css">
/* Style for all rows */
tr { font: bold 16px Arial; }
/* Style for rows with a row before: 2nd, 3rd, 4th, ... */
tr + tr { font: 12px Arial; }

/* Style for all columns */
td { background-color: #cccccc; }
/* Style for rows with a row before: 2nd, 3rd, 4th, 5th */
td + td { background-color: #eeeeee; }
/* Style for rows with 4 rows before: 5th */
td + td + td + td + td { background-color: #cccccc; }
</style>

<table>
<tr><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td></tr>
<tr><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td></tr>
<tr><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td></tr>
<tr><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td></tr>
<tr><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td><td>Cell</td></tr>
</table>
