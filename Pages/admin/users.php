<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireRank('1');
?>
<h2>Users</h2>
<div id="auUControls"></div>
<div id="auTable"></div>
<div id="auLControls"></div>
<script type="text/javascript">
    $(function() {
        autable = new jTable();
        autable.init('auTable', '/PHP5/admin/users/autget.php', true, true, true);
        autable.setSaveURI(['/PHP5/admin/users/auupdate.php', 'post']);
        autable.setCreateURI(['/PHP5/admin/users/aunew.php', 'post']);
        autable.setDeleteURI(['/PHP5/admin/users/audelete.php', 'post']);
        autable.setSplits('{[,]}', '{(,)}');
        autable.setControlIDs('auUControls', 'auLControls');
        autable.setUControls(['new', 'edit', 'refresh', 'search']);
        autable.setLControls(['new', 'edit', 'refresh', 'search']);
        autable.setTColumns([['table-checkbox'], ['ID', 0, 'number'], ['Username', 1, 'text'], ['Rank', 2, 'drop-down', [['Admin', '1'], ['TA', '2'], ['Editor', '3'], ['User', '4'], ['Banned', 'b'], ['Not Approved', 'n']]], ['Email', 3, 'email'], ['jcanvas', 'Delete', [20, 20], 'cs,b,m:1:1,l:5:19,l:15:19,l:20:1,l:1:1,m:7:1,l:7:19,m:13:1,l:13:19,m:2:6,l:18:6,m:3:13,l:17:13,m:0:0,ss:#4466AA,w:2,c,s', 'table-delete'], ['jcanvas', 'Edit', [20, 20], ['cs,b,m:20:20,l:6:6,l:0:3,m:6:6,l:3:0,m:0:20,l:14:6,l:17:0,m:14:6,l:20:3,ss:#4466AA,w:2,c,s', 'cs,b,m:1:1,l:1:19,l:19:19,l:19:5,l:15:1,l:1:1,m:15:1,l:15:5,l:19:5,ss:#4466AA,w:2,c,s'], 'table-edit']]);
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
