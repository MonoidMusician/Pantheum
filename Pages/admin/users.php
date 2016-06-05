<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

	if (!hasACL('admin_panel', 'R', 'S')) {
		sro('/Pages/restricted/admin.php');
		die("");
	}
?>
<h2>Users</h2>
<p>Click on the user in table below to edit settings.</p>
<div id="auUControls"></div>
<div id="auTable"></div>
<div id="auLControls"></div>
<script type="text/javascript">
    $(function() {
        autable = new jTable();
        autable.init('auTable', '/PHP5/admin/users/autget.php', false, true, true);
        autable.setDeleteURI(['/PHP5/admin/users/audelete.php', 'post']);
        autable.setSplits('{[,]}', '{(,)}');
        autable.setControlIDs('auUControls', 'auLControls');
        autable.setUControls(['refresh', 'search']);
        autable.setLControls(['refresh', 'search']);
        autable.setTColumns([['ID', 0, 'number'], ['Username', 1, 'text'], ['Rank', 2, 'drop-down', [['Admin', '1'], ['User', '2'], ['Banned', 'b']]], ['Email', 3, 'email'], ['jcanvas', 'Delete', [20, 20], 'cs,b,m:1:1,l:5:19,l:15:19,l:20:1,l:1:1,m:7:1,l:7:19,m:13:1,l:13:19,m:2:6,l:18:6,m:3:13,l:17:13,m:0:0,ss:#AA0000,w:2,c,s', 'table-delete']]);
        autable.setKeyField(0);
        autable.setBindHandler(function() {
            for (var drpos in this.drows) {
                var rowid = this.drows[drpos];
                var row = this.data[rowid];
                for (var column in this.tcolumns) {
                    var name = this.tcolumns[column][0];
                    if ((name != 'jcanvas') && (name != 'table-checkbox')) {
                        $(document).on('click', '#' + this.telement + '-tbtr-' + rowid + '-td-' + column + ':not(.jTableEditing)', { id: this.data[rowid][0] }, adminLoadUser);
                    }
                }
            }
        });
        autable.setUnbindHandler(function() {
            for (var drpos in this.drows) {
                var rowid = this.drows[drpos];
                var row = this.data[rowid];
                for (var column in this.tcolumns) {
                    var name = this.tcolumns[column][0];
                    if ((name != 'jcanvas') && (name != 'table-checkbox')) {
                        $(document).off('click', '#' + this.telement + '-tbtr-' + rowid + '-td-' + column);
                    }
                }
            }
        });
        autable.load();

    });
</script>
