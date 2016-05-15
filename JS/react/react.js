var view = {};
module.exports = view;

var model = require('../model/model');
view.expand = require('./expand');
var stampit = require('stampit');
var React = require('react');
var h = require('react-hyperscript');
var la_ipa = null;

var pantheum = {
    user: {
        administrator: true,
    },
};

var createClass = function(c) {
    var r = React.createClass(c);
    r.h = h.bind(undefined, r);
    return r;
};
var propsdata = function(props, data) {
    for (let p in data) {
        props["data-"+p] = data[p];
    }
    return props;
};
var $dom = function(component) {
    return $(ReactDOM.findDOMNode(component));
};

	var autokey = (el, i) => el==null ? i : (typeof el === 'number' ? el : el.key || el.toString());

	// Create a React table from a list of rows, converting to appropriate React components
	view.create_table = function(data, options, props) {
		var {noheader} = options||{};
		var rows = data.map(
			(row, i) => React.isValidElement(row) ? row :
				h('tr', {key:i}, row.map(
					(el, k) => React.isValidElement(el) && (['td','th'].includes(el.type)) ? el :
						el !== undefined ? h(i || noheader ? 'td' : 'th', {key:autokey(el, k)}, el) : el
				))
		);
		return h('table', props||{}, [h('tbody', rows)]);
	};

	// Create a React table which merges values hierarchically
	view.create_table.merge_vertical = function(data, options, ...arg) {
		var {noheader} = options||{};
		var header = data.slice(0, +!noheader), rest = data.slice(+!noheader);
		var nrows = (i,start,v) => {
			if (!start) start = 0;
			if (!v) var v = rest[start][i];
			for (var j=start; j<rest.length; j++)
				if (rest[j][i] != v) break;
			return i ? Math.min(j-start, nrows(i-1,start)) : j-start;
		};
		var keep = (i,start) => {
			if (!start) return true;
			for (i; i>=0; i--)
				if (rest[start-1][i] != rest[start][i]) return true;
			return false;
		};
		data = header.concat(rest.map(
			(row, j) =>
				row.map((el, i) => {
					if (!keep(i, j)) return;
					return h('td', {key:autokey(el, i), rowSpan: nrows(i, j, el)}, el)
				})
		));
		return view.create_table(data, options, ...arg);
	};


view.Input = createClass({
    displayName: 'view.Input',
    componentDidMount: function() {
        var input = $dom(this);
        if (this.props.autoSize)
            input.autosizeInput();
        if (this.props.autoFocus)
            input[0].focus(); // actually redundant...
        if (this.props.autoSelect)
            input[0].select();
    },
    handleChange: function(event) {
        if (this.props.onChange)
            this.props.onChange(event);
        var {target: {value}} = event;
        if (this.props.onNewValue)
            this.props.onNewValue(value);
    },
    render: function() {
        return h('input', Object.assign({}, this.props, {onChange: this.handleChange}));
    }
});

