var h = require('react-hyperscript');

module.exports = function(view) {

	view.Abbreviation = view.createClass({
		displayName: 'view.Abbreviation',
		render: function() {
			return h('abbr', {title:this.props.title}, this.props.children);
		},
		componentDidMount: function() {
			view.$dom(this).qtip({
				style: {
					classes: "qtip-light qtip-abbr"
				},
				position: {
					at: "top center",
					my: "bottom center",
					adjust: {y:5},
				},
				show: {
					delay: 200,
				},
				hide: {
					fixed: true,
					delay: 100,
				}
			});
		},
		componentDidUpdate: function() {
			// Hint qtip to update
			view.$dom(this).attr('title', this.props.title);
		},
		componentWillUnmount: function() {
			view.$dom(this).qtip('destroy', true);
		}
	});
	view.format_abbr = function(desc, ...children) {
		return view.Abbreviation.h({title:desc}, children);
	};

};
