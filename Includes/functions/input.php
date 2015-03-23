<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    
    function cleanInput($type, $input) {
        if ($type == "comment") {
            return encodeHex($input);
        } else {
            global $mysqli;
            return $mysqli->real_escape_string(stripslashes(preg_replace($type, '', str_replace(chr(0), '', $input))));
        }
    }

    function encodeHex($input) {
        $zinput = unpack('H*', $input);
        return array_shift($zinput);
    }

    function decodeHex($output) {
        $zoutput = $output;
        return pack('H*', str_replace('\x', '', str_replace(' ', '', $zoutput)));
    }
?>
