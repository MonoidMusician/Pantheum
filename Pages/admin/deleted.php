<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('1');
?>
<h2>Deleted</h2>
<div id="adUControls"></div>
<div id="adTable"></div>
<div id="adLControls"></div>
<script type="text/javascript">
    $(function() {
        var adtable = new jTable();
        adtable.init('adTable', '/PHP5/admin/deleted/adtget.php', false, false, true);
        adtable.setSplits('{[,]}', '{(,)}');
        adtable.setControlIDs('adUControls', 'adLControls');
        adtable.setUControls(['refresh', 'search']);
        adtable.setLControls(['refresh', 'search']);
        adtable.setTColumns([['table-checkbox'], ['ID', 0, 'number'], ['FID', 1, 'text'], ['Data', 2, 'hextext']]);
        adtable.setKeyField(0);
        adtable.load();
    });
</script>
