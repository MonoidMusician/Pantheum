<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    sro('/PHP5/lib/PHPLang/db.php');

    if (!isLoggedIn()) {
		sro('/Pages/restricted/logged-out.php');
		die("");
	}

	global $suid;
	$level = 'S';
	if (isset($_GET['uid']) && $suid != $_GET['uid']) {
		$level = 'E';
	}

	if (!hasACL('user_settings', 'R', $level)) {
		sro('/Pages/restricted/admin.php');
		die("");
	}
?>
<h2 data-i18n="preferences">Preferences</h2>

<span data-i18n="ui.change_language">Language</span>:
<select id="lang" style="width: 200px">
    <option value="en">English</option>
    <option value="la">Latin</option>
</select>
<br>
<br>

<span data-i18n="word_formatting">Word formatting</span>:
<select id="la_ipa" style="width: 200px">
</select>
<br>

<label><span data-i18n="archaic_translations">Archai&#x308;c translations</span>:
<input id="archtrans" type="checkbox"></label>

<br><br>

<span data-i18n="order_cases">Order of cases</span>:
<div>
<ul id="cases">
<?php
    foreach (defaultDB()->get_mgr("la","noun")->key2values["case"] as $case) {
?>
<li><span class="value"><?= ucfirst($case) ?></span>
<?php
    }
?>
</ul>
</div>

<span id="word-format-ex" class="format-word-la">
Exemplum verbōrum: Salvē! Quid agis? Hoc verbum habet multās fōrmās: oppugnātus‣, et hoc īnflexum: aestⱶmāvissem. Jussa! Euge!
</span>

<button id="save" data-i18n="ui.save">Save</button>

<script>
$('#lang').select2({
    minimumResultsForSearch: -1
}).val(pantheum.lang()).change().on('change', function() {
    pantheum.udata["language"] = $(this).val();
    i18n.setLng(pantheum.udata["language"], pantheum._private.i18nload);
});
$('#la_ipa').select2({
    minimumResultsForSearch: -1,
    data: Object.keys(la_ipa.transforms)
}).val(la_ipa.transform_key).on('change', function() {
    la_ipa.select_transformer($(this).val()).format();
}).trigger('change');
if (pantheum.udata["archtrans"] == "true")
    $('#archtrans').attr('checked', true);

$('#cases').sortable();

$('#save').on('click', function() {
    var cases = [];
    $('#cases li').each(function() {
        cases.push($(this).find('.value').text());
    });
    var keys = [
        {'key':'la_ipa','value':$('#la_ipa').val()},
        {'key':'language','value':$('#lang').val()},
        {'key':'archtrans','value':$('#archtrans').is(':checked')},
        {'key':'cases','value':cases.join(',').toLowerCase()},
    ]
    var r = function(data) {
        if (data === "success")
        { if (keys.length) $.post('/PHP5/user/udata-set.php', keys.shift(), r); }
        else if (data === "1") errorTip("Unknown error. (Try again or report to webmaster.)");
        else if (data === "2") errorTip("Bad parameters.");
        else if (data === "3") errorTip("Unknown user. Try logging out and back in.");
        else if (data === "4") errorTip("You have been logged out. Please log in and try again.");
    }
    r('success');
});
</script>
