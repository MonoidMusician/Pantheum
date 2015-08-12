<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
    
    requireLoggedIn(TRUE);
?>
<h2>Preferences</h2>

Language:
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
Exemplum verbōrum: Salvē! Quid agis? Hoc verbum habet multās fōrmās: oppugnātus‣, et hoc īnflexum: aestəmāvissem. Jussa!
</span>

<script>
$('#lang').select2({
    minimumResultsForSearch: Infinity
});
$('#la_ipa').select2({
    minimumResultsForSearch: Infinity,
    data: Object.keys(la_ipa.transforms)
}).val(la_ipa.transform_key).on('change', function() {
    la_ipa.select_transformer($(this).val()).format();
}).trigger('change');
</script>
