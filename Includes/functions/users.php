<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    
    function getUser($uid) {
        global $mysqli, $suid;
        
        if (!isset($uid)) {
            $uid = $suid;
        }
        
        $result = array();
        $M_query = "SELECT * FROM users WHERE id='$uid';";
        $M_result = $mysqli->query($M_query);
        if ($M_row = $M_result->fetch_assoc()) {
            $result = $M_row;
            
            // Sanitize by default...
            unset($result['password']);
            unset($result['createip']);
            unset($result['seccode']);
            
            return json_encode($result);
        } else {
            return false;
        }
    }
?>
