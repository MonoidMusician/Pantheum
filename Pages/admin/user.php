<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    requireRank('1');

    $uid = cleanInput('/[^0-9]/', $_GET['id']);
    $user = json_decode(getUser($uid), true);
?>
<h2><?php echo $user['username']; ?></h2>
<div id="asuDetails">
    <p>
        Username: <?php echo $user['username']; ?><br>
        Rank: <?php echo getNamedRank($user['rank']); ?><br>
        Email: <?php echo $user['email']; ?><br>
        Join Date: <?php echo $user['joindate']; ?><br>
        Create IP: <?php echo $user['createip']; ?><br>
        Current IP: <?php echo $user['currentip']; ?><br>
    </p>
</div>
<br>
<br>
<div id="asuEditable">
</div>
<br>
<br>
<div id="asuExtras">
    <h3>Miscellaneous Extras</h3>
    <button id="deletepicks">Delete Picks</button>
    <button id="impersonate">Impersonate User</button>
</div>
<script type="text/javascript">
    $(function() {
        var settings = new jSettings();
        settings.init('asuEditable', '/PHP5/admin/user/load.php');
        settings.setSettings([
            ["jsettings-element", "Username", "username", "hidden", "regular"],
            ["jsettings-element", "Email Address", "email", "text", "regular"],
            ["jsettings-space"],
            ["jsettings-group",
                "password",
                ["jsettings-element", "New Password", "npassword", "password", "regular"],
                ["jsettings-element", "Confirm Password", "cpassword", "password", "regular"],
            ],
        ]);
        settings.setSaveURI("/PHP5/admin/user/save.php");
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
        $(document).on('click', '#deletepicks', function() {
            if (confirm("Are you sure you want to delete <?php echo $user['username']; ?>'s picks?")) {
                $.get('/PHP5/admin/user/deletepicks.php?uid=<?php echo $uid; ?>', function(data) {
                    if (data == 'success') {
                        alert("Successfully deleted <?php echo $user['username']; ?>'s picks.");
                    } else {
                        alert(data);
                    }
                });
            }
        });

        // Misc functions
        $(document).on('click', '#impersonate', function() {
            if (confirm("Are you sure you want to impersonate <?php echo $user['username']; ?>?")) {
                $.get('/PHP5/admin/user/impersonate.php?uid=<?php echo $uid; ?>', function(data) {
                    if (data == 'success') {
                        alert("Successfully impersonated user.");
                        window.location.href="/";
                    } else {
                        alert(data);
                    }
                });
            }
        });
    });
</script>
