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
	view.RawPage = view.createClass({
		displayName: 'Page',
		render: function() {
			return h('html', [
				h('head', [
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
