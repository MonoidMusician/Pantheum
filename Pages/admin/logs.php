<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('1');
?>
<h3>Logs</h3>
<div id="alUControls"></div>
<div id="alTable"></div>
<div id="alLControls"></div>
<script type="text/javascript">
    $(function() {
        var altable = new jTable();
        altable.init('alTable', '/PHP5/admin/logs/altget.php', false, false, true);
        altable.setSplits('{[,]}', '{(,)}');
        altable.setControlIDs('alUControls', 'alLControls');
        altable.setUControls(['refresh', 'search']);
        altable.setLControls(['refresh', 'search']);
        altable.setTColumns([['table-checkbox'], ['ID', 0, 'number'], ['IP', 1, 'text'], ['Time', 2, 'datetime'], ['Userid', 3, 'number'], ['Page', 4, 'text'], ['Type', 5, 'text'], ['Content', 6, 'hextext']]);
        altable.setKeyField(0);
        altable.load();
    });
</script>
