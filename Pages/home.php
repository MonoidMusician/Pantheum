<?php
    global $sli,$sgid,$srank;
    if ((isset($sli)) && ($sli == 'true')) {
        sro('/Pages/home/home-logged-in.php');
    } else {
        sro('/Pages/home/home-logged-out.php');
    }
?>
