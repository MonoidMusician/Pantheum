var h = require('react-hyperscript');

module.exports = function(view) {
	view.RequireFonts = view.createClass({
		displayName: 'view.RequireFonts',
		render: function() {
			return h('style', this.props.fonts.map(
				f => '@font-face {src:url('+f.url+');'+view.expand.proto(f.style)+'}'
			).join(''))
		},
	});

	view.Page = view.createClass({
		displayName: 'Page',
		render: function() {
			return h('html', [
				h('head', [
					h('title', this.props.title),
					...(this.props.scripts||[]).map(s=>h('script', s)),
					...(this.props.links||[]).map(s=>h('link', s)),
				]),
				h('body', {}, this.props.children)
			]);
		}
	});
};
