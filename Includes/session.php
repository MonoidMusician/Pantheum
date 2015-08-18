<?php
    session_start();
    global $sli, $suid, $suname, $srank, $sudata;
    $sli = false;
    $suid = '';
    $suname = 'error';
    $srank = '';
    $sudata = null;
    
    if ((isset($_SESSION['li'])) && ($_SESSION['li'] == 'true')) {
        $sli = true;
        $suid = $_SESSION['uid'];
        $suname = $_SESSION['username'];
        $srank = $_SESSION['rank'];
        $sudata = $_SESSION['udata'];
    }
    
    if ($sli == true) {
        global $mysqli;
        $M_query = "SELECT forcelogout FROM users WHERE id='$suid';";
        $M_result = $mysqli->query($M_query);
        $M_row = $M_result->fetch_array();
        
        if ($M_row[0] != '') {
            $result = explode(',', $M_row[0]);
            if (($result[1] == '*') || ($result[1] != $_SERVER['REMOTE_ADDR'])) {
                header("Location: /PHP5/logout.php");
                die('');
            }
        }
    }
?>
