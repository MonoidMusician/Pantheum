<?php
    global $sli,$sgid,$srank;
    if ((isset($sli)) && ($sli == 'true')) {
        sro('/Pages/restricted/logged-in.php');
    } else {
        sro('/Pages/login/login.php');
    }
?>
