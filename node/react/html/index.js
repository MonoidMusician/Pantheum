var h = require('react-hyperscript');
var ReactDOMServer = require('react-dom/server');

var createClass = require('../createClass');

var RawPage = require('./RawPage');

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
];

var rawscript = function(content) {
	return h('script', {dangerouslySetInnerHTML:{__html:content.replace(/\n\s*/g,' ')}});
};

module.exports = createClass({
	render: function renderPage() {
		var pages = require('../pages');
		var page = pages[this.props.page];
		var props = Object.assign({}, this.props, {
			children: undefined,
			links: [
				'/CSS/react.css',
				'/CSS/jquery.qtip.min.css',
				'/Images/open-iconic/font/css/open-iconic.css',
			].map(href=>({href})).concat(icons),
			scripts: [
				'/JS/lib/jquery.js',
				'/JS/lib/unorm.js',
				'/JS/lib/i18next.js',
				'/JS/lib/jquery.autosize.input.min.js',
				'/JS/lib/jquery.qtip.min.js',
				'/JS/lib/select2.min.js',
				'https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.8.0/polyfill.min.js',
				'/node/build/static.js',
				'/node/build/pantheum.js',
			].map(src=>({src})),
		});
		if (page.title) props.title = page.title;
		var children = [];
		if (page.heading) children.push(h('h1#title', page.heading));
		if (this.props.data) {
			var component = page.render(this.props.data, this.props.req);
			var content = {dangerouslySetInnerHTML:{__html:ReactDOMServer.renderToString(component)}};
			children.push(h('section#content', content));
			children.push(rawscript(`(function(pages){
				var page = pages[${JSON.stringify(this.props.page)}], data = (page.fromJSON ? page.fromJSON.bind(page) : a=>a)(${JSON.stringify((page.toJSON ? page.toJSON.bind(page) : a=>a)(this.props.data))});
				ReactDOM.render(
					page.render(data),
					document.getElementById('content')
				);
			}(pantheum.view.pages))`));
		} else {
			children.push(h('section#content'));
			children.push(rawscript(`(function(pages){
				var page = pages[${JSON.stringify(this.props.page)}];
				Promise.resolve(page.data()).then(data => {
					ReactDOM.render(
						page.render(data),
						document.getElementById('content')
					)
				});
			}(pantheum.view.pages))`));
		}
		return RawPage.h(props, children);
	}
});
