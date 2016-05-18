var h = require('react-hyperscript');
var ReactDOMServer = require('react-dom/server');

module.exports = function(view) {
	view.RequireFonts = view.createClass({
		displayName: 'view.RequireFonts',
		render: function() {
			return h('style', this.props.fonts.map(
				f => '@font-face {src:url('+f.url+');'+view.expand.style.proto(f.style)+'}'
			).join(''))
		},
	});

	var scriptify = function(props) {
		if (!props.type) props = Object.assign({type:'text/javascript'}, props);
		return h('script', props);
	};
	var linkify = function(props) {
		if (!props.type) props = Object.assign({type:'text/css'}, props);
		if (!props.rel) props = Object.assign({rel:'stylesheet'}, props);
		return h('link', props);
	};
	var icons = [{ rel: "apple-touch-icon", sizes: "57x57", href: "/Images/icons/apple-icon-57x57.png" },
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
	{ type: null, rel: "manifest", href: "/manifest.json"}].map(linkify);

	var public_navigation = [ { href: "/", title: "Home" }, { href: "/quiz", title: "Quiz" }, { href: "/help", title: "Help" }, { href: "/login", title: "Login" }, { href: "/sum", title: "Sum - Dictionary" } ];
	
	view.RawPage = view.createClass({
		displayName: 'Page',
		render: function() {
			return h('html', [
				h('head', [
					h('meta', { charSet: 'utf8' }),
					h('meta', { name: 'viewport', content: 'width=device-width,user-scalable=yes' }),
					...icons,
					h('title', this.props.title),
					...(this.props.scripts||[]).map(scriptify),
					...(this.props.links||[]).map(linkify),
				]),
				h('body', {}, this.props.children)
			]);
		}
	});

	var rawscript = function(content) {
		return h('script', {dangerouslySetInnerHTML:{__html:content.replace(/\n\s*/g,' ')}});
	};

	view.Navigation = view.createClass({
		render: function() {
			var elements = [];

			for (var i in this.props.pages) {
				elements.push(
					h('li', [
						h('a',
							{ href: this.props.pages[i].href },
							this.props.pages[i].title
						)
					])
				);
			}

			return h("header", [
				h("nav", [
					h("ul", elements)
				])
			]);
		}
	})

	view.Page = view.createClass({
		render: function() {
			var page = view.pages[this.props.page];
			var props = Object.assign({}, this.props, {
				children: undefined,
				links: [
					'/CSS/react.css',
					'/CSS/jquery.qtip.min.css',
					'/Images/open-iconic/font/css/open-iconic.css',
				].map(href=>({href})),
				scripts: [
					'/JS/lib/jquery.js',
					'/JS/lib/unorm.js',
					'/JS/lib/i18next.js',
					'/JS/lib/jquery.autosize.input.min.js',
					'/JS/lib/jquery.qtip.min.js',
					'/JS/lib/select2.min.js',
					'https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.8.0/polyfill.min.js',
					'/node/build/browser.js',
				].map(src=>({src})),
			});
			if (page.title) props.title = page.title;
			var children = [
				view.Navigation.h({ pages: public_navigation }),
				h('h1#title', page.heading||page.title),
			];
			if (this.props.data) {
				var component = page.render(this.props.data);
				var content = {dangerouslySetInnerHTML:{__html:ReactDOMServer.renderToString(component)}};
				children.push(h('section#content', content));
				children.push(rawscript(`(function(view){
					var page = view.pages[${JSON.stringify(this.props.page)}], data = page.fromJSON(${JSON.stringify(page.toJSON(this.props.data))});
					ReactDOM.render(
						page.render(data),
						document.getElementById('content')
					);
				}(pantheum.view))`));
			} else {
				children.push(h('section#content'));
				children.push(rawscript(`(function(view){
					var page = view.pages[${JSON.stringify(this.props.page)}];
					Promise.resolve(page.data()).then(data => {
						ReactDOM.render(
							page.render(data),
							document.getElementById('content')
						)
					});
				}(pantheum.view))`));
			}
			return view.RawPage.h(props, children);
		}
	});
};