/*
.squaredFour {
    width: 20px;
    margin: 20px auto;
    position: relative;
}

.squaredFour label {
    cursor: pointer;
    position: absolute;
    width: 20px;
    height: 20px;
    top: 0;
    border-radius: 4px;

    -webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    -moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    background: #fcfff4;

    background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
}

.squaredFour label:after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
    filter: alpha(opacity=0);
    opacity: 0;
    content: '';
    position: absolute;
    width: 9px;
    height: 5px;
    background: transparent;
    top: 4px;
    left: 4px;
    border: 3px solid #333;
    border-top: none;
    border-right: none;

    -webkit-transform: rotate(-45deg);
    -moz-transform: rotate(-45deg);
    -o-transform: rotate(-45deg);
    -ms-transform: rotate(-45deg);
    transform: rotate(-45deg);
}

.squaredFour label:hover::after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";
    filter: alpha(opacity=30);
    opacity: 0.5;
}

.squaredFour input[type=checkbox]:checked + label:after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
    filter: alpha(opacity=100);
    opacity: 1;
}
*/
view.Checkbox = createClass({
    displayName: 'view.Checkbox',
    getInitialState: function() {
        return {hover:false, focus:false};
    },
    enter: function() {
        this.setState({hover:true});
    },
    leave: function() {
        this.setState({hover:false});
    },
    focus: function() {
        this.setState({focus:true});
    },
    blur: function() {
        this.setState({focus:false});
    },
    handleKeyPress: function(event) {
        var {which:key} = event;
        if (key === 13 || key === 32) {
            if (this.props.onNewValue)
                this.props.onNewValue(!this.props.checked);
            return event.preventDefault();
        }
    },
    handleChange: function(event) {
        if (this.props.onChange)
            this.props.onChange(event);
        var {target: {checked}} = event;
        if (this.props.onNewValue)
            this.props.onNewValue(checked);
    },
    render: function() {
        var style = k => view.expand.style.make.call(this, view.Checkbox.style[k]);
        return h('label', {
            style: style('label'),
            onMouseEnter: this.enter,
            onMouseLeave: this.leave,
            onFocus: this.focus,
            onBlur: this.blur,
            onMouseUp: this.blur,
            onKeyPress: this.handleKeyPress,
            tabIndex: 0,
        }, [
            h('input', Object.assign({}, this.props, {onChange: this.handleChange, type:'checkbox', style: style('input')})),
            h('span', {style: style('box')}),
            h('span', {style: style('check')}),
            h('span', this.props.children)
        ]);
    },
});
view.Checkbox.style = {
    label: {
        width: 20,
        position: 'relative',
        outline: 0,
    },
    input: {
        visibility: 'hidden',
        marginLeft: '10px',
    },
    box: {
        cursor: 'pointer',
        position: 'absolute',
        top: 0,
        left: 0,
        width: 20,
        height: 20,
        borderRadius: 4,
        outline: 0,
        boxShadow: 'inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5)',
        background: 'linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%)',
    },
    check: {
        opacity: function() {
            return (this.props.checked ? 1 : (this.state.hover || this.state.focus ? 0.4 : 0));
        },
        border: function() {
            return '3px solid '+(this.state.focus ? '#C33' : '#333');
        },
        borderTop: 'none',
        borderRight: 'none',
        cursor: 'pointer',
        position: 'absolute',
        width: 9,
        height: 5,
        background: 'transparent',
        top: 4,
        left: 4,
        transform: 'rotate(-45deg)',
    }
}

view.EditableText = createClass({
    displayName: 'view.EditableText',
    getInitialState: function() {
        return {
            initial: this.props.value,
            value: this.props.value,
            editing: false,
            focused: false,
            wasfocused: false,
        };
    },
    toggle: function() {
        this.setState({editing: !this.state.editing});
    },
    edit: function() {
        this.setState({editing: true, wasfocused: this.state.focused||true});
    },
    set: function(value) {
        this.setState({value});
    },
    done: function() {
        if (!this.state.value) return;
        this.setState({editing: false, initial: this.state.value});
        this.props.onNewValue(this.state.value);
    },
    cancel: function() {
        this.setState({editing: false, value: this.state.initial});
    },
    focus: function() {
        this.setState({focused: true});
    },
    blur: function() {
        this.setState({focused: false});
    },
    handleKeyUp: function({which: key}) {
        if (key === 13 || key === 32) this.done();
        else if (key === 27) this.cancel();
    },
    componentDidUpdate: function(){
        //console.log('wasfocused:', this.state.wasfocused, this._edit && ReactDOM.findDOMNode(this._edit));
        if (this.wasfocused && this._edit)
            ReactDOM.findDOMNode(this._edit).focus();
    },
    render: function() {
        var text = this.props.display || this.state.value;
        this._edit = null;
        if (this.props.disabled) {
            var props = Object.assign({}, this.props);
            if (props.spanClassName) props.className = props.spanClassName;
            delete props.spanClassName;
            return h('span', props, text);
        }
        if (!this.state.editing) {
            var props = Object.assign({}, this.props);
            if (props.spanClassName) props.className = props.spanClassName;
            delete props.spanClassName;
            props.onClick = this.edit;
            return h('span', props, [
                text,
                view.Icon.h.small.edit({
                    ref: r => this._edit = r,
                })
            ]);
        } else {
            var props = Object.assign({}, this.props);
            if (props.inputClassName) props.className = props.inputClassName;
            delete props.inputClassName;
            props.autoFocus = props.autoSelect = props.autoSize = true;
            props.value = this.state.value;
            props.onBlur = this.cancel;
            props.onNewValue = this.set;
            props.onKeyUp = this.handleKeyUp;
            return view.Input.h(props);
        }
    }
});

