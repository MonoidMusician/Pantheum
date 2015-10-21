$(function() {
	if ($('article:first:last').length) return;
	$w = $(window);
	var min_width = 800;
	$('article:first').css('min-width', $('article:first :input').width()+'px');
	var secondary_article = !!$('article + article.secondary').length;
	if ($w.width() < min_width) {
		$('meta[name=viewport]').remove();
		$('head').append('<meta name="viewport" content="width=device-width,initial-scale=0.70,user-scalable=no">');
	}
	var timer = null;
	var resize = function() {
		if ($w.width() < min_width) {
			$('article + article').removeClass('secondary');
			$('article:first').css('width','auto');
			$('article:first').css('max-width','auto');
		} else if (secondary_article) {
			$('article + article').addClass('secondary');
			$('article:first').css('width','calc(70% - 20px)');
			$('article:first').css('max-width','calc(100% - '+$('article + article.secondary').css('min-width')+' - 80px)');
		}
	};
	resize();
	$w.on('resize', function() {
		if (timer !== null) clearTimeout(timer);
		timer = setTimeout(function() {
			resize();
			timer = null;
		}, 100);
	});
});
