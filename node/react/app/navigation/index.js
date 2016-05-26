var h = require('react-hyperscript');
var MaterialUI = require('material-ui')

var createClass = require('../../createClass');

module.exports = createClass({
	displayName: 'view.Navigation',
	getInitialState() {
		return { open: true };
	},
	handleChange(event, value) {
		var view = require('../../');
		if (value in view.pages) {
			console.log("Loading page "+value);
			var page = view.pages[value];
			window.history.pushState(value, page.title, '/'+value);
			window.document.title = page.title;
			Promise.resolve(page.data()).then(data => {
				ReactDOM.render(
					page.render(data),
					document.getElementById('content')
				)
			});
			event.preventDefault();
		}
	},
	render: function renderNavigation() {
		var elements = [];

		for (var page of this.props.pages) {
			var props = { href: page.href };
			if (page.image)
				props.leftIcon = page.image;
			if (page.value)
				props.value = page.value;
			if (page.event)
				props.onTouchTap = page.event;
			elements.push(
				h(MaterialUI.MenuItem, props, page.title)
			);
		}

		return h(MaterialUI.Drawer, {
			docked: true,
			open: true
		}, h(MaterialUI.Menu, {
			onChange: this.handleChange,
			value: this.props.value,
		}, elements));
	}
});
