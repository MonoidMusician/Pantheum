var h = require('react-hyperscript');

var user = require('../user');

module.exports = function(view) {
	var {createClass, $dom, React, ReactDOM} = view;

	var format_abbr_del = function(abbr, desc, action) {
		return h('span', [view.format_abbr(desc, abbr), view.delete({action:action, key:1})]);
	};
	view.delete = function(props) {
		if (user.administrator)
			return view.Icon.h.small.delete(props);
	};
	view.Attribute = view.createClass({
		displayName: 'view.Attribute',
		delete_API() {
			var {tag, value, id, onDelete} = this.props;
			$.get(pantheum.api_path+'add-attributes.php',
				  'attr=!'+tag+'&id='+id)
			.done(function(data){
				if (data == "success") {
					successTip("Successfully deleted "+tag+" attribute");
					if (onDelete) onDelete(tag, value);
				} else errorTip("Could not delete "+tag+" attribute: "+data,6900);
			});
		},
		_render: function() {
			var {lang, tag, value} = this.props;
			if (lang && lang.attributes && lang.attributes.abbreviation) {
				let res = lang.attributes.abbreviation(tag, value);
				if (res)
					return format_abbr_del(...res, this.delete_API);
			}
			return (value !== null && value !== "true") ? tag+"="+value : tag;
		},
		render: function renderAttribute() {
			var r = this._render();
			if (typeof r === 'string')
				return h('span', [r, view.delete(this.delete_API)]);
			return r;
		}
	});
	view.create_attribute = function(props) {return function(a, key) {
		if (React.isValidElement(a)) return a;
		var [tag, value] = a.split("=");
		if (!value) return a;
		return view.Attribute.h(Object.assign(props, {key: tag, tag, value}));
	}};

	function intersperse(arr, sep) {
		if (arr.length === 0) {
			return [];
		}

		return arr.slice(1).reduce(function(xs, x, i) {
			return xs.concat([sep, x]);
		}, [arr[0]]);
	}

	view.Attributes = view.createClass({
		displayName: 'view.Attributes',
		handleNewValue(value) {
			console.log(value);
		},
		render: function renderAttributes() {
			var props = {
				word: this.props.word,
				id: this.props.word.id,
				lang: this.props.word.lang,
				onDelete: this.props.onAttrDelete,
			};
			var spart = view.EditableText.h({
				key:0, disabled: !user.administrator,
				value: this.props.word.spart,
				onNewValue: this.handleNewValue,
			});
			var attrs = [spart];
			if (!Array.isArray(this.props.word.attrs)) {
				for (let key in this.props.word.attrs) {
					attrs.push(key+'='+this.props.word.attrs[key]);
				}
			} else attrs.push(...this.props.word.attrs);
			if (user.administrator) attrs.push(view.Icon.h.add({key:attrs.length}));
			attrs = attrs.map(view.create_attribute(props));
			return h('span', ['(', ...intersperse(attrs, '; '), ')']);
		}
	});

};
