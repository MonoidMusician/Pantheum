<?php
    session_start();

    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    
    global $sli, $sgtitle, $sgid, $sgtype,$suid, $suname, $srank;
    $sli = "";
    $sgtitle = "";
    $sgid = "";
    $sgtype = "";
    $suid = "";
    $suname = "";
    $srank = "";

    session_destroy();
?>

