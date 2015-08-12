<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    sro('/PHP5/lib/PHPLang/db.php');
    sro('/PHP5/lib/PHPLang/display.php');
    
    requireLoggedIn(TRUE);
?>
<h2>Vocab</h2>
<div id="uvUControls"></div>
<div id="uvtable"></div>
<div id="uvLControls"></div>
<script type="text/javascript">
    $(function() {
        uvtable = new jTable();
        uvtable.init('uvtable', '/PHP5/admin/words/uvtget.php', true, true, true);
        uvtable.setSaveURI(['/PHP5/admin/words/uvupdate.php', 'post']);
        uvtable.setCreateURI(['/PHP5/admin/words/uvnew.php', 'post']);
        uvtable.setDeleteURI(['/PHP5/admin/words/uvdelete.php', 'post']);
        uvtable.setSplits('{[,]}', '{(,)}');
        uvtable.setControlIDs('uvUControls', 'uvLControls');
        uvtable.setUControls(['new', 'edit', 'refresh', 'search']);
        uvtable.setLControls(['new', 'edit', 'refresh', 'search']);
        uvtable.setTColumns([['table-checkbox'], ['ID', 0, 'number'], ['Name', 1, 'text'], ['Part of speech', 2, 'drop-down', [['N.', 'noun'], ['Adj.', 'adjective'], ['V.', 'verb'], ['Adv.', 'adverb'], ['Pro.', 'pronoun']]], ['jcanvas', 'Delete', [20, 20], 'cs,b,m:1:1,l:5:19,l:15:19,l:20:1,l:1:1,m:7:1,l:7:19,m:13:1,l:13:19,m:2:6,l:18:6,m:3:13,l:17:13,m:0:0,ss:#4466AA,w:2,c,s', 'table-delete'], ['jcanvas', 'Edit', [20, 20], ['cs,b,m:20:20,l:6:6,l:0:3,m:6:6,l:3:0,m:0:20,l:14:6,l:17:0,m:14:6,l:20:3,ss:#4466AA,w:2,c,s', 'cs,b,m:1:1,l:1:19,l:19:19,l:19:5,l:15:1,l:1:1,m:15:1,l:15:5,l:19:5,ss:#4466AA,w:2,c,s'], 'table-edit']]);
        uvtable.setKeyField(0);
        uvtable.setBindHandler(function() {
            for (var drpos in this.drows) {
                var rowid = this.drows[drpos];
                var row = this.data[rowid];
                for (var column in this.tcolumns) {
                    var name = this.tcolumns[column][0];
                    if ((name != 'jcanvas') && (name != 'table-checkbox')) {
                        $(document).on('click', '#' + this.telement + '-tbtr-' + rowid + '-td-' + column + ':not(.jTableEditing)', { id: this.data[rowid][0] }, function() {});
                    }
                }
            }
        });
        uvtable.setUnbindHandler(function() {
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
        uvtable.load();

    });
</script>
