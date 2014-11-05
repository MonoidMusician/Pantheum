<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    sro('/PHP5/lib/PHPLang/display.php');

    $result = [];
    if (!is_array($_SESSION["current_answer"])) exit("session timed out");
    foreach ($_SESSION["current_answer"] as $name=>$values) {
        $answer = $_POST[$name];
        $answer2 = unformat_word($answer);
        if (array_key_exists("correct", $values)
        and array_key_exists("acceptable", $values)
        and is_array($values["correct"])
        and is_array($values["acceptable"])) {
            $also = $values["correct"];
            $correct = $values["correct"] ? "“".implode("” or “", $values["correct"])."”" : "";
            $values = $values["acceptable"];
        } else $correct = ($also=$values) ? "“".implode("” or “", $values)."”" : "";
        $score = FALSE;
        foreach ($values as $value) {
            if (!$score and $answer2 == ($_val=unformat_word($value))) {
                $score = TRUE;
                $_also = $also;
                foreach ($_also as $key => $v2) {
                    if (unformat_word($v2)==$_val) unset($also[$key]);
                }
                break;
            }
        }
        foreach ($also as &$definition) {
            $paren = -1;
            $new = "";
            for ($i=0;$i<strlen($definition);$i++) {
                if ($definition[$i] == "(" or $definition[$i] == ")")
                    if ($i == 0 or $paren >= 0)
                    { $paren += 1; continue; }
                if ($paren !== 0 and (!$new or !ctype_space($definition[$i]))) $new .= $definition[$i];
            }
        }
        if (!$score) {
            $result[$name] = [$answer, $correct];
        } else {
            if ($also) $also = " (also: “".implode("” or “", $also)."”)";
            else $also = "";
            $result[$name] = [$value.$also, true];
        }
    }
    
    $_SESSION["current_answer"] = NULL;
    
    print json_encode($result);

/*
    $answer = $_POST;
    $correct = array_key_exists("current_answer", $_SESSION) ? $_SESSION["current_answer"] : NULL;
    if ($correct) {
        if (count($correct) == 1) {
            $answer = $answer[array_keys($correct)[0]];
            $correct = $correct[array_keys($correct)[0]];
            if ($answer == $correct) {
                echo "Correctum est $answer! Bene";
            } else {
                echo "Minime, non est $answer, esset $correct.";
            }
        }
        unset($_SESSION["current_answer"]);
    }
*/
?>