view.Abbreviation = createClass({
    displayName: 'view.Abbreviation',
    render: function() {
        return h('abbr', {title:this.props.title}, this.props.children);
    },
    componentDidMount: function() {
        $dom(this).qtip({
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
        $dom(this).attr('title', this.props.title);
    },
    componentWillUnmount: function() {
        $dom(this).qtip('destroy', true);
    }
});
view.format_abbr = function(desc, ...children) {
    return view.Abbreviation.h({title:desc}, children);
};

view.Icon = createClass({
    displayName: 'view.Icon',
    getInitialState: function() {
        return {hover:false, focus:false, active:false};
    },
    handleKeyUp: function(event) {
        event.preventDefault();
    },
    handleKeyDown: function(event) {
        event.preventDefault();
    },
    handleMouseOver: function() {
        this.setState({hover:true});
    },
    handleMouseOut: function() {
        this.setState({hover:false});
    },
    handleMouseDown: function() {
        this.setState({active:'viamouse'});
    },
    handleMouseUp: function() {
        this.setState({active:false});
    },
    handleFocus: function() {
        this.setState({focus:true});
    },
    handleBlur: function() {
        this.setState({focus:false});
    },
    render: function() {
        var glyph = view.Icon.glyphs[this.props.type];
        var classes = this.props.className || [];
        if (typeof classes === 'string') classes = classes.split(" ");
        classes.push('oi', 'inline', 'spaced');
        var styles = [view.Icon.style.base];
        if (this.props.small) styles.push(view.Icon.style.small);
        if (this.props.nospace) styles.push({paddingLeft:null});
        var style = view.expand.make(...styles);
        if (this.state.active) style.color = 'red';
        else if (this.state.focus) style.color = '#CC3333';
        else if (this.state.hover) style.color = '#DA9031';
        else style.color = '#DA7B00';
        return h('a', propsdata({
            href: this.props.link||"javascript:void(0)",
            onClick: this.props.action||this.props.onClick,
            onKeyUp: this.handleKeyUp,
            onMouseOver: this.handleMouseOver,
            onMouseOut: this.handleMouseOut,
            onMouseDown: this.handleMouseDown,
            onMouseUp: this.handleMouseUp,
            onFocus: this.handleFocus,
            onBlur: this.handleBlur,
            className: classes.join(" "),
            title: this.props.desc,
            id: this.props.id,
            style: style,
            tabIndex: 0,
        }, {glyph}));
    },
    componentDidMount: function() {
        $dom(this).qtip({
            style: {
                classes: "qtip-light qtip-abbr"
            },
            position: {
                at: "top center",
                my: "bottom center",
                adjust: {y:0},
            },
            show: {
                delay: 800,
            },
            hide: {
                fixed: true,
                delay: 100,
            }
        });
    }
});
view.Icon.style = {
    base: {
        verticalAlign: 'sub',
        paddingLeft: '0.3em',
        color: '#DA7B00',
        textDecoration: 'none',
        outline: 'none',
        border: 'none',
    },
    small: {
        verticalAlign: 'inherit',
        fontSize: '60%',
        paddingLeft: '0.5em',
    },
};
view.Icon.glyphs = {
    "edit": "pencil",
    "refresh": "reload",
    "hardlink": "link-intact",
    "del": "trash",
    "tools": "wrench",
    "rename": "text",
    "change POS": "compass", // FIXME
    "&lt;&lt;": "media-skip-backward",
    "&lt;": "media-step-backward",
    "&gt;": "media-step-forward",
    "&gt;&gt;": "media-skip-forward",
    "visibility": "eye",
    "add": "plus",
};
view.Icon.h.small = function(props, children) {
    props = Object.assign({}, props||{}, {small:true});
    return view.Icon.h(props, children);
};
for (let fn of [view.Icon.h.small, view.Icon.h]) {
    for (let type in view.Icon.glyphs) {
        if (!/^[$_a-zA-Z][$_a-zA-Z0-9]*$/.test(type)) continue;
        fn[type] = function(props, children) {
            props = Object.assign({}, props||{}, {type});
            return fn(props, children);
        };
    }
}


view.format_value = function format_value(value) {
	return view.FormattedValue.h({value});
};
view.FormattedValue = createClass({
	displayName: 'view.FormattedValue',
	render: function renderFormattedValue() {
		var {value:v} = this.props;
		v = v.split('///')[0];
		var o = {
			'person-1': '1st person',
			'person-2': '2nd person',
			'person-3': '3rd person',
		};
		if (v in o) v = o[v];
		else v = v[0].toUpperCase()+v.substr(1).split('-').join(' ');
		return h('span', v);
	},
});
view.format_word = function format_word(value) {
	return view.FormattedWord.h({value});
};
view.FormattedWord = createClass({
	displayName: 'view.FormattedWord',
	render: function renderFormattedWord() {
		var {value:v} = this.props;
		//console.log('before:',v);
		if (typeof v === 'object' && 'value' in v) v = v.value;
		if (v) v = la_ipa ? la_ipa.transform(v) : v;
		else v = '\u2014';
		//console.log('after:',v);
		return h('span', v);
	},
});

	var format_abbr_del = function(abbr, desc, action) {
		return h('span', [view.format_abbr(desc, abbr), view.del({action:action, key:1})]);
	};
	view.del = function(props) {
		if (pantheum.user.administrator)
			return view.Icon.h.small.del(props);
	};
	view.Attribute = createClass({
		displayName: 'view.Attribute',
		delete_API: function delete_API() {
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
			var {tag, value} = this.props;
			var result;
			switch (tag) {
				case "transitive":
					switch (value) {
						case "true":  return format_abbr_del( "TR","Transitive",   this.delete_API);
						case "false": return format_abbr_del("NTR","Intransitive", this.delete_API);
					}; break;
				case "irregular":
					switch (value) {
						case "true":  return format_abbr_del( "REG","Regular",   this.delete_API);
						case "false": return format_abbr_del("NREG","Irregular", this.delete_API);
					}; break;
				case "common":
					switch (value) {
						case "true":  return format_abbr_del( "COM","Common",  this.delete_API);
						case "false": return format_abbr_del("NCOM","Uncommon", this.delete_API);
					}; break;
				case "person":
					result = {"person-1":"1st person","person-2":"2nd person","person-3":"3rd person"}; break;
				case "case":
					switch (value) {
						case "ablative":        return format_abbr_del("+ABL", "Uses the "+value, this.delete_API);
						case "accusative":      return format_abbr_del("+ACC", "Uses the "+value, this.delete_API);
						case "genitive":        return format_abbr_del("+GEN", "Uses the "+value, this.delete_API);
						case "dative":          return format_abbr_del("+DAT", "Uses the "+value, this.delete_API);
						case "dative-personal": return format_abbr_del("+DAT (of persons)", "Uses the dative for people", this.delete_API);
					}; break;
				case "declension":
					result = {
						"decl-1":"1st Declension",
						"decl-2":"2nd Declension",
						"decl-3":"3rd Declension",
						"decl-4":"4th Declension",
						"decl-5":"5th Declension",
						"decl-3-i":"3rd Declension i-stem",
						"decl-2-neuter":"2nd Declension Neuter",
						"decl-3-neuter":"3rd Declension Neuter",
						"decl-3-i-neuter":"3rd Declension Neuter i-stem",
						"decl-4-neuter":"4th Declension Neuter",
						"decl-2-4":"2nd/4th Declension",
						"adjective-12":"1st/2nd Declension",
						"adjective-3-3":"3rd Declension",
					 }; break;
				case "conjugation":
					result = {
						"conj-1":"1st Conjugation",
						"conj-2":"2nd Conjugation",
						"conj-3":"3rd Conjugation",
						"conj-3-io":"3rd Conjugation i-stem",
						"conj-4":"4th Conjugation",
						"conj-1-deponent":"1st Conjugation Deponent",
						"conj-2-deponent":"2nd Conjugation Deponent",
						"conj-3-deponent":"3rd Conjugation Deponent",
						"conj-3-io-deponent":"3rd Conjugation Deponent i-stem",
						"conj-4-deponent":"4th Conjugation Deponent",
					}; break;
				case "clc-stage":
					var sp = value.split("+");
					var CLC = format_abbr_del("CLC", "Cambridge Latin Course", this.delete_API);
					if (sp.length === 1)
						return h('span', ['Stage ',value,' (',CLC,')']);
					else if (sp.length === 2)
						return h('span', ['Stages ',sp[0],' and ',sp[1],' (',CLC,')']);
					var value = sp.slice(0,sp.length-1).join(", ") + ", and" + sp[sp.length-1];
					return h('span', ['Stage ',value,' (',CLC,')']);
			}
			if (Array.isArray(result) && result.length == 2)
				result = {"true":result[0],"false":result[1]};
			if (typeof result === "object")
				result = result[value];
			if (result) return result;
			var abbrs = {
				"copulative": "COP",
			};
			var Tag = tag.charAt(0).toUpperCase() + tag.substr(1);
			if (value === "true" && abbrs[tag]) return format_abbr_del(abbrs[tag], Tag, this.delete_API);
			return (value !== null && value !== "true") ? tag+"="+value : tag;
		},
		render: function renderAttribute() {
			var r = this._render();
			if (typeof r === 'string')
				return h('span', [r, view.del(this.delete_API)]);
			return r;
		}
	});
	view.create_attribute = function(props) {return function(a, key) {
		if (React.isValidElement(a)) return a;
		var [tag, value] = a.split("=");
		if (!value) return a;
		return view.Attribute.h(Object.assign({}, props, {key: tag, tag, value}));
	}};

	function intersperse(arr, sep) {
		if (arr.length === 0) {
			return [];
		}

		return arr.slice(1).reduce(function(xs, x, i) {
			return xs.concat([sep, x]);
		}, [arr[0]]);
	}

	view.Attributes = createClass({
		displayName: 'view.Attributes',
		handleNewValue(value) {
			console.log(value);
		},
		render: function renderAttributes() {
			var props = {
				word: this.props.word,
				id: this.props.word.id,
				onDelete: this.props.onAttrDelete,
			};
			var spart = view.EditableText.h({
				key:0, disabled: !pantheum.user.administrator,
				value: this.props.word.spart,
				onNewValue: this.handleNewValue,
			});
			var attrs = [spart];
			if (!Array.isArray(this.props.word.attrs)) {
                for (let key in this.props.word.attrs) {
					attrs.push(key+'='+this.props.word.attrs[key]);
				}
			} else attrs.push(...this.props.word.attrs);
			if (pantheum.user.administrator) attrs.push(view.Icon.h.add({key:attrs.length}));
			attrs = attrs.map(view.create_attribute(props));
			return h('span', ['(', ...intersperse(attrs, '; '), ')']);
		}
	});



var languages = {
    "la": "Latin",
    "en": "English",
};

view.Language = createClass({
    displayName: 'view.Language',
    render: function renderLanguage() {
        var title = this.props.name || languages[this.props.children];
        return h('sup', {title}, ["[",this.props.children,"]"]);
    },
    componentDidMount: function() {
        $(ReactDOM.findDOMNode(this)).qtip({
            style: {
                classes: "qtip-light qtip-abbr"
            },
            position: {
                at: "center left",
                my: "center right",
                adjust: {y:5},
            },
            show: {
                delay: 1000,
            },
            hide: {
                fixed: true,
                delay: 100,
            }
        });
    }
});
view.WordName = createClass({
    displayName: 'view.WordName',
    handleNewValue(name) {
        console.log(name);
    },
    render: function renderWordName() {
        var classes = ["word-name"];
        if (this.props.word.lang)
            classes.push("format-word-"+this.props.word.lang);
        return view.EditableText.h({
            disabled: !pantheum.user.administrator,
            spanClassName: classes.join(" "),
            onNewValue: this.handleNewValue,
            value: this.props.word.name,
            display: this.props.word.entry,
        });
    }
});
view.Definitions = createClass({
    displayName: 'view.Definitions',
    handleNewValue(id) {
        return (name) => {
            console.log(name);
        };
    },
    render: function renderDefinitions() {
        var edit;
        if (pantheum.user.administrator)
            edit = view.Icon.h({type:"del"});
        return h('ol', this.props.definitions.map((def, key) => {
            var tag = def.form_tag;
            return h('li', {key}, [
                view.Language.h(def.lang),
                tag && '(',
                tag && tag,
                tag && def.tag.value && ' “'+def.tag.value+'”',
                tag && ') ',
                view.EditableText.h({
                    disabled: !pantheum.user.administrator,
                    onNewValue: this.handleNewValue(def.id),
                    value: def.value.split('\n').join(', '),
                }),
                edit
            ]);
        }));
    }
});

view.Inflection = createClass({
    displayName: 'view.Inflection',
    getInitialState() {
        return {onlyleaves:false};
    },
    handleCheckbox(onlyleaves) {
        this.setState({onlyleaves});
    },
    render: function renderInflection() {
        var edit;
        if (pantheum.user.administrator)
            edit = view.Icon.h({type:"del"});
        var {onlyleaves} = this.state;
        var mgr = this.props.mgr;
        var sorted = model.Path.sort(this.props.forms);
        if (onlyleaves) sorted = sorted.filter((form, i) => i===sorted.length-1 || !sorted[i+1].issub(form));
        var rows = [
            mgr.all_sub_keys.concat(['value']),
            ...sorted.map(
                (form, key) => mgr.all_sub_keys.map(k=>form.key_value(k)).concat([
                    view.EditableText.h({
                        disabled: !pantheum.user.administrator,
                        onNewValue: this.handleNewValue,
                        value: form.value && form.value.split('\n').join(', '),
                        key: form.value,
                    })
                ])
            )
        ];
        return h('div', [
            view.Checkbox.h({
                checked: this.state.onlyleaves,
                onNewValue: this.handleCheckbox,
            }, 'Show only leaf nodes'),
            view.create_table.merge_vertical(rows, {}, {className:'inflection'}),
        ]);
    }
});
view.EntryName = createClass({
    displayName: 'view.EntryName',
    render: function renderEntryName() {
        return h('span', [view.Language.h(this.props.word.lang), view.WordName.h(this.props)]);
    }
});
view.Wiktionary = createClass({
    displayName: 'view.Wiktionary',
    render: function renderWiktionary() {
        // TODO: slugify (transform æ, œ, macrons....)
        return h('a', {
            href: "http://en.wiktionary.org/wiki/"+this.props.word.name+"#"+languages[this.props.word.lang],
            target: "_blank"
        }, this.props.text||"Wiktionary");
    }
});
view.LewisShort = createClass({
    displayName: 'view.LewisShort',
    render: function renderLewisShort() {
        if (this.props.word.lang != 'la') return h('span');
        // TODO: slugify (transform æ, œ, macrons....)
        return h('span', [' – ', h('a', {
            href: "http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry="+this.props.word.name,
            target: "_blank"
        }, this.props.word.text||"Lewis & Short")]);
    }
});
view.PronunciationTool = createClass({
    displayName: 'view.PronunciationTool',
    transform: la_ipa ? la_ipa.transforms["IPA transcription"] : function(v) {v},
    getInitialState() {
        return {value: ""};
    },
    handleChange({target: {value}}) {
        this.setState({value});
    },
    render: function renderPronunciationTool() {
        return h('span', [
            h('input',{onChange:this.handleChange}),
            h('span', this.transform(this.state.value))
        ]);
    }
});
view.Entry = createClass({
    displayName: 'view.Entry',
    getInitialState() {
        return {toolsOpen: false};
    },
    toggleTools() {
        this.setState({toolsOpen: !this.state.toolsOpen});
    },
    render: function renderEntry() {
        var tools, action = this.toggleTools;
        if (!this.state.toolsOpen) {
            tools = [view.Icon.h.tools({action})];
        } else {
            var k = 0;
            tools = [
                view.Icon.h.tools(   { key:k++, className: "hider", action }),
                view.Icon.h.hardlink({ key:k++, link: "dictionary.php?id="+this.props.id }),
                view.Icon.h.refresh( { key:k++ }),
                view.Icon.h.del(     { key:k++ }),
                h('div', {key:k++,style:{"paddingLeft":"2em"}}, [
                    view.Wiktionary.h(Object.assign({}, this.props, {key:0})),
                    view.LewisShort.h(Object.assign({}, this.props, {key:1})),
                    h('br', {key:2}),
                    'Pronunciation: ',
                    view.PronunciationTool.h({key:4})
                ])
            ];
        }
        return h('section', {id:this.id}, [
            view.EntryName.h(this.props),
            " ",
            view.Attributes.h(this.props),
            ...tools,
            view.Definitions.h({definitions:this.props.word.definitions}),
            view.Inflection.h({forms:this.props.word.forms, mgr:this.props.word.mgr}),
            h('hr'),
        ]);
    }
});
var word = {
    id: 10176,
    entry: "sum, esse, fui", // TODO: should be calculated from spart and forms (and attrs)
    attrs: {
        common:true,
        copulative:true,
        irregular:true,
        transitive:false
    },
};
view.word = word = model.Word(word, true);
word.onAttrDelete = function(tag, value) {
    delete word.attrs[tag];
    view.render();
};
view.render = function() {
    word.pullall().then(w=>ReactDOM.render(
        view.Entry.h({word}),
        document.getElementById('dictionary')
    ));
};
