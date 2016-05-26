var h = require('react-hyperscript');

var createClass = require('../createClass');

var scriptify = function(props) {
	if (!props.type) props = Object.assign({type:'text/javascript'}, props);
	return h('script', props);
};
var linkify = function(props) {
	if (!props.type) props = Object.assign({type:'text/css'}, props);
	if (!props.rel) props = Object.assign({rel:'stylesheet'}, props);
	return h('link', props);
};

var icons = [
	{ rel: "apple-touch-icon", sizes: "57x57", href: "/Images/icons/apple-icon-57x57.png" },
	{ rel: "apple-touch-icon", sizes: "60x60", href: "/Images/icons/apple-icon-60x60.png" },
	{ rel: "apple-touch-icon", sizes: "72x72", href: "/Images/icons/apple-icon-72x72.png" },
	{ rel: "apple-touch-icon", sizes: "76x76", href: "/Images/icons/apple-icon-76x76.png" },
	{ rel: "apple-touch-icon", sizes: "114x114", href: "/Images/icons/apple-icon-114x114.png" },
	{ rel: "apple-touch-icon", sizes: "120x120", href: "/Images/icons/apple-icon-120x120.png" },
	{ rel: "apple-touch-icon", sizes: "144x144", href: "/Images/icons/apple-icon-144x144.png" },
	{ rel: "apple-touch-icon", sizes: "152x152", href: "/Images/icons/apple-icon-152x152.png" },
	{ rel: "apple-touch-icon", sizes: "180x180", href: "/Images/icons/apple-icon-180x180.png" },
	{ rel: "icon", type: "image/png", sizes: "192x192",  href: "/Images/icons/android-icon-192x192.png" },
	{ rel: "icon", type: "image/png", sizes: "32x32", href: "/Images/icons/favicon-32x32.png" },
	{ rel: "icon", type: "image/png", sizes: "96x96", href: "/Images/icons/favicon-96x96.png" },
	{ rel: "icon", type: "image/png", sizes: "16x16", href: "/Images/icons/favicon-16x16.png" },
	{ type: null, rel: "manifest", href: "/manifest.json"}
].map(linkify);

module.exports = createClass({
	displayName: 'Page',
	render: function() {
		return h('html', [
			h('head', [
				h('meta', { charSet: 'utf8' }),
				h('meta', { name: 'viewport', content: 'width=device-width,user-scalable=yes' }),
				h('title', this.props.title),
				...(this.props.scripts||[]).map(scriptify),
				...(this.props.links||[]).map(linkify),
			]),
			h('body', {}, this.props.children)
		]);
	}
});
