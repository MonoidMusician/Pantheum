<?php
    require_once('/var/www/config.php');
    session_start();

    sro('/Includes/mysql.php');

    global $mysqli;

    $suid = $_SESSION['uid'];

    $M_query = "SELECT * FROM users WHERE id='$suid';";
    $M_result = $mysqli->query($M_query);
    $M_row = $M_result->fetch_assoc();

    $current = json_decode($M_row['currentip'] ? $M_row['currentip'] : '[]', true);
    foreach ($current as $key=>$ip) {
        if ($ip == $_SERVER['REMOTE_ADDR']) {
            unset($current[$key]);
        }
    }
    $current = json_encode($current);

    $M_query2 = "UPDATE users SET currentip='$current' WHERE id='$suid';";
    $M_result2 = $mysqli->query($M_query2);

    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }


    $sli = "";
    $suid = "";
    $suname = "";
    $srank = "";

    session_destroy();

    header("Location: /logged-out.php");
?>

