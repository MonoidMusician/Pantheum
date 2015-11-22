if (!pantheum) panthem = {_private:{}};

pantheum.lang = function() {
	return pantheum.udata && pantheum.udata["language"]
		? pantheum.udata["language"]
		: 'en';
};
pantheum._private.i18nload = function(err, t) {
	if (err) console.log(err);
	pantheum.update();
};
pantheum.update = function(element) {
	if (!element) element = 'body';
	var $e = $(element);
	var lang = pantheum.lang();
	$e.i18n();
	$e.find('[data-i18n]').removeClass(function(index, css) {
		return (css.match(/(^|\s)format-word-\S+/g) || []).join(' ');
	}).addClass('format-word-'+lang).attr('data-original-word0', '');
	la_ipa.format($e);
	$e.find('[title]:not(abbr):not(.actionable):not(.select2-selection__rendered)').qtip({
		style:{
			classes:"qtip-light"
		},
		position:{
			my:"center left",
			at:"center right"
		},
		hide: {
			fixed: true,
			delay: 100,
		}
	});
	$e.find('abbr[title]').qtip({
		style:{
			classes:"qtip-light qtip-abbr"
		},
		position:{
			at:"top center",
			my:"bottom center",
			adjust: {y:5},
		},
		hide: {
			fixed: true,
			delay: 100,
		}
	});
	$e.find('.actionable[title]').qtip({
		style:{
			classes:"qtip-light qtip-actionable"
		},
		position:{
			my:"top center",
			at:"bottom center",
			adjust: {y:-5},
		},
		hide: {
			fixed: true,
			delay: 100,
		}
	});
	$e.find('.inflection').each(function() {
		console.log(this);
		pantheum.sortword(this);
	});
	return $e;
};
pantheum.sortword = function($word) {
	var cases = pantheum.udata && pantheum.udata["cases"];
	if (!cases) return;
	cases = cases.split(",");
	var case_index = {};
	$.each(cases, function(i,c) {
		case_index[c] = i;
	});
	var $word = $($word); // just in case
	var $rows = $word.find('tr > th:first-child.minor').parent();
	var $parent = $rows.parent();
	$rows.sort(function(a,b) {
		var $a = $(a).find('th:first-child').text().toLowerCase(),
		    $b = $(b).find('th:first-child').text().toLowerCase();
		var i_a = case_index[$a], i_b = case_index[$b];
		if (i_a > i_b) return  1;
		if (i_a < i_b) return -1;
		return 0;
	});
	$rows.detach().appendTo($parent);
};
pantheum.init = function() {
	i18n.init({
		fallbackLng: 'en'
	});
	i18n.setLng(pantheum.lang(), pantheum._private.i18nload);
	i18n.translatable = function(s) {
		var s2 = i18n.t(s);
		if (!s2) s2 = s;
		return '<span data-i18n="'+s+'">'+s2+'</span>';
	};
}
