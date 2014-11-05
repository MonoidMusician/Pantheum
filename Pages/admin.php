<?php
    global $sli,$sgid,$srank;
    if ((isset($sli)) && ($sli == 'true')) {
        if ($srank == 1) {
            sro('/Pages/admin/admin.php');
        } else {
            sro('/Pages/restricted/admin.php');
        }
    } else {
        sro('/Pages/restricted/logged-out.php');
    }
?>
