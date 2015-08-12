<?php
    global $sli,$sgid,$srank;
    if ((isset($sli)) && ($sli == 'true')) {
        sro('/Pages/user/user.php');
    } else {
        sro('/Pages/restricted/logged-out.php');
    }
?>
