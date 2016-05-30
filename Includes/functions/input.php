<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');

    function cleanInput($type, $input) {
        if ($type == "comment") {
            return encodeHex($input);
        } else if ($type == "prepared") {
	    return cleanPrepared($input);
	} else {
            global $mysqli;
            return $mysqli->real_escape_string(cleanPrepared(preg_replace($type, '', cleanPrepared($input))));
        }
    }

    function cleanPrepared($input) {
        $result = stripslashes(strip_tags(str_replace(chr(0), '', $input)));
	while ($result != stripslashes(strip_tags($result))) {
	    $result = stripslashes(strip_tags($result));
	}
	return $result;
    }

    function encodeHex($input) {
        $zinput = unpack('H*', $input);
        return array_shift($zinput);
    }

    /*
    Currently unused.

    function decodeHex($output) {
        $zoutput = $output;
        return pack('H*', str_replace('\x', '', str_replace(' ', '', $zoutput)));
    }
    */
?>
