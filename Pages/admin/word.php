<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    sro('/PHP5/lib/PHPLang/db.php');
    sro('/PHP5/lib/PHPLang/display.php');
    
    requireRank('1');
    
    $uid = cleanInput('/[^0-9]/', $_GET['id']);
    $word = WORD(defaultDB(), intval($uid));
?>
<h2><?php echo $word->name(); ?></h2>
<div id="aswDetails">
    <p>
        Name: <?php echo $word->name(); ?><br>
        Part of speech: <?php echo display_spart($word->spart()); ?><br>
    </p>
</div>
<br>
<br>
<div id="aswEditable">
</div>
<br>
<br>
<div id="aswExtras">
    <h3>Miscellaneous Extras</h3>
    <button id="logoff">Force Log Off</button>
</div>
<script type="text/javascript">
    $(function() {
        var settings = new jSettings();
        settings.init('aswEditable', '/latin/PHP5/admin/word/load.php');
        settings.setSettings([
            ["jsettings-element", "Name", "name", "text", "regular"],
            ["jsettings-element", "Part of speech", "speech", "text", "regular"],
        ]);
        settings.setSaveURI("/latin/PHP5/admin/word/save.php");
        settings.setSplit(',');
        settings.setSaveBehavior('button');
        settings.setLabels(true);
        settings.setSuccess('success');
        settings.setURLAppend('&uid=<?php echo $uid; ?>');
        settings.setBindHandler(function() {
            $(document).off('click', '#' + this.selement + '-submit');
            $(document).on('click', '#' + this.selement + '-submit', { instance: this }, function(event) {
                event.data.instance.saveData();
                event.data.instance.storage['npassword'] = loginHash(event.data.instance.storage['username'], event.data.instance.storage['npassword']);
                event.data.instance.storage['cpassword'] = loginHash(event.data.instance.storage['username'], event.data.instance.storage['cpassword']);
                
                if (event.data.instance.changed.length != 0) {
                    $('#' + event.data.instance.selement + '-saving').html('Saving...');
                    event.data.instance.ledited = event.data.element;
                    event.data.instance.pushData();
                    event.data.instance.load();
                }
            });
        });
        settings.load();
        
        // Misc functions
        $(document).on('click', '#logoff', function() {
            $.get('/latin/PHP5/admin/user/logoff.php?uid=<?php echo $uid; ?>', function(data) {
                if (data == 'success') {
                    alert("Successfully logged off <?php echo $user['username']; ?>.");
                } else {
                    alert(data);
                }
            });
        });
    });
</script>

