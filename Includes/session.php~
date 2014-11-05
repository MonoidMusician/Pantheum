<?php
    session_start();
    global $sli, $sgtitle, $sgid, $sgtype,$suid, $suname, $srank;
    $sli = false;
    $sgid = '';
    $sgtitle = 'Tourney Host';
    $sgtype = '';
    $suid = '';
    $suname = 'error';
    $srank = '';
    
    if ((isset($_SESSION['li'])) && ($_SESSION['li'] == 'true')) {
        $sli = true;
        $sgid = $_SESSION['gid'];
        if ((isset($_SESSION['gtitle'])) && ($_SESSION['gtitle'] != '')) {
            $sgtitle = $_SESSION['gtitle'];
        }
        if ((isset($_SESSION['gtype'])) && ($_SESSION['gtype'] != ''))  {
            $sgtype = $_SESSION['gtype'];
        }
        $suid = $_SESSION['uid'];
        $suname = $_SESSION['username'];
        $srank = $_SESSION['rank'];
        
    }
?>
