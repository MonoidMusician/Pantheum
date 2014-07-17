<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    if (isset($_GET['id'])) {
        global $suid;
        $uid = $suid;
        $id = cleanInput('/[^0-9a-zA-Z]/', $_GET['id']);
        $result = '';
        $user = json_decode(getUser($uid), true);
        
        if ($id != $_GET['id']) {
            die("error");
        }
        
        if (($id == 'username') || ($id == 'email')) {
            $result = $user[$id];
        } else if ($id == 'password') {
            $result = json_encode(['opassword,', 'npassword,', 'cpassword,']);
            die("$result");
        } else {
            $tmp = json_decode($user['settings'], true);
            $result = $tmp[$id];
        }
        
        
        print cleanInput('/[^0-9a-zA-Z]/', $_GET['id']) . ',' . $result;
    } else {
        die("error");
    }
?>
