<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<h2>Preferences</h2>

<span data-i18n="ui.change_language">Language:</span>
<select id="lang" style="width: 200px">
    <option value="en">English</option>
    <option value="la">Latin</option>
</select>
<br>
<br>

Word formatting:
<select id="la_ipa" style="width: 200px">
</select>
<br>
<span id="word-format-ex" class="format-word-la">
Exemplum verbōrum: Salvē! Quid agis? Hoc verbum habet multās fōrmās: oppugnātus‣, et hoc īnflexum: aestⱶmāvissem. Jussa!
</span>

<button id="save">Save</button>

<script>
$('#lang').select2({
    minimumResultsForSearch: Infinity
}).val(pantheum.udata["language"]).change().on('change', function() {
    i18n.setLng($(this).val(), pantheum._private.i18nload);
});
$('#la_ipa').select2({
    minimumResultsForSearch: Infinity,
    data: Object.keys(la_ipa.transforms)
}).val(la_ipa.transform_key).on('change', function() {
    la_ipa.select_transformer($(this).val()).format();
}).trigger('change');

$('#save').on('click', function() {
    $.post('/PHP5/user/udata-set.php', {'key':'la_ipa','value':$('#la_ipa').val()}, function(data) {
        if (data === "success") {
            $.post('/PHP5/user/udata-set.php', {'key':'language','value':$('#lang').val()}, function(data) {
                if (data === "success") successTip("Preferences updated successfully");
                else if (data === "1") errorTip("Unknown error. (Try again or report to webmaster.)");
                else if (data === "2") errorTip("Bad parameters.");
                else if (data === "3") errorTip("Unknown user. Try logging out and back in.");
                else if (data === "4") errorTip("You have been logged out. Please log in and try again.");
            });
        }
        else if (data === "1") errorTip("Unknown error. (Try again or report to webmaster.)");
        else if (data === "2") errorTip("Bad parameters.");
        else if (data === "3") errorTip("Unknown user. Try logging out and back in.");
        else if (data === "4") errorTip("You have been logged out. Please log in and try again.");
    });
});
</script>
