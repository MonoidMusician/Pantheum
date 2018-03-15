(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.Yolk = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){

},{}],2:[function(require,module,exports){
/**
 * cuid.js
 * Collision-resistant UID generator for browsers and node.
 * Sequential for fast db lookups and recency sorting.
 * Safe for element IDs and server-side lookups.
 *
 * Extracted from CLCTR
 *
 * Copyright (c) Eric Elliott 2012
 * MIT License
 */

/*global window, navigator, document, require, process, module */
(function (app) {
  'use strict';
  var namespace = 'cuid',
    c = 0,
    blockSize = 4,
    base = 36,
    discreteValues = Math.pow(base, blockSize),

    pad = function pad(num, size) {
      var s = "000000000" + num;
      return s.substr(s.length-size);
    },

    randomBlock = function randomBlock() {
      return pad((Math.random() *
            discreteValues << 0)
            .toString(base), blockSize);
    },

    safeCounter = function () {
      c = (c < discreteValues) ? c : 0;
      c++; // this is not subliminal
      return c - 1;
    },

    api = function cuid() {
      // Starting with a lowercase letter makes
      // it HTML element ID friendly.
      var letter = 'c', // hard-coded allows for sequential access

        // timestamp
        // warning: this exposes the exact date and time
        // that the uid was created.
        timestamp = (new Date().getTime()).toString(base),

        // Prevent same-machine collisions.
        counter,

        // A few chars to generate distinct ids for different
        // clients (so different computers are far less
        // likely to generate the same id)
        fingerprint = api.fingerprint(),

        // Grab some more chars from Math.random()
        random = randomBlock() + randomBlock();

        counter = pad(safeCounter().toString(base), blockSize);

      return  (letter + timestamp + counter + fingerprint + random);
    };

  api.slug = function slug() {
    var date = new Date().getTime().toString(36),
      counter,
      print = api.fingerprint().slice(0,1) +
        api.fingerprint().slice(-1),
      random = randomBlock().slice(-2);

      counter = safeCounter().toString(36).slice(-4);

    return date.slice(-2) +
      counter + print + random;
  };

  api.globalCount = function globalCount() {
    // We want to cache the results of this
    var cache = (function calc() {
        var i,
          count = 0;

        for (i in window) {
          count++;
        }

        return count;
      }());

    api.globalCount = function () { return cache; };
    return cache;
  };

  api.fingerprint = function browserPrint() {
    return pad((navigator.mimeTypes.length +
      navigator.userAgent.length).toString(36) +
      api.globalCount().toString(36), 4);
  };

  // don't change anything from here down.
  if (app.register) {
    app.register(namespace, api);
  } else if (typeof module !== 'undefined') {
    module.exports = api;
  } else {
    app[namespace] = api;
  }

}(this.applitude || this));

},{}],3:[function(require,module,exports){
/*! (C) WebReflection Mit Style License */
(function(e,t,n,r){"use strict";function rt(e,t){for(var n=0,r=e.length;n<r;n++)dt(e[n],t)}function it(e){for(var t=0,n=e.length,r;t<n;t++)r=e[t],nt(r,b[ot(r)])}function st(e){return function(t){j(t)&&(dt(t,e),rt(t.querySelectorAll(w),e))}}function ot(e){var t=e.getAttribute("is"),n=e.nodeName.toUpperCase(),r=S.call(y,t?v+t.toUpperCase():d+n);return t&&-1<r&&!ut(n,t)?-1:r}function ut(e,t){return-1<w.indexOf(e+'[is="'+t+'"]')}function at(e){var t=e.currentTarget,n=e.attrChange,r=e.attrName,i=e.target;Q&&(!i||i===t)&&t.attributeChangedCallback&&r!=="style"&&t.attributeChangedCallback(r,n===e[a]?null:e.prevValue,n===e[l]?null:e.newValue)}function ft(e){var t=st(e);return function(e){X.push(t,e.target)}}function lt(e){K&&(K=!1,e.currentTarget.removeEventListener(h,lt)),rt((e.target||t).querySelectorAll(w),e.detail===o?o:s),B&&pt()}function ct(e,t){var n=this;q.call(n,e,t),G.call(n,{target:n})}function ht(e,t){D(e,t),et?et.observe(e,z):(J&&(e.setAttribute=ct,e[i]=Z(e),e.addEventListener(p,G)),e.addEventListener(c,at)),e.createdCallback&&Q&&(e.created=!0,e.createdCallback(),e.created=!1)}function pt(){for(var e,t=0,n=F.length;t<n;t++)e=F[t],E.contains(e)||(F.splice(t,1),dt(e,o))}function dt(e,t){var n,r=ot(e);-1<r&&(tt(e,b[r]),r=0,t===s&&!e[s]?(e[o]=!1,e[s]=!0,r=1,B&&S.call(F,e)<0&&F.push(e)):t===o&&!e[o]&&(e[s]=!1,e[o]=!0,r=1),r&&(n=e[t+"Callback"])&&n.call(e))}if(r in t)return;var i="__"+r+(Math.random()*1e5>>0),s="attached",o="detached",u="extends",a="ADDITION",f="MODIFICATION",l="REMOVAL",c="DOMAttrModified",h="DOMContentLoaded",p="DOMSubtreeModified",d="<",v="=",m=/^[A-Z][A-Z0-9]*(?:-[A-Z0-9]+)+$/,g=["ANNOTATION-XML","COLOR-PROFILE","FONT-FACE","FONT-FACE-SRC","FONT-FACE-URI","FONT-FACE-FORMAT","FONT-FACE-NAME","MISSING-GLYPH"],y=[],b=[],w="",E=t.documentElement,S=y.indexOf||function(e){for(var t=this.length;t--&&this[t]!==e;);return t},x=n.prototype,T=x.hasOwnProperty,N=x.isPrototypeOf,C=n.defineProperty,k=n.getOwnPropertyDescriptor,L=n.getOwnPropertyNames,A=n.getPrototypeOf,O=n.setPrototypeOf,M=!!n.__proto__,_=n.create||function vt(e){return e?(vt.prototype=e,new vt):this},D=O||(M?function(e,t){return e.__proto__=t,e}:L&&k?function(){function e(e,t){for(var n,r=L(t),i=0,s=r.length;i<s;i++)n=r[i],T.call(e,n)||C(e,n,k(t,n))}return function(t,n){do e(t,n);while((n=A(n))&&!N.call(n,t));return t}}():function(e,t){for(var n in t)e[n]=t[n];return e}),P=e.MutationObserver||e.WebKitMutationObserver,H=(e.HTMLElement||e.Element||e.Node).prototype,B=!N.call(H,E),j=B?function(e){return e.nodeType===1}:function(e){return N.call(H,e)},F=B&&[],I=H.cloneNode,q=H.setAttribute,R=H.removeAttribute,U=t.createElement,z=P&&{attributes:!0,characterData:!0,attributeOldValue:!0},W=P||function(e){J=!1,E.removeEventListener(c,W)},X,V=e.requestAnimationFrame||e.webkitRequestAnimationFrame||e.mozRequestAnimationFrame||e.msRequestAnimationFrame||function(e){setTimeout(e,10)},$=!1,J=!0,K=!0,Q=!0,G,Y,Z,et,tt,nt;O||M?(tt=function(e,t){N.call(t,e)||ht(e,t)},nt=ht):(tt=function(e,t){e[i]||(e[i]=n(!0),ht(e,t))},nt=tt),B?(J=!1,function(){var e=k(H,"addEventListener"),t=e.value,n=function(e){var t=new CustomEvent(c,{bubbles:!0});t.attrName=e,t.prevValue=this.getAttribute(e),t.newValue=null,t[l]=t.attrChange=2,R.call(this,e),this.dispatchEvent(t)},r=function(e,t){var n=this.hasAttribute(e),r=n&&this.getAttribute(e),i=new CustomEvent(c,{bubbles:!0});q.call(this,e,t),i.attrName=e,i.prevValue=n?r:null,i.newValue=t,n?i[f]=i.attrChange=1:i[a]=i.attrChange=0,this.dispatchEvent(i)},s=function(e){var t=e.currentTarget,n=t[i],r=e.propertyName,s;n.hasOwnProperty(r)&&(n=n[r],s=new CustomEvent(c,{bubbles:!0}),s.attrName=n.name,s.prevValue=n.value||null,s.newValue=n.value=t[r]||null,s.prevValue==null?s[a]=s.attrChange=0:s[f]=s.attrChange=1,t.dispatchEvent(s))};e.value=function(e,o,u){e===c&&this.attributeChangedCallback&&this.setAttribute!==r&&(this[i]={className:{name:"class",value:this.className}},this.setAttribute=r,this.removeAttribute=n,t.call(this,"propertychange",s)),t.call(this,e,o,u)},C(H,"addEventListener",e)}()):P||(E.addEventListener(c,W),E.setAttribute(i,1),E.removeAttribute(i),J&&(G=function(e){var t=this,n,r,s;if(t===e.target){n=t[i],t[i]=r=Z(t);for(s in r){if(!(s in n))return Y(0,t,s,n[s],r[s],a);if(r[s]!==n[s])return Y(1,t,s,n[s],r[s],f)}for(s in n)if(!(s in r))return Y(2,t,s,n[s],r[s],l)}},Y=function(e,t,n,r,i,s){var o={attrChange:e,currentTarget:t,attrName:n,prevValue:r,newValue:i};o[s]=e,at(o)},Z=function(e){for(var t,n,r={},i=e.attributes,s=0,o=i.length;s<o;s++)t=i[s],n=t.name,n!=="setAttribute"&&(r[n]=t.value);return r})),t[r]=function(n,r){p=n.toUpperCase(),$||($=!0,P?(et=function(e,t){function n(e,t){for(var n=0,r=e.length;n<r;t(e[n++]));}return new P(function(r){for(var i,s,o=0,u=r.length;o<u;o++)i=r[o],i.type==="childList"?(n(i.addedNodes,e),n(i.removedNodes,t)):(s=i.target,Q&&s.attributeChangedCallback&&i.attributeName!=="style"&&s.attributeChangedCallback(i.attributeName,i.oldValue,s.getAttribute(i.attributeName)))})}(st(s),st(o)),et.observe(t,{childList:!0,subtree:!0})):(X=[],V(function E(){while(X.length)X.shift().call(null,X.shift());V(E)}),t.addEventListener("DOMNodeInserted",ft(s)),t.addEventListener("DOMNodeRemoved",ft(o))),t.addEventListener(h,lt),t.addEventListener("readystatechange",lt),t.createElement=function(e,n){var r=U.apply(t,arguments),i=""+e,s=S.call(y,(n?v:d)+(n||i).toUpperCase()),o=-1<s;return n&&(r.setAttribute("is",n=n.toLowerCase()),o&&(o=ut(i.toUpperCase(),n))),Q=!t.createElement.innerHTMLHelper,o&&nt(r,b[s]),r},H.cloneNode=function(e){var t=I.call(this,!!e),n=ot(t);return-1<n&&nt(t,b[n]),e&&it(t.querySelectorAll(w)),t});if(-2<S.call(y,v+p)+S.call(y,d+p))throw new Error("A "+n+" type is already registered");if(!m.test(p)||-1<S.call(g,p))throw new Error("The type "+n+" is invalid");var i=function(){return f?t.createElement(l,p):t.createElement(l)},a=r||x,f=T.call(a,u),l=f?r[u].toUpperCase():p,c=y.push((f?v:d)+p)-1,p;return w=w.concat(w.length?",":"",f?l+'[is="'+n.toLowerCase()+'"]':l),i.prototype=b[c]=T.call(a,"prototype")?a.prototype:_(H),rt(t.querySelectorAll(w),s),i}})(window,document,Object,"registerElement");
},{}],4:[function(require,module,exports){
var EvStore = require("ev-store")

module.exports = addEvent

function addEvent(target, type, handler) {
    var events = EvStore(target)
    var event = events[type]

    if (!event) {
        events[type] = handler
    } else if (Array.isArray(event)) {
        if (event.indexOf(handler) === -1) {
            event.push(handler)
        }
    } else if (event !== handler) {
        events[type] = [event, handler]
    }
}

},{"ev-store":9}],5:[function(require,module,exports){
var globalDocument = require("global/document")
var EvStore = require("ev-store")
var createStore = require("weakmap-shim/create-store")

var addEvent = require("./add-event.js")
var removeEvent = require("./remove-event.js")
var ProxyEvent = require("./proxy-event.js")

var HANDLER_STORE = createStore()

module.exports = DOMDelegator

function DOMDelegator(document) {
    if (!(this instanceof DOMDelegator)) {
        return new DOMDelegator(document);
    }

    document = document || globalDocument

    this.target = document.documentElement
    this.events = {}
    this.rawEventListeners = {}
    this.globalListeners = {}
}

DOMDelegator.prototype.addEventListener = addEvent
DOMDelegator.prototype.removeEventListener = removeEvent

DOMDelegator.allocateHandle =
    function allocateHandle(func) {
        var handle = new Handle()

        HANDLER_STORE(handle).func = func;

        return handle
    }

DOMDelegator.transformHandle =
    function transformHandle(handle, broadcast) {
        var func = HANDLER_STORE(handle).func

        return this.allocateHandle(function (ev) {
            broadcast(ev, func);
        })
    }

DOMDelegator.prototype.addGlobalEventListener =
    function addGlobalEventListener(eventName, fn) {
        var listeners = this.globalListeners[eventName] || [];
        if (listeners.indexOf(fn) === -1) {
            listeners.push(fn)
        }

        this.globalListeners[eventName] = listeners;
    }

DOMDelegator.prototype.removeGlobalEventListener =
    function removeGlobalEventListener(eventName, fn) {
        var listeners = this.globalListeners[eventName] || [];

        var index = listeners.indexOf(fn)
        if (index !== -1) {
            listeners.splice(index, 1)
        }
    }

DOMDelegator.prototype.listenTo = function listenTo(eventName) {
    if (!(eventName in this.events)) {
        this.events[eventName] = 0;
    }

    this.events[eventName]++;

    if (this.events[eventName] !== 1) {
        return
    }

    var listener = this.rawEventListeners[eventName]
    if (!listener) {
        listener = this.rawEventListeners[eventName] =
            createHandler(eventName, this)
    }

    this.target.addEventListener(eventName, listener, true)
}

DOMDelegator.prototype.unlistenTo = function unlistenTo(eventName) {
    if (!(eventName in this.events)) {
        this.events[eventName] = 0;
    }

    if (this.events[eventName] === 0) {
        throw new Error("already unlistened to event.");
    }

    this.events[eventName]--;

    if (this.events[eventName] !== 0) {
        return
    }

    var listener = this.rawEventListeners[eventName]

    if (!listener) {
        throw new Error("dom-delegator#unlistenTo: cannot " +
            "unlisten to " + eventName)
    }

    this.target.removeEventListener(eventName, listener, true)
}

function createHandler(eventName, delegator) {
    var globalListeners = delegator.globalListeners;
    var delegatorTarget = delegator.target;

    return handler

    function handler(ev) {
        var globalHandlers = globalListeners[eventName] || []

        if (globalHandlers.length > 0) {
            var globalEvent = new ProxyEvent(ev);
            globalEvent.currentTarget = delegatorTarget;
            callListeners(globalHandlers, globalEvent)
        }

        findAndInvokeListeners(ev.target, ev, eventName)
    }
}

function findAndInvokeListeners(elem, ev, eventName) {
    var listener = getListener(elem, eventName)

    if (listener && listener.handlers.length > 0) {
        var listenerEvent = new ProxyEvent(ev);
        listenerEvent.currentTarget = listener.currentTarget
        callListeners(listener.handlers, listenerEvent)

        if (listenerEvent._bubbles) {
            var nextTarget = listener.currentTarget.parentNode
            findAndInvokeListeners(nextTarget, ev, eventName)
        }
    }
}

function getListener(target, type) {
    // terminate recursion if parent is `null`
    if (target === null || typeof target === "undefined") {
        return null
    }

    var events = EvStore(target)
    // fetch list of handler fns for this event
    var handler = events[type]
    var allHandler = events.event

    if (!handler && !allHandler) {
        return getListener(target.parentNode, type)
    }

    var handlers = [].concat(handler || [], allHandler || [])
    return new Listener(target, handlers)
}

function callListeners(handlers, ev) {
    handlers.forEach(function (handler) {
        if (typeof handler === "function") {
            handler(ev)
        } else if (typeof handler.handleEvent === "function") {
            handler.handleEvent(ev)
        } else if (handler.type === "dom-delegator-handle") {
            HANDLER_STORE(handler).func(ev)
        } else {
            throw new Error("dom-delegator: unknown handler " +
                "found: " + JSON.stringify(handlers));
        }
    })
}

function Listener(target, handlers) {
    this.currentTarget = target
    this.handlers = handlers
}

function Handle() {
    this.type = "dom-delegator-handle"
}

},{"./add-event.js":4,"./proxy-event.js":7,"./remove-event.js":8,"ev-store":9,"global/document":12,"weakmap-shim/create-store":29}],6:[function(require,module,exports){
var Individual = require("individual")
var cuid = require("cuid")
var globalDocument = require("global/document")

var DOMDelegator = require("./dom-delegator.js")

var versionKey = "13"
var cacheKey = "__DOM_DELEGATOR_CACHE@" + versionKey
var cacheTokenKey = "__DOM_DELEGATOR_CACHE_TOKEN@" + versionKey
var delegatorCache = Individual(cacheKey, {
    delegators: {}
})
var commonEvents = [
    "blur", "change", "click",  "contextmenu", "dblclick",
    "error","focus", "focusin", "focusout", "input", "keydown",
    "keypress", "keyup", "load", "mousedown", "mouseup",
    "resize", "select", "submit", "touchcancel",
    "touchend", "touchstart", "unload"
]

/*  Delegator is a thin wrapper around a singleton `DOMDelegator`
        instance.

    Only one DOMDelegator should exist because we do not want
        duplicate event listeners bound to the DOM.

    `Delegator` will also `listenTo()` all events unless
        every caller opts out of it
*/
module.exports = Delegator

function Delegator(opts) {
    opts = opts || {}
    var document = opts.document || globalDocument

    var cacheKey = document[cacheTokenKey]

    if (!cacheKey) {
        cacheKey =
            document[cacheTokenKey] = cuid()
    }

    var delegator = delegatorCache.delegators[cacheKey]

    if (!delegator) {
        delegator = delegatorCache.delegators[cacheKey] =
            new DOMDelegator(document)
    }

    if (opts.defaultEvents !== false) {
        for (var i = 0; i < commonEvents.length; i++) {
            delegator.listenTo(commonEvents[i])
        }
    }

    return delegator
}

Delegator.allocateHandle = DOMDelegator.allocateHandle;
Delegator.transformHandle = DOMDelegator.transformHandle;

},{"./dom-delegator.js":5,"cuid":2,"global/document":12,"individual":13}],7:[function(require,module,exports){
var inherits = require("inherits")

var ALL_PROPS = [
    "altKey", "bubbles", "cancelable", "ctrlKey",
    "eventPhase", "metaKey", "relatedTarget", "shiftKey",
    "target", "timeStamp", "type", "view", "which"
]
var KEY_PROPS = ["char", "charCode", "key", "keyCode"]
var MOUSE_PROPS = [
    "button", "buttons", "clientX", "clientY", "layerX",
    "layerY", "offsetX", "offsetY", "pageX", "pageY",
    "screenX", "screenY", "toElement"
]

var rkeyEvent = /^key|input/
var rmouseEvent = /^(?:mouse|pointer|contextmenu)|click/

module.exports = ProxyEvent

function ProxyEvent(ev) {
    if (!(this instanceof ProxyEvent)) {
        return new ProxyEvent(ev)
    }

    if (rkeyEvent.test(ev.type)) {
        return new KeyEvent(ev)
    } else if (rmouseEvent.test(ev.type)) {
        return new MouseEvent(ev)
    }

    for (var i = 0; i < ALL_PROPS.length; i++) {
        var propKey = ALL_PROPS[i]
        this[propKey] = ev[propKey]
    }

    this._rawEvent = ev
    this._bubbles = false;
}

ProxyEvent.prototype.preventDefault = function () {
    this._rawEvent.preventDefault()
}

ProxyEvent.prototype.startPropagation = function () {
    this._bubbles = true;
}

function MouseEvent(ev) {
    for (var i = 0; i < ALL_PROPS.length; i++) {
        var propKey = ALL_PROPS[i]
        this[propKey] = ev[propKey]
    }

    for (var j = 0; j < MOUSE_PROPS.length; j++) {
        var mousePropKey = MOUSE_PROPS[j]
        this[mousePropKey] = ev[mousePropKey]
    }

    this._rawEvent = ev
}

inherits(MouseEvent, ProxyEvent)

function KeyEvent(ev) {
    for (var i = 0; i < ALL_PROPS.length; i++) {
        var propKey = ALL_PROPS[i]
        this[propKey] = ev[propKey]
    }

    for (var j = 0; j < KEY_PROPS.length; j++) {
        var keyPropKey = KEY_PROPS[j]
        this[keyPropKey] = ev[keyPropKey]
    }

    this._rawEvent = ev
}

inherits(KeyEvent, ProxyEvent)

},{"inherits":14}],8:[function(require,module,exports){
var EvStore = require("ev-store")

module.exports = removeEvent

function removeEvent(target, type, handler) {
    var events = EvStore(target)
    var event = events[type]

    if (!event) {
        return
    } else if (Array.isArray(event)) {
        var index = event.indexOf(handler)
        if (index !== -1) {
            event.splice(index, 1)
        }
    } else if (event === handler) {
        events[type] = null
    }
}

},{"ev-store":9}],9:[function(require,module,exports){
'use strict';

var OneVersionConstraint = require('individual/one-version');

var MY_VERSION = '7';
OneVersionConstraint('ev-store', MY_VERSION);

var hashKey = '__EV_STORE_KEY@' + MY_VERSION;

module.exports = EvStore;

function EvStore(elem) {
    var hash = elem[hashKey];

    if (!hash) {
        hash = elem[hashKey] = {};
    }

    return hash;
}

},{"individual/one-version":11}],10:[function(require,module,exports){
(function (global){
'use strict';

/*global window, global*/

var root = typeof window !== 'undefined' ?
    window : typeof global !== 'undefined' ?
    global : {};

module.exports = Individual;

function Individual(key, value) {
    if (key in root) {
        return root[key];
    }

    root[key] = value;

    return value;
}

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],11:[function(require,module,exports){
'use strict';

var Individual = require('./index.js');

module.exports = OneVersion;

function OneVersion(moduleName, version, defaultValue) {
    var key = '__INDIVIDUAL_ONE_VERSION_' + moduleName;
    var enforceKey = key + '_ENFORCE_SINGLETON';

    var versionValue = Individual(enforceKey, version);

    if (versionValue !== version) {
        throw new Error('Can only have one copy of ' +
            moduleName + '.\n' +
            'You already have version ' + versionValue +
            ' installed.\n' +
            'This means you cannot install version ' + version);
    }

    return Individual(key, defaultValue);
}

},{"./index.js":10}],12:[function(require,module,exports){
(function (global){
var topLevel = typeof global !== 'undefined' ? global :
    typeof window !== 'undefined' ? window : {}
var minDoc = require('min-document');

if (typeof document !== 'undefined') {
    module.exports = document;
} else {
    var doccy = topLevel['__GLOBAL_DOCUMENT_CACHE@4'];

    if (!doccy) {
        doccy = topLevel['__GLOBAL_DOCUMENT_CACHE@4'] = minDoc;
    }

    module.exports = doccy;
}

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"min-document":1}],13:[function(require,module,exports){
(function (global){
var root = typeof window !== 'undefined' ?
    window : typeof global !== 'undefined' ?
    global : {};

module.exports = Individual

function Individual(key, value) {
    if (root[key]) {
        return root[key]
    }

    Object.defineProperty(root, key, {
        value: value
        , configurable: true
    })

    return value
}

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],14:[function(require,module,exports){
if (typeof Object.create === 'function') {
  // implementation from standard node.js 'util' module
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    ctor.prototype = Object.create(superCtor.prototype, {
      constructor: {
        value: ctor,
        enumerable: false,
        writable: true,
        configurable: true
      }
    });
  };
} else {
  // old school shim for old browsers
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    var TempCtor = function () {}
    TempCtor.prototype = superCtor.prototype
    ctor.prototype = new TempCtor()
    ctor.prototype.constructor = ctor
  }
}

},{}],15:[function(require,module,exports){
"use strict";

module.exports = function isObject(x) {
	return typeof x === "object" && x !== null;
};

},{}],16:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * The base implementation of `baseForIn` and `baseForOwn` which iterates
 * over `object` properties returned by `keysFunc` invoking `iteratee` for
 * each property. Iteratee functions may exit iteration early by explicitly
 * returning `false`.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @param {Function} keysFunc The function to get the keys of `object`.
 * @returns {Object} Returns `object`.
 */
var baseFor = createBaseFor();

/**
 * Creates a base function for `_.forIn` or `_.forInRight`.
 *
 * @private
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */
function createBaseFor(fromRight) {
  return function(object, iteratee, keysFunc) {
    var iterable = toObject(object),
        props = keysFunc(object),
        length = props.length,
        index = fromRight ? length : -1;

    while ((fromRight ? index-- : ++index < length)) {
      var key = props[index];
      if (iteratee(iterable[key], key, iterable) === false) {
        break;
      }
    }
    return object;
  };
}

/**
 * Converts `value` to an object if it's not one.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {Object} Returns the object.
 */
function toObject(value) {
  return isObject(value) ? value : Object(value);
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

module.exports = baseFor;

},{}],17:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Converts `value` to a string if it's not one. An empty string is returned
 * for `null` or `undefined` values.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  return value == null ? '' : (value + '');
}

module.exports = baseToString;

},{}],18:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var deburr = require('lodash.deburr'),
    words = require('lodash.words');

/**
 * Creates a function that produces compound words out of the words in a
 * given string.
 *
 * @private
 * @param {Function} callback The function to combine each word.
 * @returns {Function} Returns the new compounder function.
 */
function createCompounder(callback) {
  return function(string) {
    var index = -1,
        array = words(deburr(string)),
        length = array.length,
        result = '';

    while (++index < length) {
      result = callback(result, array[index], index);
    }
    return result;
  };
}

module.exports = createCompounder;

},{"lodash.deburr":21,"lodash.words":27}],19:[function(require,module,exports){
/**
 * lodash 3.0.9 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** Used to detect unsigned integer values. */
var reIsUint = /^\d+$/;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  value = (typeof value == 'number' || reIsUint.test(value)) ? +value : -1;
  length = length == null ? MAX_SAFE_INTEGER : length;
  return value > -1 && value % 1 == 0 && value < length;
}

/**
 * Checks if the provided arguments are from an iteratee call.
 *
 * @private
 * @param {*} value The potential iteratee value argument.
 * @param {*} index The potential iteratee index or key argument.
 * @param {*} object The potential iteratee object argument.
 * @returns {boolean} Returns `true` if the arguments are from an iteratee call, else `false`.
 */
function isIterateeCall(value, index, object) {
  if (!isObject(object)) {
    return false;
  }
  var type = typeof index;
  if (type == 'number'
      ? (isArrayLike(object) && isIndex(index, object.length))
      : (type == 'string' && index in object)) {
    var other = object[index];
    return value === value ? (value === other) : (other !== other);
  }
  return false;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

module.exports = isIterateeCall;

},{}],20:[function(require,module,exports){
/**
 * lodash 3.0.1 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var createCompounder = require('lodash._createcompounder');

/**
 * Converts `string` to camel case.
 * See [Wikipedia](https://en.wikipedia.org/wiki/CamelCase) for more details.
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to convert.
 * @returns {string} Returns the camel cased string.
 * @example
 *
 * _.camelCase('Foo Bar');
 * // => 'fooBar'
 *
 * _.camelCase('--foo-bar');
 * // => 'fooBar'
 *
 * _.camelCase('__foo_bar__');
 * // => 'fooBar'
 */
var camelCase = createCompounder(function(result, word, index) {
  word = word.toLowerCase();
  return result + (index ? (word.charAt(0).toUpperCase() + word.slice(1)) : word);
});

module.exports = camelCase;

},{"lodash._createcompounder":18}],21:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseToString = require('lodash._basetostring');

/** Used to match [combining diacritical marks](https://en.wikipedia.org/wiki/Combining_Diacritical_Marks). */
var reComboMark = /[\u0300-\u036f\ufe20-\ufe23]/g;

/** Used to match latin-1 supplementary letters (excluding mathematical operators). */
var reLatin1 = /[\xc0-\xd6\xd8-\xde\xdf-\xf6\xf8-\xff]/g;

/** Used to map latin-1 supplementary letters to basic latin letters. */
var deburredLetters = {
  '\xc0': 'A',  '\xc1': 'A', '\xc2': 'A', '\xc3': 'A', '\xc4': 'A', '\xc5': 'A',
  '\xe0': 'a',  '\xe1': 'a', '\xe2': 'a', '\xe3': 'a', '\xe4': 'a', '\xe5': 'a',
  '\xc7': 'C',  '\xe7': 'c',
  '\xd0': 'D',  '\xf0': 'd',
  '\xc8': 'E',  '\xc9': 'E', '\xca': 'E', '\xcb': 'E',
  '\xe8': 'e',  '\xe9': 'e', '\xea': 'e', '\xeb': 'e',
  '\xcC': 'I',  '\xcd': 'I', '\xce': 'I', '\xcf': 'I',
  '\xeC': 'i',  '\xed': 'i', '\xee': 'i', '\xef': 'i',
  '\xd1': 'N',  '\xf1': 'n',
  '\xd2': 'O',  '\xd3': 'O', '\xd4': 'O', '\xd5': 'O', '\xd6': 'O', '\xd8': 'O',
  '\xf2': 'o',  '\xf3': 'o', '\xf4': 'o', '\xf5': 'o', '\xf6': 'o', '\xf8': 'o',
  '\xd9': 'U',  '\xda': 'U', '\xdb': 'U', '\xdc': 'U',
  '\xf9': 'u',  '\xfa': 'u', '\xfb': 'u', '\xfc': 'u',
  '\xdd': 'Y',  '\xfd': 'y', '\xff': 'y',
  '\xc6': 'Ae', '\xe6': 'ae',
  '\xde': 'Th', '\xfe': 'th',
  '\xdf': 'ss'
};

/**
 * Used by `_.deburr` to convert latin-1 supplementary letters to basic latin letters.
 *
 * @private
 * @param {string} letter The matched letter to deburr.
 * @returns {string} Returns the deburred letter.
 */
function deburrLetter(letter) {
  return deburredLetters[letter];
}

/**
 * Deburrs `string` by converting [latin-1 supplementary letters](https://en.wikipedia.org/wiki/Latin-1_Supplement_(Unicode_block)#Character_table)
 * to basic latin letters and removing [combining diacritical marks](https://en.wikipedia.org/wiki/Combining_Diacritical_Marks).
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to deburr.
 * @returns {string} Returns the deburred string.
 * @example
 *
 * _.deburr('déjà vu');
 * // => 'deja vu'
 */
function deburr(string) {
  string = baseToString(string);
  return string && string.replace(reLatin1, deburrLetter).replace(reComboMark, '');
}

module.exports = deburr;

},{"lodash._basetostring":17}],22:[function(require,module,exports){
/**
 * lodash 3.0.4 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/** Native method references. */
var propertyIsEnumerable = objectProto.propertyIsEnumerable;

/**
 * Used as the [maximum length](http://ecma-international.org/ecma-262/6.0/#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

/**
 * Gets the "length" property value of `object`.
 *
 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
 * that affects Safari on at least iOS 8.1-8.3 ARM64.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {*} Returns the "length" value.
 */
var getLength = baseProperty('length');

/**
 * Checks if `value` is array-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 */
function isArrayLike(value) {
  return value != null && isLength(getLength(value));
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](http://ecma-international.org/ecma-262/6.0/#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is classified as an `arguments` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isArguments(function() { return arguments; }());
 * // => true
 *
 * _.isArguments([1, 2, 3]);
 * // => false
 */
function isArguments(value) {
  return isObjectLike(value) && isArrayLike(value) &&
    hasOwnProperty.call(value, 'callee') && !propertyIsEnumerable.call(value, 'callee');
}

module.exports = isArguments;

},{}],23:[function(require,module,exports){
/**
 * lodash 3.0.4 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */

/** `Object#toString` result references. */
var arrayTag = '[object Array]',
    funcTag = '[object Function]';

/** Used to detect host constructors (Safari > 5). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var fnToString = Function.prototype.toString;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  fnToString.call(hasOwnProperty).replace(/[\\^$.*+?()[\]{}|]/g, '\\$&')
  .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/* Native method references for those with the same name as other `lodash` methods. */
var nativeIsArray = getNative(Array, 'isArray');

/**
 * Used as the [maximum length](http://ecma-international.org/ecma-262/6.0/#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * Gets the native function at `key` of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {string} key The key of the method to get.
 * @returns {*} Returns the function if it's native, else `undefined`.
 */
function getNative(object, key) {
  var value = object == null ? undefined : object[key];
  return isNative(value) ? value : undefined;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](http://ecma-international.org/ecma-262/6.0/#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is classified as an `Array` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isArray([1, 2, 3]);
 * // => true
 *
 * _.isArray(function() { return arguments; }());
 * // => false
 */
var isArray = nativeIsArray || function(value) {
  return isObjectLike(value) && isLength(value.length) && objToString.call(value) == arrayTag;
};

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in older versions of Chrome and Safari which return 'function' for regexes
  // and Safari 8 equivalents which return 'object' for typed array constructors.
  return isObject(value) && objToString.call(value) == funcTag;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Checks if `value` is a native function.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
 * @example
 *
 * _.isNative(Array.prototype.push);
 * // => true
 *
 * _.isNative(_);
 * // => false
 */
function isNative(value) {
  if (value == null) {
    return false;
  }
  if (isFunction(value)) {
    return reIsNative.test(fnToString.call(value));
  }
  return isObjectLike(value) && reIsHostCtor.test(value);
}

module.exports = isArray;

},{}],24:[function(require,module,exports){
/**
 * lodash 3.2.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseFor = require('lodash._basefor'),
    isArguments = require('lodash.isarguments'),
    keysIn = require('lodash.keysin');

/** `Object#toString` result references. */
var objectTag = '[object Object]';

/**
 * Checks if `value` is object-like.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 */
function isObjectLike(value) {
  return !!value && typeof value == 'object';
}

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
 * of values.
 */
var objToString = objectProto.toString;

/**
 * The base implementation of `_.forIn` without support for callback
 * shorthands and `this` binding.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Object} Returns `object`.
 */
function baseForIn(object, iteratee) {
  return baseFor(object, iteratee, keysIn);
}

/**
 * Checks if `value` is a plain object, that is, an object created by the
 * `Object` constructor or one with a `[[Prototype]]` of `null`.
 *
 * **Note:** This method assumes objects created by the `Object` constructor
 * have no inherited enumerable properties.
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a plain object, else `false`.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 * }
 *
 * _.isPlainObject(new Foo);
 * // => false
 *
 * _.isPlainObject([1, 2, 3]);
 * // => false
 *
 * _.isPlainObject({ 'x': 0, 'y': 0 });
 * // => true
 *
 * _.isPlainObject(Object.create(null));
 * // => true
 */
function isPlainObject(value) {
  var Ctor;

  // Exit early for non `Object` objects.
  if (!(isObjectLike(value) && objToString.call(value) == objectTag && !isArguments(value)) ||
      (!hasOwnProperty.call(value, 'constructor') && (Ctor = value.constructor, typeof Ctor == 'function' && !(Ctor instanceof Ctor)))) {
    return false;
  }
  // IE < 9 iterates inherited properties before own properties. If the first
  // iterated property is an object's own property then there are no inherited
  // enumerable properties.
  var result;
  // In most environments an object's own properties are iterated before
  // its inherited properties. If the last iterated property is an object's
  // own property then there are no inherited enumerable properties.
  baseForIn(value, function(subValue, key) {
    result = key;
  });
  return result === undefined || hasOwnProperty.call(value, result);
}

module.exports = isPlainObject;

},{"lodash._basefor":16,"lodash.isarguments":22,"lodash.keysin":26}],25:[function(require,module,exports){
/**
 * lodash 3.0.0 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.7.0 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var createCompounder = require('lodash._createcompounder');

/**
 * Converts `string` to kebab case (a.k.a. spinal case).
 * See [Wikipedia](https://en.wikipedia.org/wiki/Letter_case#Special_case_styles) for
 * more details.
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to convert.
 * @returns {string} Returns the kebab cased string.
 * @example
 *
 * _.kebabCase('Foo Bar');
 * // => 'foo-bar'
 *
 * _.kebabCase('fooBar');
 * // => 'foo-bar'
 *
 * _.kebabCase('__foo_bar__');
 * // => 'foo-bar'
 */
var kebabCase = createCompounder(function(result, word, index) {
  return result + (index ? '-' : '') + word.toLowerCase();
});

module.exports = kebabCase;

},{"lodash._createcompounder":18}],26:[function(require,module,exports){
/**
 * lodash 3.0.8 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var isArguments = require('lodash.isarguments'),
    isArray = require('lodash.isarray');

/** Used to detect unsigned integer values. */
var reIsUint = /^\d+$/;

/** Used for native method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used as the [maximum length](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-number.max_safe_integer)
 * of an array-like value.
 */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  value = (typeof value == 'number' || reIsUint.test(value)) ? +value : -1;
  length = length == null ? MAX_SAFE_INTEGER : length;
  return value > -1 && value % 1 == 0 && value < length;
}

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This function is based on [`ToLength`](https://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength).
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 */
function isLength(value) {
  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

/**
 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(1);
 * // => false
 */
function isObject(value) {
  // Avoid a V8 JIT bug in Chrome 19-20.
  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
  var type = typeof value;
  return !!value && (type == 'object' || type == 'function');
}

/**
 * Creates an array of the own and inherited enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects.
 *
 * @static
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keysIn(new Foo);
 * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
 */
function keysIn(object) {
  if (object == null) {
    return [];
  }
  if (!isObject(object)) {
    object = Object(object);
  }
  var length = object.length;
  length = (length && isLength(length) &&
    (isArray(object) || isArguments(object)) && length) || 0;

  var Ctor = object.constructor,
      index = -1,
      isProto = typeof Ctor == 'function' && Ctor.prototype === object,
      result = Array(length),
      skipIndexes = length > 0;

  while (++index < length) {
    result[index] = (index + '');
  }
  for (var key in object) {
    if (!(skipIndexes && isIndex(key, length)) &&
        !(key == 'constructor' && (isProto || !hasOwnProperty.call(object, key)))) {
      result.push(key);
    }
  }
  return result;
}

module.exports = keysIn;

},{"lodash.isarguments":22,"lodash.isarray":23}],27:[function(require,module,exports){
/**
 * lodash 3.0.2 (Custom Build) <https://lodash.com/>
 * Build: `lodash modern modularize exports="npm" -o ./`
 * Copyright 2012-2015 The Dojo Foundation <http://dojofoundation.org/>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 * Available under MIT license <https://lodash.com/license>
 */
var baseToString = require('lodash._basetostring'),
    isIterateeCall = require('lodash._isiterateecall');

/** Used to match words to create compound words. */
var reWords = (function() {
  var upper = '[A-Z\\xc0-\\xd6\\xd8-\\xde]',
      lower = '[a-z\\xdf-\\xf6\\xf8-\\xff]+';

  return RegExp(upper + '+(?=' + upper + lower + ')|' + upper + '?' + lower + '|' + upper + '+|[0-9]+', 'g');
}());

/**
 * Splits `string` into an array of its words.
 *
 * @static
 * @memberOf _
 * @category String
 * @param {string} [string=''] The string to inspect.
 * @param {RegExp|string} [pattern] The pattern to match words.
 * @param- {Object} [guard] Enables use as a callback for functions like `_.map`.
 * @returns {Array} Returns the words of `string`.
 * @example
 *
 * _.words('fred, barney, & pebbles');
 * // => ['fred', 'barney', 'pebbles']
 *
 * _.words('fred, barney, & pebbles', /[^, ]+/g);
 * // => ['fred', 'barney', '&', 'pebbles']
 */
function words(string, pattern, guard) {
  if (guard && isIterateeCall(string, pattern, guard)) {
    pattern = undefined;
  }
  string = baseToString(string);
  return string.match(pattern || reWords) || [];
}

module.exports = words;

},{"lodash._basetostring":17,"lodash._isiterateecall":19}],28:[function(require,module,exports){
'use strict';

var classIdSplit = /([\.#]?[a-zA-Z0-9\u007F-\uFFFF_:-]+)/;
var notClassId = /^\.|#/;

module.exports = parseTag;

function parseTag(tag, props) {
  if (!tag) {
    return 'DIV';
  }

  var noId = !(props.hasOwnProperty('id'));

  var tagParts = tag.split(classIdSplit);
  var tagName = null;

  if (notClassId.test(tagParts[1])) {
    tagName = 'DIV';
  }

  var classes, part, type, i;

  for (i = 0; i < tagParts.length; i++) {
    part = tagParts[i];

    if (!part) {
      continue;
    }

    type = part.charAt(0);

    if (!tagName) {
      tagName = part;
    } else if (type === '.') {
      classes = classes || [];
      classes.push(part.substring(1, part.length));
    } else if (type === '#' && noId) {
      props.id = part.substring(1, part.length);
    }
  }

  if (classes) {
    if (props.className) {
      classes.push(props.className);
    }

    props.className = classes.join(' ');
  }

  return props.namespace ? tagName : tagName.toUpperCase();
}

},{}],29:[function(require,module,exports){
var hiddenStore = require('./hidden-store.js');

module.exports = createStore;

function createStore() {
    var key = {};

    return function (obj) {
        if ((typeof obj !== 'object' || obj === null) &&
            typeof obj !== 'function'
        ) {
            throw new Error('Weakmap-shim: Key must be object')
        }

        var store = obj.valueOf(key);
        return store && store.identity === key ?
            store : hiddenStore(obj, key);
    };
}

},{"./hidden-store.js":30}],30:[function(require,module,exports){
module.exports = hiddenStore;

function hiddenStore(obj, key) {
    var store = { identity: key };
    var valueOf = obj.valueOf;

    Object.defineProperty(obj, "valueOf", {
        value: function (value) {
            return value !== key ?
                valueOf.apply(this, arguments) : store;
        },
        writable: true
    });

    return store;
}

},{}],31:[function(require,module,exports){
var createElement = require("./vdom/create-element.js")

module.exports = createElement

},{"./vdom/create-element.js":37}],32:[function(require,module,exports){
var diff = require("./vtree/diff.js")

module.exports = diff

},{"./vtree/diff.js":55}],33:[function(require,module,exports){
var h = require("./virtual-hyperscript/index.js")

module.exports = h

},{"./virtual-hyperscript/index.js":45}],34:[function(require,module,exports){
module.exports = require('./vdom/setup-initialize-widgets.js').initialize

},{"./vdom/setup-initialize-widgets.js":41}],35:[function(require,module,exports){
var patch = require("./vdom/patch.js")

module.exports = patch

},{"./vdom/patch.js":40}],36:[function(require,module,exports){
var isObject = require("is-object")
var isHook = require("../vnode/is-vhook.js")

module.exports = applyProperties

function applyProperties(node, props, previous) {
    for (var propName in props) {
        var propValue = props[propName]

        if (propValue === undefined) {
            removeProperty(node, propName, propValue, previous);
        } else if (isHook(propValue)) {
            removeProperty(node, propName, propValue, previous)
            if (propValue.hook) {
                propValue.hook(node,
                    propName,
                    previous ? previous[propName] : undefined)
            }
        } else {
            if (isObject(propValue)) {
                patchObject(node, props, previous, propName, propValue);
            } else {
                node[propName] = propValue
            }
        }
    }
}

function removeProperty(node, propName, propValue, previous) {
    if (previous) {
        var previousValue = previous[propName]

        if (!isHook(previousValue)) {
            if (propName === "attributes") {
                for (var attrName in previousValue) {
                    node.removeAttribute(attrName)
                }
            } else if (propName === "style") {
                for (var i in previousValue) {
                    node.style[i] = ""
                }
            } else if (typeof previousValue === "string") {
                node[propName] = ""
            } else {
                node[propName] = null
            }
        } else if (previousValue.unhook) {
            previousValue.unhook(node, propName, propValue)
        }
    }
}

function patchObject(node, props, previous, propName, propValue) {
    var previousValue = previous ? previous[propName] : undefined

    // Set attributes
    if (propName === "attributes") {
        for (var attrName in propValue) {
            var attrValue = propValue[attrName]

            if (attrValue === undefined) {
                node.removeAttribute(attrName)
            } else {
                node.setAttribute(attrName, attrValue)
            }
        }

        return
    }

    if(previousValue && isObject(previousValue) &&
        getPrototype(previousValue) !== getPrototype(propValue)) {
        node[propName] = propValue
        return
    }

    if (!isObject(node[propName])) {
        node[propName] = {}
    }

    var replacer = propName === "style" ? "" : undefined

    for (var k in propValue) {
        var value = propValue[k]
        node[propName][k] = (value === undefined) ? replacer : value
    }
}

function getPrototype(value) {
    if (Object.getPrototypeOf) {
        return Object.getPrototypeOf(value)
    } else if (value.__proto__) {
        return value.__proto__
    } else if (value.constructor) {
        return value.constructor.prototype
    }
}

},{"../vnode/is-vhook.js":46,"is-object":15}],37:[function(require,module,exports){
var document = require("global/document")

var applyProperties = require("./apply-properties")
var setupInitializeWidgets = require("./setup-initialize-widgets")

var isVNode = require("../vnode/is-vnode.js")
var isVText = require("../vnode/is-vtext.js")
var isWidget = require("../vnode/is-widget.js")

module.exports = createElement

function createElement(vnode, opts) {
    var doc = opts ? opts.document || document : document
    var warn = opts ? opts.warn : null
    var node

    if (isWidget(vnode)) {
        var widget = vnode.init()
        node = createElement(widget)
        setupInitializeWidgets(node, vnode)
        return node
    } else if (isVText(vnode)) {
        return doc.createTextNode(vnode.text)
    } else if (!isVNode(vnode)) {
        if (warn) {
            warn("Item is not a valid virtual dom node", vnode)
        }
        return null
    }

    node = (vnode.namespace === null) ?
        doc.createElement(vnode.tagName) :
        doc.createElementNS(vnode.namespace, vnode.tagName)

    var props = vnode.properties
    applyProperties(node, props)

    var children = vnode.children

    for (var i = 0; i < children.length; i++) {
        var childNode = createElement(children[i], opts)
        if (childNode) {
            node.appendChild(childNode)
        }
    }

    setupInitializeWidgets(node, vnode)

    return node
}

},{"../vnode/is-vnode.js":47,"../vnode/is-vtext.js":48,"../vnode/is-widget.js":49,"./apply-properties":36,"./setup-initialize-widgets":41,"global/document":12}],38:[function(require,module,exports){
// Maps a virtual DOM tree onto a real DOM tree in an efficient manner.
// We don't want to read all of the DOM nodes in the tree so we use
// the in-order tree indexing to eliminate recursion down certain branches.
// We only recurse into a DOM node if we know that it contains a child of
// interest.

var noChild = {}

module.exports = domIndex

function domIndex(rootNode, tree, indices, nodes) {
    if (!indices || indices.length === 0) {
        return {}
    } else {
        indices.sort(ascending)
        return recurse(rootNode, tree, indices, nodes, 0)
    }
}

function recurse(rootNode, tree, indices, nodes, rootIndex) {
    nodes = nodes || {}


    if (rootNode) {
        if (indexInRange(indices, rootIndex, rootIndex)) {
            nodes[rootIndex] = rootNode
        }

        var vChildren = tree.children

        if (vChildren) {

            var childNodes = rootNode.childNodes

            for (var i = 0; i < tree.children.length; i++) {
                rootIndex += 1

                var vChild = vChildren[i] || noChild
                var nextIndex = rootIndex + (vChild.count || 0)

                // skip recursion down the tree if there are no nodes down here
                if (indexInRange(indices, rootIndex, nextIndex)) {
                    recurse(childNodes[i], vChild, indices, nodes, rootIndex)
                }

                rootIndex = nextIndex
            }
        }
    }

    return nodes
}

// Binary search for an index in the interval [left, right]
function indexInRange(indices, left, right) {
    if (indices.length === 0) {
        return false
    }

    var minIndex = 0
    var maxIndex = indices.length - 1
    var currentIndex
    var currentItem

    while (minIndex <= maxIndex) {
        currentIndex = ((maxIndex + minIndex) / 2) >> 0
        currentItem = indices[currentIndex]

        if (minIndex === maxIndex) {
            return currentItem >= left && currentItem <= right
        } else if (currentItem < left) {
            minIndex = currentIndex + 1
        } else  if (currentItem > right) {
            maxIndex = currentIndex - 1
        } else {
            return true
        }
    }

    return false;
}

function ascending(a, b) {
    return a > b ? 1 : -1
}

},{}],39:[function(require,module,exports){
var applyProperties = require("./apply-properties")

var isWidget = require("../vnode/is-widget.js")
var VPatch = require("../vnode/vpatch.js")

var updateWidget = require("./update-widget")
var createElement = require("./create-element")

module.exports = applyPatch

function applyPatch(vpatch, domNode, renderOptions) {
    var type = vpatch.type
    var vNode = vpatch.vNode
    var patch = vpatch.patch

    switch (type) {
        case VPatch.REMOVE:
            return removeNode(domNode, vNode)
        case VPatch.INSERT:
            return insertNode(domNode, patch, renderOptions)
        case VPatch.VTEXT:
            return stringPatch(domNode, vNode, patch, renderOptions)
        case VPatch.WIDGET:
            return widgetPatch(domNode, vNode, patch, renderOptions)
        case VPatch.VNODE:
            return vNodePatch(domNode, vNode, patch, renderOptions)
        case VPatch.ORDER:
            reorderChildren(domNode, patch)
            return domNode
        case VPatch.PROPS:
            applyProperties(domNode, patch, vNode.properties)
            return domNode
        default:
            return domNode
    }
}

function removeNode(domNode, vNode) {
    var parentNode = domNode.parentNode

    predestroyWidget(domNode, vNode);

    if (parentNode) {
        parentNode.removeChild(domNode);
    }

    destroyWidget(domNode, vNode);

    return null
}

function insertNode(parentNode, vNode, renderOptions) {
    var newNode = renderOptions.render(vNode, renderOptions)

    if (parentNode) {
        parentNode.appendChild(newNode)
    }

    return parentNode
}

function stringPatch(domNode, leftVNode, vText, renderOptions) {
    var newNode

    if (domNode.nodeType === 3) {
        domNode.replaceData(0, domNode.length, vText.text)
        newNode = domNode
    } else {
        var parentNode = domNode.parentNode
        newNode = renderOptions.render(vText, renderOptions)

        if (parentNode && newNode !== domNode) {
            parentNode.replaceChild(newNode, domNode)
        }
    }

    return newNode
}

function widgetPatch(domNode, leftVNode, widget, renderOptions) {
    var updating = updateWidget(leftVNode, widget)
    var newNode

    if (updating) {
        var widget = widget.update(leftVNode, domNode)
        newNode = widget ? createElement(widget) : domNode
    } else {
        newNode = renderOptions.render(widget, renderOptions)
    }

    var parentNode = domNode.parentNode

    if (!updating) {
        predestroyWidget(domNode, leftVNode)
    }

    if (parentNode && newNode !== domNode) {
        parentNode.replaceChild(newNode, domNode)
    }

    if (!updating) {
        destroyWidget(domNode, leftVNode)
    }

    return newNode
}

function vNodePatch(domNode, leftVNode, vNode, renderOptions) {
    var parentNode = domNode.parentNode
    var newNode = renderOptions.render(vNode, renderOptions)

    if (parentNode && newNode !== domNode) {
        parentNode.replaceChild(newNode, domNode)
    }

    return newNode
}

function predestroyWidget(domNode, w) {
  if (typeof w.predestroy === "function" && isWidget(w)) {
      w.predestroy(domNode)
  }
}

function destroyWidget(domNode, w) {
    if (typeof w.destroy === "function" && isWidget(w)) {
        w.destroy(domNode)
    }
}

function reorderChildren(domNode, moves) {
    var childNodes = domNode.childNodes
    var keyMap = {}
    var node
    var remove
    var insert

    for (var i = 0; i < moves.removes.length; i++) {
        remove = moves.removes[i]
        node = childNodes[remove.from]
        if (remove.key) {
            keyMap[remove.key] = node
        }
        domNode.removeChild(node)
    }

    var length = childNodes.length
    for (var j = 0; j < moves.inserts.length; j++) {
        insert = moves.inserts[j]
        node = keyMap[insert.key]
        // this is the weirdest bug i've ever seen in webkit
        domNode.insertBefore(node, insert.to >= length++ ? null : childNodes[insert.to])
    }
}

function replaceRoot(oldRoot, newRoot) {
    if (oldRoot && newRoot && oldRoot !== newRoot && oldRoot.parentNode) {
        oldRoot.parentNode.replaceChild(newRoot, oldRoot)
    }

    return newRoot;
}

},{"../vnode/is-widget.js":49,"../vnode/vpatch.js":52,"./apply-properties":36,"./create-element":37,"./update-widget":42}],40:[function(require,module,exports){
var document = require("global/document")

var render = require("./create-element")
var domIndex = require("./dom-index")
var patchOp = require("./patch-op")
var initializeWidgets = require("../initialize-widgets")
module.exports = patch

function patch(rootNode, patches, renderOptions) {
    renderOptions = renderOptions || {}
    renderOptions.patch = renderOptions.patch && renderOptions.patch !== patch
        ? renderOptions.patch
        : patchRecursive
    renderOptions.render = renderOptions.render || render

    var newNode = renderOptions.patch(rootNode, patches, renderOptions)

    initializeWidgets(newNode, rootNode)

    return newNode
}

function patchRecursive(rootNode, patches, renderOptions) {
    var indices = patchIndices(patches)

    if (indices.length === 0) {
        return rootNode
    }

    var index = domIndex(rootNode, patches.a, indices)
    var ownerDocument = rootNode.ownerDocument

    if (!renderOptions.document && ownerDocument !== document) {
        renderOptions.document = ownerDocument
    }

    for (var i = 0; i < indices.length; i++) {
        var nodeIndex = indices[i]
        rootNode = applyPatch(rootNode,
            index[nodeIndex],
            patches[nodeIndex],
            renderOptions)
    }

    return rootNode
}

function applyPatch(rootNode, domNode, patchList, renderOptions) {
    if (!domNode) {
        return rootNode
    }

    var newNode

    if (Array.isArray(patchList)) {
        for (var i = 0; i < patchList.length; i++) {
            newNode = patchOp(patchList[i], domNode, renderOptions)

            if (domNode === rootNode) {
                rootNode = newNode
            }
        }
    } else {
        newNode = patchOp(patchList, domNode, renderOptions)

        if (domNode === rootNode) {
            rootNode = newNode
        }
    }

    return rootNode
}

function patchIndices(patches) {
    var indices = []

    for (var key in patches) {
        if (key !== "a") {
            indices.push(Number(key))
        }
    }

    return indices
}

},{"../initialize-widgets":34,"./create-element":37,"./dom-index":38,"./patch-op":39,"global/document":12}],41:[function(require,module,exports){
var isWidget = require("../vnode/is-widget.js")

var INITIALIZED_KEY = '__VDOM___INITIALIZED_KEY__'
var POSTINIT_KEY = '__VDOM___POSTINIT_KEY__'

function initialize (node, previous) {
    if (!node) {
        return
    }

    if (previous && typeof previous[INITIALIZED_KEY] !== 'undefined') {
        node[INITIALIZED_KEY] = previous[INITIALIZED_KEY]
    }

    if (node[INITIALIZED_KEY] === false) {
        node[POSTINIT_KEY]()
    } else if (node[INITIALIZED_KEY] === true) {
        initializeChildren(node)
    }
}

function initializeChildren (node) {
    var len = node.childNodes.length
    var i = -1

    while (++i < len) {
        initialize(node.childNodes[i])
    }
}

function setupInitializeWidgets (node, vnode) {
    var outerPostinit

    if (!node || node[INITIALIZED_KEY] === true) {
      return
    }

    node[INITIALIZED_KEY] = false
    vnode[INITIALIZED_KEY] = false

    outerPostinit = node[POSTINIT_KEY] || function () {}

    node[POSTINIT_KEY] = function () {
        if (node[INITIALIZED_KEY] === true) {
          return
        }

        initializeChildren(node)

        if (typeof vnode.postinit === "function" && isWidget(vnode) && vnode[INITIALIZED_KEY] === false) {
            vnode[INITIALIZED_KEY] = true
            vnode.postinit(node)
        }

        outerPostinit()

        node[INITIALIZED_KEY] = true
    }

    if (node.parentNode && node.parentNode[INITIALIZED_KEY] === true) {
        node[POSTINIT_KEY]()
    }
}

setupInitializeWidgets.initialize = initialize
module.exports = setupInitializeWidgets

},{"../vnode/is-widget.js":49}],42:[function(require,module,exports){
var isWidget = require("../vnode/is-widget.js")

module.exports = updateWidget

function updateWidget(a, b) {
    if (isWidget(a) && isWidget(b)) {
        if ("name" in a && "name" in b) {
            return a.id === b.id
        } else {
            return a.init === b.init
        }
    }

    return false
}

},{"../vnode/is-widget.js":49}],43:[function(require,module,exports){
'use strict';

module.exports = AttributeHook;

function AttributeHook(namespace, value) {
    if (!(this instanceof AttributeHook)) {
        return new AttributeHook(namespace, value);
    }

    this.namespace = namespace;
    this.value = value;
}

AttributeHook.prototype.hook = function (node, prop, prev) {
    if (prev && prev.type === 'AttributeHook' &&
        prev.value === this.value &&
        prev.namespace === this.namespace) {
        return;
    }

    node.setAttributeNS(this.namespace, prop, this.value);
};

AttributeHook.prototype.unhook = function (node, prop, next) {
    if (next && next.type === 'AttributeHook' &&
        next.namespace === this.namespace) {
        return;
    }

    var colonPosition = prop.indexOf(':');
    var localName = colonPosition > -1 ? prop.substr(colonPosition + 1) : prop;
    node.removeAttributeNS(this.namespace, localName);
};

AttributeHook.prototype.type = 'AttributeHook';

},{}],44:[function(require,module,exports){
'use strict';

module.exports = SoftSetHook;

function SoftSetHook(value) {
    if (!(this instanceof SoftSetHook)) {
        return new SoftSetHook(value);
    }

    this.value = value;
}

SoftSetHook.prototype.hook = function (node, propertyName) {
    if (node[propertyName] !== this.value) {
        node[propertyName] = this.value;
    }
};

},{}],45:[function(require,module,exports){
'use strict';

var VNode = require('../vnode/vnode.js');
var VText = require('../vnode/vtext.js');
var isVNode = require('../vnode/is-vnode');
var isVText = require('../vnode/is-vtext');
var isWidget = require('../vnode/is-widget');
var isHook = require('../vnode/is-vhook');

var softSetHook = require('./hooks/soft-set-hook.js');

module.exports = h;

function h(tagName, properties, children) {
    var childNodes = [];
    var tag, props, key, namespace;

    if (!children && isChildren(properties)) {
        children = properties;
        props = {};
    }

    props = props || properties || {};

    if (tagName) {
      tag = props.namespace ? tagName : tagName.toUpperCase();
    } else {
      tag = 'DIV';
    }

    // support keys
    if (props.hasOwnProperty('key')) {
        key = props.key;
        props.key = undefined;
    }

    // support namespace
    if (props.hasOwnProperty('namespace')) {
        namespace = props.namespace;
        props.namespace = undefined;
    }

    // fix cursor bug
    if (tag === 'INPUT' &&
        !namespace &&
        props.hasOwnProperty('value') &&
        props.value !== undefined &&
        !isHook(props.value)
    ) {
        props.value = softSetHook(props.value);
    }

    if (children !== undefined && children !== null) {
        addChild(children, childNodes, tag, props);
    }


    return new VNode(tag, props, childNodes, key, namespace);
}

function addChild(c, childNodes, tag, props) {
    if (typeof c === 'string') {
        childNodes.push(new VText(c));
    } else if (typeof c === 'number') {
        childNodes.push(new VText(String(c)));
    } else if (isChild(c)) {
        childNodes.push(c);
    } else if (Array.isArray(c)) {
        for (var i = 0; i < c.length; i++) {
            addChild(c[i], childNodes, tag, props);
        }
    } else if (c === null || c === undefined) {
        return;
    } else {
        throw UnexpectedVirtualElement({
            foreignObject: c,
            parentVnode: {
                tagName: tag,
                properties: props
            }
        });
    }
}

function isChild(x) {
    return isVNode(x) || isVText(x) || isWidget(x);
}

function isChildren(x) {
    return typeof x === 'string' || Array.isArray(x) || isChild(x);
}

function UnexpectedVirtualElement(data) {
    var err = new Error();

    err.type = 'virtual-hyperscript.unexpected.virtual-element';
    err.message = 'Unexpected virtual child passed to h().\n' +
        'Expected a VNode / Vthunk / VWidget / string but:\n' +
        'got:\n' +
        errorString(data.foreignObject) +
        '.\n' +
        'The parent vnode is:\n' +
        errorString(data.parentVnode) +
        '\n' +
        'Suggested fix: change your `h(..., [ ... ])` callsite.';
    err.foreignObject = data.foreignObject;
    err.parentVnode = data.parentVnode;

    return err;
}

function errorString(obj) {
    try {
        return JSON.stringify(obj, null, '    ');
    } catch (e) {
        return String(obj);
    }
}

},{"../vnode/is-vhook":46,"../vnode/is-vnode":47,"../vnode/is-vtext":48,"../vnode/is-widget":49,"../vnode/vnode.js":51,"../vnode/vtext.js":53,"./hooks/soft-set-hook.js":44}],46:[function(require,module,exports){
module.exports = isHook

function isHook(hook) {
    return hook &&
      (typeof hook.hook === "function" && !hook.hasOwnProperty("hook") ||
       typeof hook.unhook === "function" && !hook.hasOwnProperty("unhook"))
}

},{}],47:[function(require,module,exports){
var version = require("./version")

module.exports = isVirtualNode

function isVirtualNode(x) {
    return x && x.type === "VirtualNode" && x.version === version
}

},{"./version":50}],48:[function(require,module,exports){
var version = require("./version")

module.exports = isVirtualText

function isVirtualText(x) {
    return x && x.type === "VirtualText" && x.version === version
}

},{"./version":50}],49:[function(require,module,exports){
module.exports = isWidget

function isWidget(w) {
    return w && w.type === "Widget"
}

},{}],50:[function(require,module,exports){
module.exports = "2"

},{}],51:[function(require,module,exports){
var version = require("./version")
var isVNode = require("./is-vnode")
var isWidget = require("./is-widget")
var isVHook = require("./is-vhook")

module.exports = VirtualNode

var noProperties = {}
var noChildren = []

function VirtualNode(tagName, properties, children, key, namespace) {
    this.tagName = tagName
    this.properties = properties || noProperties
    this.children = children || noChildren
    this.key = key != null ? String(key) : undefined
    this.namespace = (typeof namespace === "string") ? namespace : null

    var count = (children && children.length) || 0
    var descendants = 0
    var hasWidgets = false
    var hasThunks = false
    var descendantHooks = false
    var hooks

    for (var propName in properties) {
        if (properties.hasOwnProperty(propName)) {
            var property = properties[propName]
            if (isVHook(property) && property.unhook) {
                if (!hooks) {
                    hooks = {}
                }

                hooks[propName] = property
            }
        }
    }

    for (var i = 0; i < count; i++) {
        var child = children[i]
        if (isVNode(child)) {
            descendants += child.count || 0

            if (!hasWidgets && child.hasWidgets) {
                hasWidgets = true
            }

            if (!hasThunks && child.hasThunks) {
                hasThunks = true
            }

            if (!descendantHooks && (child.hooks || child.descendantHooks)) {
                descendantHooks = true
            }
        } else if (!hasWidgets && isWidget(child)) {
            if (typeof child.destroy === "function") {
                hasWidgets = true
            }
        }
    }

    this.count = count + descendants
    this.hasWidgets = hasWidgets
    this.hooks = hooks
    this.descendantHooks = descendantHooks
}

VirtualNode.prototype.version = version
VirtualNode.prototype.type = "VirtualNode"

},{"./is-vhook":46,"./is-vnode":47,"./is-widget":49,"./version":50}],52:[function(require,module,exports){
var version = require("./version")

VirtualPatch.NONE = 0
VirtualPatch.VTEXT = 1
VirtualPatch.VNODE = 2
VirtualPatch.WIDGET = 3
VirtualPatch.PROPS = 4
VirtualPatch.ORDER = 5
VirtualPatch.INSERT = 6
VirtualPatch.REMOVE = 7

module.exports = VirtualPatch

function VirtualPatch(type, vNode, patch) {
    this.type = Number(type)
    this.vNode = vNode
    this.patch = patch
}

VirtualPatch.prototype.version = version
VirtualPatch.prototype.type = "VirtualPatch"

},{"./version":50}],53:[function(require,module,exports){
var version = require("./version")

module.exports = VirtualText

function VirtualText(text) {
    this.text = String(text)
}

VirtualText.prototype.version = version
VirtualText.prototype.type = "VirtualText"

},{"./version":50}],54:[function(require,module,exports){
var isObject = require("is-object")
var isHook = require("../vnode/is-vhook")

module.exports = diffProps

function diffProps(a, b) {
    var diff

    for (var aKey in a) {
        if (!(aKey in b)) {
            diff = diff || {}
            diff[aKey] = undefined
        }

        var aValue = a[aKey]
        var bValue = b[aKey]

        if (aValue === bValue) {
            continue
        } else if (isObject(aValue) && isObject(bValue)) {
            if (getPrototype(bValue) !== getPrototype(aValue)) {
                diff = diff || {}
                diff[aKey] = bValue
            } else if (isHook(bValue)) {
                 diff = diff || {}
                 diff[aKey] = bValue
            } else {
                var objectDiff = diffProps(aValue, bValue)
                if (objectDiff) {
                    diff = diff || {}
                    diff[aKey] = objectDiff
                }
            }
        } else {
            diff = diff || {}
            diff[aKey] = bValue
        }
    }

    for (var bKey in b) {
        if (!(bKey in a)) {
            diff = diff || {}
            diff[bKey] = b[bKey]
        }
    }

    return diff
}

function getPrototype(value) {
  if (Object.getPrototypeOf) {
    return Object.getPrototypeOf(value)
  } else if (value.__proto__) {
    return value.__proto__
  } else if (value.constructor) {
    return value.constructor.prototype
  }
}

},{"../vnode/is-vhook":46,"is-object":15}],55:[function(require,module,exports){
var VPatch = require("../vnode/vpatch")
var isVNode = require("../vnode/is-vnode")
var isVText = require("../vnode/is-vtext")
var isWidget = require("../vnode/is-widget")

var diffProps = require("./diff-props")

module.exports = diff

function diff(a, b) {
    var patch = { a: a }
    walk(a, b, patch, 0)
    return patch
}

function walk(a, b, patch, index) {
    if (a === b) {
        return
    }

    var apply = patch[index]
    var applyClear = false

    if (b == null) {

        // If a is a widget we will add a remove patch for it
        // Otherwise any child widgets/hooks must be destroyed.
        // This prevents adding two remove patches for a widget.
        if (!isWidget(a)) {
            clearState(a, patch, index)
            apply = patch[index]
        }

        apply = appendPatch(apply, new VPatch(VPatch.REMOVE, a, b))
    } else if (isVNode(b)) {
        if (isVNode(a)) {
            if (a.tagName === b.tagName &&
                a.namespace === b.namespace &&
                a.key === b.key) {
                var propsPatch = diffProps(a.properties, b.properties)
                if (propsPatch) {
                    apply = appendPatch(apply,
                        new VPatch(VPatch.PROPS, a, propsPatch))
                }
                apply = diffChildren(a, b, patch, apply, index)
            } else {
                apply = appendPatch(apply, new VPatch(VPatch.VNODE, a, b))
                applyClear = true
            }
        } else {
            apply = appendPatch(apply, new VPatch(VPatch.VNODE, a, b))
            applyClear = true
        }
    } else if (isVText(b)) {
        if (!isVText(a)) {
            apply = appendPatch(apply, new VPatch(VPatch.VTEXT, a, b))
            applyClear = true
        } else if (a.text !== b.text) {
            apply = appendPatch(apply, new VPatch(VPatch.VTEXT, a, b))
        }
    } else if (isWidget(b)) {
        if (!isWidget(a)) {
            applyClear = true
        }

        apply = appendPatch(apply, new VPatch(VPatch.WIDGET, a, b))
    }

    if (apply) {
        patch[index] = apply
    }

    if (applyClear) {
        clearState(a, patch, index)
    }
}

function diffChildren(a, b, patch, apply, index) {
    var aChildren = a.children
    var orderedSet = reorder(aChildren, b.children)
    var bChildren = orderedSet.children

    var aLen = aChildren.length
    var bLen = bChildren.length
    var len = aLen > bLen ? aLen : bLen

    for (var i = 0; i < len; i++) {
        var leftNode = aChildren[i]
        var rightNode = bChildren[i]
        index += 1

        if (!leftNode) {
            if (rightNode) {
                // Excess nodes in b need to be added
                apply = appendPatch(apply,
                    new VPatch(VPatch.INSERT, null, rightNode))
            }
        } else {
            walk(leftNode, rightNode, patch, index)
        }

        if (isVNode(leftNode) && leftNode.count) {
            index += leftNode.count
        }
    }

    if (orderedSet.moves) {
        // Reorder nodes last
        apply = appendPatch(apply, new VPatch(
            VPatch.ORDER,
            a,
            orderedSet.moves
        ))
    }

    return apply
}

function clearState(vNode, patch, index) {
    // TODO: Make this a single walk, not two
    unhook(vNode, patch, index)
    destroyWidgets(vNode, patch, index)
}

// Patch records for all destroyed widgets must be added because we need
// a DOM node reference for the destroy function
function destroyWidgets(vNode, patch, index) {
    if (isWidget(vNode)) {
        if (typeof vNode.destroy === "function") {
            patch[index] = appendPatch(
                patch[index],
                new VPatch(VPatch.REMOVE, vNode, null)
            )
        }
    } else if (isVNode(vNode) && (vNode.hasWidgets || vNode.hasThunks)) {
        var children = vNode.children
        var len = children.length
        for (var i = 0; i < len; i++) {
            var child = children[i]
            index += 1

            destroyWidgets(child, patch, index)

            if (isVNode(child) && child.count) {
                index += child.count
            }
        }
    }
}

function hasPatches(patch) {
    for (var index in patch) {
        if (index !== "a") {
            return true
        }
    }

    return false
}

// Execute hooks when two nodes are identical
function unhook(vNode, patch, index) {
    if (isVNode(vNode)) {
        if (vNode.hooks) {
            patch[index] = appendPatch(
                patch[index],
                new VPatch(
                    VPatch.PROPS,
                    vNode,
                    undefinedKeys(vNode.hooks)
                )
            )
        }

        if (vNode.descendantHooks || vNode.hasThunks) {
            var children = vNode.children
            var len = children.length
            for (var i = 0; i < len; i++) {
                var child = children[i]
                index += 1

                unhook(child, patch, index)

                if (isVNode(child) && child.count) {
                    index += child.count
                }
            }
        }
    }
}

function undefinedKeys(obj) {
    var result = {}

    for (var key in obj) {
        result[key] = undefined
    }

    return result
}

// List diff, naive left to right reordering
function reorder(aChildren, bChildren) {
    // O(M) time, O(M) memory
    var bChildIndex = keyIndex(bChildren)
    var bKeys = bChildIndex.keys
    var bFree = bChildIndex.free

    if (bFree.length === bChildren.length) {
        return {
            children: bChildren,
            moves: null
        }
    }

    // O(N) time, O(N) memory
    var aChildIndex = keyIndex(aChildren)
    var aKeys = aChildIndex.keys
    var aFree = aChildIndex.free

    if (aFree.length === aChildren.length) {
        return {
            children: bChildren,
            moves: null
        }
    }

    // O(MAX(N, M)) memory
    var newChildren = []

    var freeIndex = 0
    var freeCount = bFree.length
    var deletedItems = 0

    // Iterate through a and match a node in b
    // O(N) time,
    for (var i = 0 ; i < aChildren.length; i++) {
        var aItem = aChildren[i]
        var itemIndex

        if (aItem.key) {
            if (bKeys.hasOwnProperty(aItem.key)) {
                // Match up the old keys
                itemIndex = bKeys[aItem.key]
                newChildren.push(bChildren[itemIndex])

            } else {
                // Remove old keyed items
                itemIndex = i - deletedItems++
                newChildren.push(null)
            }
        } else {
            // Match the item in a with the next free item in b
            if (freeIndex < freeCount) {
                itemIndex = bFree[freeIndex++]
                newChildren.push(bChildren[itemIndex])
            } else {
                // There are no free items in b to match with
                // the free items in a, so the extra free nodes
                // are deleted.
                itemIndex = i - deletedItems++
                newChildren.push(null)
            }
        }
    }

    var lastFreeIndex = freeIndex >= bFree.length ?
        bChildren.length :
        bFree[freeIndex]

    // Iterate through b and append any new keys
    // O(M) time
    for (var j = 0; j < bChildren.length; j++) {
        var newItem = bChildren[j]

        if (newItem.key) {
            if (!aKeys.hasOwnProperty(newItem.key)) {
                // Add any new keyed items
                // We are adding new items to the end and then sorting them
                // in place. In future we should insert new items in place.
                newChildren.push(newItem)
            }
        } else if (j >= lastFreeIndex) {
            // Add any leftover non-keyed items
            newChildren.push(newItem)
        }
    }

    var simulate = newChildren.slice()
    var simulateIndex = 0
    var removes = []
    var inserts = []
    var simulateItem

    for (var k = 0; k < bChildren.length;) {
        var wantedItem = bChildren[k]
        simulateItem = simulate[simulateIndex]

        // remove items
        while (simulateItem === null && simulate.length) {
            removes.push(remove(simulate, simulateIndex, null))
            simulateItem = simulate[simulateIndex]
        }

        if (!simulateItem || simulateItem.key !== wantedItem.key) {
            // if we need a key in this position...
            if (wantedItem.key) {
                if (simulateItem && simulateItem.key) {
                    // if an insert doesn't put this key in place, it needs to move
                    if (bKeys[simulateItem.key] !== k + 1) {
                        removes.push(remove(simulate, simulateIndex, simulateItem.key))
                        simulateItem = simulate[simulateIndex]
                        // if the remove didn't put the wanted item in place, we need to insert it
                        if (!simulateItem || simulateItem.key !== wantedItem.key) {
                            inserts.push({key: wantedItem.key, to: k})
                        }
                        // items are matching, so skip ahead
                        else {
                            simulateIndex++
                        }
                    }
                    else {
                        inserts.push({key: wantedItem.key, to: k})
                    }
                }
                else {
                    inserts.push({key: wantedItem.key, to: k})
                }
                k++
            }
            // a key in simulate has no matching wanted key, remove it
            else if (simulateItem && simulateItem.key) {
                removes.push(remove(simulate, simulateIndex, simulateItem.key))
            }
        }
        else {
            simulateIndex++
            k++
        }
    }

    // remove all the remaining nodes from simulate
    while(simulateIndex < simulate.length) {
        simulateItem = simulate[simulateIndex]
        removes.push(remove(simulate, simulateIndex, simulateItem && simulateItem.key))
    }

    // If the only moves we have are deletes then we can just
    // let the delete patch remove these items.
    if (removes.length === deletedItems && !inserts.length) {
        return {
            children: newChildren,
            moves: null
        }
    }

    return {
        children: newChildren,
        moves: {
            removes: removes,
            inserts: inserts
        }
    }
}

function remove(arr, index, key) {
    arr.splice(index, 1)

    return {
        from: index,
        key: key
    }
}

function keyIndex(children) {
    var keys = {}
    var free = []
    var length = children.length

    for (var i = 0; i < length; i++) {
        var child = children[i]

        if (child.key) {
            keys[child.key] = i
        } else {
            free.push(i)
        }
    }

    return {
        keys: keys,     // A hash of key name to index
        free: free      // An array of unkeyed item indices
    }
}

function appendPatch(apply, patch) {
    if (apply) {
        if (Array.isArray(apply)) {
            apply.push(patch)
        } else {
            apply = [apply, patch]
        }

        return apply
    } else {
        return patch
    }
}

},{"../vnode/is-vnode":47,"../vnode/is-vtext":48,"../vnode/is-widget":49,"../vnode/vpatch":52,"./diff-props":54}],56:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = CompositePropSubject;

var _yolk = require('./yolk');

function CompositePropSubject(obj) {
  this._keys = Object.keys(obj);
  this._length = this._keys.length;
  this._obj = {};

  var i = -1;

  while (++i < this._length) {
    var key = this._keys[i];
    var value = obj[key];
    this._obj[key] = new _yolk.Rx.BehaviorSubject(value);
  }
}

CompositePropSubject.prototype = {
  asSubjectObject: function asSubjectObject() {
    return this._obj;
  },
  asDistinctObservableObject: function asDistinctObservableObject() {
    var obsObj = {};

    var i = -1;

    while (++i < this._length) {
      var key = this._keys[i];
      var subject = this._obj[key];
      obsObj[key] = subject.flatMapLatest(function (v) {
        return (0, _yolk.wrapObject)(v, { base: false });
      }).distinctUntilChanged(); // eslint-disable-line no-loop-func
    }

    return obsObj;
  },
  asObservableObject: function asObservableObject() {
    var obsObj = {};

    var i = -1;

    while (++i < this._length) {
      var key = this._keys[i];
      var subject = this._obj[key];
      obsObj[key] = subject.flatMapLatest(function (v) {
        return (0, _yolk.wrapObject)(v, { base: false });
      }); // eslint-disable-line no-loop-func
    }

    return obsObj;
  },
  onNext: function onNext(obj) {
    var i = -1;

    while (++i < this._length) {
      var key = this._keys[i];
      var value = obj[key];
      this._obj[key].onNext(value || null);
    }
  },
  dispose: function dispose() {
    var i = -1;

    while (++i < this._length) {
      var key = this._keys[i];
      this._obj[key].dispose();
    }
  }
};

},{"./yolk":92}],57:[function(require,module,exports){
(function (global){
"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = global.CustomEvent || (function () {
  var DEFAULT_PARAMS = { bubbles: false, cancelable: false, detail: undefined };

  function CustomEvent(_event, _params) {
    var params = _extends({}, DEFAULT_PARAMS, _params);
    var event = document.createEvent("CustomEvent");

    event.initCustomEvent(_event, params.bubbles, params.cancelable, params.detail);
    return event;
  }

  return CustomEvent;
})();

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],58:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _lodash = require('lodash.kebabcase');

var _lodash2 = _interopRequireDefault(_lodash);

var _EventsList = require('./EventsList');

var _EventsList2 = _interopRequireDefault(_EventsList);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var HAS_LOWER_CASE = 0x1;
var HAS_DASH_CASE = 0x2;
var IS_ATTRIBUTE = 0x4;
var USE_PROPERTY_HOOK = 0x8;
var USE_ATTRIBUTE_HOOK = 0x10;
var USE_EVENT_HOOK = 0x20;
var CAN_BE_ARRAY_OF_STRINGS = 0x80;
var HAS_BOOLEAN_VALUE = 0x100;
var IS_STAR = 0x200;

function checkMask(value, bitmask) {
  return (value & bitmask) === bitmask;
}

var attributes = {
  // inferred by virtual-dom
  className: CAN_BE_ARRAY_OF_STRINGS,
  id: null,
  style: null,

  // attributes
  accept: IS_ATTRIBUTE,
  acceptCharset: IS_ATTRIBUTE | HAS_DASH_CASE,
  accessKey: IS_ATTRIBUTE | HAS_LOWER_CASE,
  action: IS_ATTRIBUTE,
  align: IS_ATTRIBUTE,
  alt: IS_ATTRIBUTE,
  async: IS_ATTRIBUTE | HAS_BOOLEAN_VALUE,
  autoComplete: IS_ATTRIBUTE | HAS_LOWER_CASE,
  autoFocus: IS_ATTRIBUTE | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  autoPlay: IS_ATTRIBUTE | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  autoSave: IS_ATTRIBUTE | HAS_LOWER_CASE,
  bgColor: IS_ATTRIBUTE | HAS_LOWER_CASE,
  border: IS_ATTRIBUTE,
  cite: IS_ATTRIBUTE,
  color: IS_ATTRIBUTE,
  colSpan: IS_ATTRIBUTE | HAS_LOWER_CASE,
  content: IS_ATTRIBUTE,
  contentEditable: IS_ATTRIBUTE | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  coords: IS_ATTRIBUTE,
  default: IS_ATTRIBUTE | HAS_BOOLEAN_VALUE,
  defer: IS_ATTRIBUTE | HAS_BOOLEAN_VALUE,
  dir: IS_ATTRIBUTE,
  dirName: IS_ATTRIBUTE | HAS_LOWER_CASE,
  draggable: IS_ATTRIBUTE,
  dropZone: IS_ATTRIBUTE | HAS_LOWER_CASE,
  encType: IS_ATTRIBUTE | HAS_LOWER_CASE,
  for: IS_ATTRIBUTE,
  headers: IS_ATTRIBUTE,
  height: IS_ATTRIBUTE,
  href: IS_ATTRIBUTE,
  hrefLang: IS_ATTRIBUTE | HAS_LOWER_CASE,
  httpEquiv: IS_ATTRIBUTE | HAS_DASH_CASE,
  icon: IS_ATTRIBUTE,
  isMap: IS_ATTRIBUTE | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  itemProp: IS_ATTRIBUTE | HAS_LOWER_CASE,
  keyType: IS_ATTRIBUTE | HAS_LOWER_CASE,
  kind: IS_ATTRIBUTE,
  label: IS_ATTRIBUTE,
  lang: IS_ATTRIBUTE,
  max: IS_ATTRIBUTE,
  method: IS_ATTRIBUTE,
  min: IS_ATTRIBUTE,
  name: IS_ATTRIBUTE,
  noValidate: IS_ATTRIBUTE | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  open: IS_ATTRIBUTE | HAS_BOOLEAN_VALUE,
  optimum: IS_ATTRIBUTE,
  pattern: IS_ATTRIBUTE,
  ping: IS_ATTRIBUTE,
  placeholder: IS_ATTRIBUTE,
  poster: IS_ATTRIBUTE,
  preload: IS_ATTRIBUTE,
  radioGroup: IS_ATTRIBUTE | HAS_LOWER_CASE,
  rel: IS_ATTRIBUTE,
  required: IS_ATTRIBUTE | HAS_BOOLEAN_VALUE,
  reversed: IS_ATTRIBUTE | HAS_BOOLEAN_VALUE,
  role: IS_ATTRIBUTE,
  rowSpan: IS_ATTRIBUTE | HAS_LOWER_CASE,
  sandbox: IS_ATTRIBUTE,
  scope: IS_ATTRIBUTE,
  span: IS_ATTRIBUTE,
  spellCheck: IS_ATTRIBUTE | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  src: IS_ATTRIBUTE,
  srcLang: IS_ATTRIBUTE | HAS_LOWER_CASE,
  start: IS_ATTRIBUTE,
  step: IS_ATTRIBUTE,
  summary: IS_ATTRIBUTE,
  tabIndex: IS_ATTRIBUTE | HAS_LOWER_CASE,
  target: IS_ATTRIBUTE,
  title: IS_ATTRIBUTE,
  type: IS_ATTRIBUTE,
  useMap: IS_ATTRIBUTE | HAS_LOWER_CASE,
  width: IS_ATTRIBUTE,
  wrap: IS_ATTRIBUTE,

  // attributes only accessible via attribute namespace
  allowFullScreen: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  allowTransparency: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  capture: USE_ATTRIBUTE_HOOK | HAS_BOOLEAN_VALUE,
  charset: USE_ATTRIBUTE_HOOK,
  challenge: USE_ATTRIBUTE_HOOK,
  codeBase: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  cols: USE_ATTRIBUTE_HOOK,
  contextMenu: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  dateTime: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  form: USE_ATTRIBUTE_HOOK,
  formAction: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  formEncType: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  formMethod: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  formTarget: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  frameBorder: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  hidden: USE_ATTRIBUTE_HOOK | HAS_BOOLEAN_VALUE,
  inputMode: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  is: USE_ATTRIBUTE_HOOK,
  list: USE_ATTRIBUTE_HOOK,
  manifest: USE_ATTRIBUTE_HOOK,
  maxLength: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  media: USE_ATTRIBUTE_HOOK,
  minLength: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,
  rows: USE_ATTRIBUTE_HOOK,
  seamless: USE_ATTRIBUTE_HOOK | HAS_BOOLEAN_VALUE,
  size: USE_ATTRIBUTE_HOOK,
  sizes: USE_ATTRIBUTE_HOOK,
  srcSet: USE_ATTRIBUTE_HOOK | HAS_LOWER_CASE,

  // attributes only accessible via setting property in JS
  checked: USE_PROPERTY_HOOK | HAS_BOOLEAN_VALUE,
  controls: USE_PROPERTY_HOOK | HAS_BOOLEAN_VALUE,
  disabled: USE_PROPERTY_HOOK | HAS_BOOLEAN_VALUE,
  loop: USE_PROPERTY_HOOK | HAS_BOOLEAN_VALUE,
  multiple: USE_PROPERTY_HOOK | HAS_BOOLEAN_VALUE,
  readOnly: USE_PROPERTY_HOOK | HAS_LOWER_CASE | HAS_BOOLEAN_VALUE,
  selected: USE_PROPERTY_HOOK | HAS_BOOLEAN_VALUE,
  srcDoc: USE_PROPERTY_HOOK | HAS_LOWER_CASE,
  value: USE_PROPERTY_HOOK,

  // X-* attributes
  aria: IS_STAR,
  data: IS_STAR
};

// events
var length = _EventsList2.default.length;
var i = -1;

while (++i < length) {
  var event = _EventsList2.default[i];
  attributes['on' + event] = HAS_LOWER_CASE | USE_EVENT_HOOK;
}

var keys = Object.keys(attributes);
var isStandard = true;
var DOMAttributeDescriptors = {};
length = keys.length;
i = -1;

while (++i < length) {
  var key = keys[i];
  var attr = attributes[key];
  var hasLowerCase = checkMask(attr, HAS_LOWER_CASE);
  var hasDashCase = checkMask(attr, HAS_DASH_CASE);
  var isAttribute = checkMask(attr, IS_ATTRIBUTE);
  var usePropertyHook = checkMask(attr, USE_PROPERTY_HOOK);
  var useAttributeHook = checkMask(attr, USE_ATTRIBUTE_HOOK);
  var useEventHook = checkMask(attr, USE_EVENT_HOOK);
  var canBeArrayOfStrings = checkMask(attr, CAN_BE_ARRAY_OF_STRINGS);
  var hasBooleanValue = checkMask(attr, HAS_BOOLEAN_VALUE);
  var isStar = checkMask(attr, IS_STAR);
  var computed = undefined;

  if (hasLowerCase) {
    computed = key.toLowerCase();
  } else if (hasDashCase) {
    computed = (0, _lodash2.default)(key);
  }

  DOMAttributeDescriptors[key] = {
    isAttribute: isAttribute,
    isStandard: isStandard,
    usePropertyHook: usePropertyHook,
    useAttributeHook: useAttributeHook,
    useEventHook: useEventHook,
    canBeArrayOfStrings: canBeArrayOfStrings,
    hasBooleanValue: hasBooleanValue,
    isStar: isStar,
    computed: computed
  };
}

exports.default = DOMAttributeDescriptors;

},{"./EventsList":60,"lodash.kebabcase":25}],59:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _evStore = require('ev-store');

var _evStore2 = _interopRequireDefault(_evStore);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function EventHook(value) {
  this.value = value;
}

EventHook.prototype = {
  hook: function hook(node, propertyName) {
    var es = (0, _evStore2.default)(node);
    var propName = propertyName.substr(2).toLowerCase();

    es[propName] = this.value;
  },
  unhook: function unhook(node, propertyName) {
    var es = (0, _evStore2.default)(node);
    var propName = propertyName.substr(2).toLowerCase();

    es[propName] = undefined;
  }
};

exports.default = EventHook;

},{"ev-store":9}],60:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = ["Abort", "Blur", "Cancel", "CanPlay", "CanPlayThrough", "Change", "Click", "CompositionStart", "CompositionUpdate", "CompositionEnd", "ContextMenu", "Copy", "CueChange", "Cut", "DblClick", "Drag", "DragEnd", "DragExit", "DragEnter", "DragLeave", "DragOver", "DragStart", "Drop", "DurationChange", "Emptied", "Encrypted", "Ended", "Error", "Focus", "FocusIn", "FocusOut", "Input", "Invalid", "KeyDown", "KeyPress", "KeyUp", "Load", "LoadedData", "LoadedMetaData", "LoadStart", "MouseDown", "MouseEnter", "MouseLeave", "MouseMove", "MouseOut", "MouseOver", "MouseUp", "Paste", "Pause", "Play", "Playing", "Progress", "RateChange", "Reset", "Resize", "Scroll", "Search", "Seeked", "Seeking", "Select", "Show", "Stalled", "Submit", "Suspend", "TimeUpdate", "Toggle", "TouchCancel", "TouchEnd", "TouchMove", "TouchStart", "VolumeChange", "Waiting", "Wheel",

// custom
"Mount", "Unmount"];

},{}],61:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createElement = require('./createElement');

var _createElement2 = _interopRequireDefault(_createElement);

var _HTMLTags = require('./HTMLTags');

var _HTMLTags2 = _interopRequireDefault(_HTMLTags);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var boundCreateElement = function boundCreateElement(tagName) {
  return function (props) {
    for (var _len = arguments.length, children = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      children[_key - 1] = arguments[_key];
    }

    return _createElement2.default.apply(undefined, [tagName, props].concat(children));
  };
};

var helpers = {};
var length = _HTMLTags2.default.length;
var i = -1;

while (++i < length) {
  var tagName = _HTMLTags2.default[i];
  helpers[tagName] = boundCreateElement(tagName);
}

exports.default = helpers;

},{"./HTMLTags":62,"./createElement":71}],62:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = ["a", "abbr", "address", "area", "article", "aside", "audio", "b", "base", "bdi", "bdo", "blockquote", "body", "br", "button", "canvas", "caption", "cite", "code", "col", "colgroup", "data", "datalist", "dd", "del", "details", "dfn", "dialog", "div", "dl", "dt", "em", "embed", "fieldset", "figcaption", "figure", "footer", "form", "h1", "h2", "h3", "h4", "h5", "h6", "head", "header", "hgroup", "hr", "html", "i", "iframe", "img", "input", "ins", "kbd", "keygen", "label", "legend", "li", "link", "main", "map", "mark", "menu", "menuitem", "meta", "meter", "nav", "noscript", "object", "ol", "optgroup", "option", "output", "p", "param", "pre", "progress", "q", "rb", "rp", "rt", "rtc", "ruby", "s", "samp", "script", "section", "select", "small", "source", "span", "strong", "style", "sub", "summary", "sup", "table", "tbody", "td", "template", "textarea", "tfoot", "th", "thead", "time", "title", "tr", "track", "u", "ul", "var", "video", "wbr"];

},{}],63:[function(require,module,exports){
'use strict';

var _slicedToArray = (function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; })();

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.create = create;

var _transformProperties = require('./transformProperties');

var _transformProperties2 = _interopRequireDefault(_transformProperties);

var _isFunction = require('./isFunction');

var _isFunction2 = _interopRequireDefault(_isFunction);

var _flatten = require('./flatten');

var _flatten2 = _interopRequireDefault(_flatten);

var _mountable = require('./mountable');

var _parseTag = require('./parseTag');

var _parseTag2 = _interopRequireDefault(_parseTag);

var _CompositePropSubject = require('./CompositePropSubject');

var _CompositePropSubject2 = _interopRequireDefault(_CompositePropSubject);

var _YolkBaseInnerComponent = require('./YolkBaseInnerComponent');

var _YolkBaseInnerComponent2 = _interopRequireDefault(_YolkBaseInnerComponent);

var _yolk = require('./yolk');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var TAG_IS_ONLY_LETTERS = /^[a-zA-Z]*$/;

function YolkBaseComponent(tag, props, children) {
  var _props = _extends({}, props);
  var _children = children || [];

  if (_props.key) {
    this.key = _props.key.toString();
    delete _props.key;
  }

  this.name = 'YolkBaseComponent_' + tag;
  this.id = tag;
  this._props = _props;
  this._children = _children;
}

YolkBaseComponent.prototype = {
  type: 'Widget',

  init: function init() {
    this._props$ = new _CompositePropSubject2.default(this._props);
    this._children$ = new _yolk.Rx.BehaviorSubject(this._children);
    this._innerComponent = new _YolkBaseInnerComponent2.default(this.id);

    var props$ = (0, _yolk.wrapObject)(this._props$.asDistinctObservableObject(), { base: true }).map(_transformProperties2.default);
    var children$ = this._children$.flatMapLatest(function (c) {
      return (0, _yolk.wrapObject)(c, { base: true });
    }).map(_flatten2.default);
    var innerComponent = this._innerComponent;

    this._disposable = props$.combineLatest(children$).subscribe(function (_ref) {
      var _ref2 = _slicedToArray(_ref, 2);

      var props = _ref2[0];
      var children = _ref2[1];
      return innerComponent.update(props, children);
    }, function (err) {
      throw err;
    });

    return innerComponent.createVirtualNode();
  },
  postinit: function postinit(node) {
    this._innerComponent.setNode(node);
    (0, _mountable.emitMount)(node, this._props.onMount);
  },
  update: function update(previous) {
    this._props$ = previous._props$;
    this._children$ = previous._children$;
    this._disposable = previous._disposable;
    this._innerComponent = previous._innerComponent;

    this._props$.onNext(this._props);
    this._children$.onNext(this._children);
  },
  predestroy: function predestroy(node) {
    (0, _mountable.emitUnmount)(node, this._props.onUnmount);
  },
  destroy: function destroy() {
    this._disposable.dispose();

    var children = this._children;
    var length = children.length;
    var i = -1;

    while (++i < length) {
      var child = children[i];
      (0, _isFunction2.default)(child.destroy) && child.destroy();
    }
  }
};

function create(_tag, _props, children) {
  var tag = undefined;
  var props = undefined;

  if (TAG_IS_ONLY_LETTERS.test(_tag)) {
    tag = _tag;
    props = _props;
  } else {
    var parsed = (0, _parseTag2.default)(_tag);
    tag = parsed.tag;
    props = _extends({}, _props, parsed.classIds);
  }

  return new YolkBaseComponent(tag, props, children);
}

exports.default = YolkBaseComponent;

},{"./CompositePropSubject":56,"./YolkBaseInnerComponent":64,"./flatten":74,"./isFunction":80,"./mountable":84,"./parseTag":86,"./transformProperties":88,"./yolk":92}],64:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _h = require('yolk-virtual-dom/h');

var _h2 = _interopRequireDefault(_h);

var _diff = require('yolk-virtual-dom/diff');

var _diff2 = _interopRequireDefault(_diff);

var _patch = require('yolk-virtual-dom/patch');

var _patch2 = _interopRequireDefault(_patch);

var _generateUid = require('./generateUid');

var _generateUid2 = _interopRequireDefault(_generateUid);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function YolkBaseInnerComponent(tag) {
  this._tag = tag;
  this._props = {};
  this._children = [];
  this._uid = (0, _generateUid2.default)();
  this._vNode = null;
  this._node = null;
}

YolkBaseInnerComponent.prototype = {
  createVirtualNode: function createVirtualNode() {
    this._vNode = (0, _h2.default)(this._tag, this._props, this._children);
    return this._vNode;
  },
  setNode: function setNode(node) {
    this._node = node;
    this.update(this._props, this._children);
  },
  update: function update(props, children) {
    this._props = props;
    this._children = children;

    if (this._node) {
      var vNode = (0, _h2.default)(this._tag, props, children);
      var patches = (0, _diff2.default)(this._vNode, vNode);

      this._vNode = vNode;
      (0, _patch2.default)(this._node, patches);
    }
  }
};

exports.default = YolkBaseInnerComponent;

},{"./generateUid":75,"yolk-virtual-dom/diff":32,"yolk-virtual-dom/h":33,"yolk-virtual-dom/patch":35}],65:[function(require,module,exports){
'use strict';

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.create = create;

var _YolkCompositeFunctionWrapper = require('./YolkCompositeFunctionWrapper');

var _YolkCompositeFunctionWrapper2 = _interopRequireDefault(_YolkCompositeFunctionWrapper);

var _CompositePropSubject = require('./CompositePropSubject');

var _CompositePropSubject2 = _interopRequireDefault(_CompositePropSubject);

var _isFunction = require('./isFunction');

var _isFunction2 = _interopRequireDefault(_isFunction);

var _yolk = require('./yolk');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function YolkCompositeComponent(fn, props, children) {
  var _props = _extends({}, props);
  var _children = children || [];

  if (_props.key) {
    this.key = _props.key.toString();
    delete _props.key;
  }

  this.name = 'YolkCompositeComponent_' + fn.name;
  this.id = fn.name;
  this._fn = fn;
  this._props = _props;
  this._children = _children;
  this._component = null;
}

YolkCompositeComponent.prototype = {
  type: 'Widget',

  init: function init() {
    this._props$ = new _CompositePropSubject2.default(this._props);
    this._children$ = new _yolk.Rx.BehaviorSubject(this._children);

    var props$ = this._props$.asObservableObject();
    var children$ = this._children$.asObservable();

    var fn = this._fn;
    this._component = _YolkCompositeFunctionWrapper2.default.create(fn, props$, children$);

    return this._component.vNode;
  },
  update: function update(previous) {
    this._props$ = previous._props$;
    this._children$ = previous._children$;
    this._component = previous._component;

    this._props$.onNext(this._props);
    this._children$.onNext(this._children);
  },
  destroy: function destroy() {
    _YolkCompositeFunctionWrapper2.default.destroy(this._component);

    var children = this._children;
    var length = children.length;
    var i = -1;

    while (++i < length) {
      var child = children[i];
      (0, _isFunction2.default)(child.destroy) && child.destroy();
    }
  }
};

function create(fn, props, children) {
  return new YolkCompositeComponent(fn, props, children);
}

exports.default = YolkCompositeComponent;

},{"./CompositePropSubject":56,"./YolkCompositeFunctionWrapper":66,"./yolk":92,"./isFunction":80}],66:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createEventHandler = require('./createEventHandler');

var _createEventHandler2 = _interopRequireDefault(_createEventHandler);

var _isComponent = require('./isComponent');

var _isComponent2 = _interopRequireDefault(_isComponent);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function YolkCompositeFunctionWrapper(fn, props, children) {
  var eventHandlers = [];

  function createEventHandler(mapFn, init) {
    var handler = (0, _createEventHandler2.default)(mapFn, init);
    eventHandlers.push(handler);
    return handler;
  }

  this.vNode = fn.call(null, { props: props, children: children, createEventHandler: createEventHandler });
  this.eventHandlers = eventHandlers;
}

YolkCompositeFunctionWrapper.create = function (fn, props$, children$) {
  var instance = new YolkCompositeFunctionWrapper(fn, props$, children$);

  if (!(0, _isComponent2.default)(instance.vNode)) {
    throw new Error('Function did not return a valid component. See "' + fn.name + '".');
  }

  return instance;
};

YolkCompositeFunctionWrapper.destroy = function (instance) {
  var length = instance.eventHandlers.length;
  var i = -1;

  while (++i < length) {
    instance.eventHandlers[i].dispose();
  }

  instance.vNode.destroy();
};

exports.default = YolkCompositeFunctionWrapper;

},{"./createEventHandler":72,"./isComponent":77}],67:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.create = create;

var _addProperties = require('./addProperties');

var _addProperties2 = _interopRequireDefault(_addProperties);

var _YolkBaseComponent = require('./YolkBaseComponent');

var _YolkBaseComponent2 = _interopRequireDefault(_YolkBaseComponent);

var _CompositePropSubject = require('./CompositePropSubject');

var _CompositePropSubject2 = _interopRequireDefault(_CompositePropSubject);

var _yolk = require('./yolk');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function YolkCustomComponent() {}

YolkCustomComponent.prototype = {
  type: 'Widget',
  onMount: function onMount() {},
  onUpdate: function onUpdate() {},
  onUnmount: function onUnmount() {},
  _initialize: function _initialize(props, children) {
    this._props = props;
    this._props$ = null;

    switch (children.length) {
      case 1:
        this._child = children[0];
        break;
      case 0:
        this._child = new _YolkBaseComponent2.default('div');
        break;
      default:
        throw new Error(this.constructor.name + ' may not have more than one child');
    }
  },
  init: function init() {
    this._props$ = new _CompositePropSubject2.default(this._props);

    return this._child;
  },
  postinit: function postinit(node) {
    var _this = this;

    var props$ = (0, _yolk.wrapObject)(this._props$.asSubjectObject(), { base: false });
    var mountDisposable = props$.take(1).subscribe(function (props) {
      return _this.onMount(props, node);
    });
    var updateDisposable = props$.skip(1).subscribe(function (props) {
      return _this.onUpdate(props, node);
    });

    this._onDestroy = function () {
      mountDisposable.dispose();
      updateDisposable.dispose();
    };
  },
  update: function update(previous) {
    this._onDestroy = previous._onDestroy;
    this._props$ = previous._props$;
    this._props$.onNext(this._props);
  },
  predestroy: function predestroy(node) {
    this.onUnmount(node);
  },
  destroy: function destroy() {
    this._onDestroy && this._onDestroy();
  }
};

YolkCustomComponent._isCustomComponent = true;

YolkCustomComponent.extend = function extend(obj) {
  function Component() {}
  (0, _addProperties2.default)(Component, YolkCustomComponent);
  Component.prototype = Object.create(YolkCustomComponent.prototype);
  (0, _addProperties2.default)(Component.prototype, obj);

  return Component;
};

function create(Tag, props, children) {
  var instance = new Tag(props, children);
  instance._initialize(props, children);

  return instance;
}

exports.default = YolkCustomComponent;

},{"./CompositePropSubject":56,"./YolkBaseComponent":63,"./addProperties":69,"./yolk":92}],68:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.render = render;

var _createElement = require('yolk-virtual-dom/create-element');

var _createElement2 = _interopRequireDefault(_createElement);

var _diff = require('yolk-virtual-dom/diff');

var _diff2 = _interopRequireDefault(_diff);

var _patch = require('yolk-virtual-dom/patch');

var _patch2 = _interopRequireDefault(_patch);

var _initializeWidgets = require('yolk-virtual-dom/initialize-widgets');

var _initializeWidgets2 = _interopRequireDefault(_initializeWidgets);

var _delegator = require('./delegator');

var _delegator2 = _interopRequireDefault(_delegator);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var PREVIOUS_WIDGET_KEY = '__YOLK_PREVIOUS_WIDGET_KEY__';

function YolkRootComponent(child) {
  this._child = child;
}

YolkRootComponent.prototype = {
  name: 'YolkRootComponent',
  type: 'Widget',

  init: function init() {
    return this._child;
  },
  update: function update(previous, node) {
    if (this._child.key !== previous._child.key) {
      return this.init();
    }

    var patches = (0, _diff2.default)(previous._child, this._child);
    (0, _patch2.default)(node, patches);
  }
};

function render(instance, node) {
  var root = new YolkRootComponent(instance);
  var child = node.children[0];

  if (child) {
    var patches = (0, _diff2.default)(child[PREVIOUS_WIDGET_KEY], root);
    (0, _patch2.default)(child, patches);
  } else {
    child = (0, _createElement2.default)(root);
    node.appendChild(child);
    (0, _delegator2.default)(node);
    (0, _initializeWidgets2.default)(child);
  }

  child[PREVIOUS_WIDGET_KEY] = root;

  return root;
}

exports.default = YolkRootComponent;

},{"./delegator":73,"yolk-virtual-dom/create-element":31,"yolk-virtual-dom/diff":32,"yolk-virtual-dom/initialize-widgets":34,"yolk-virtual-dom/patch":35}],69:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = addProperties;
/* eslint-disable guard-for-in */

function addProperties(base) {
  for (var _len = arguments.length, objs = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    objs[_key - 1] = arguments[_key];
  }

  var length = objs.length;
  var i = -1;

  while (++i < length) {
    var obj = objs[i];

    for (var key in obj) {
      base[key] = obj[key];
    }
  }

  return base;
}

},{}],70:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = compact;

var _isDefined = require('./isDefined');

var _isDefined2 = _interopRequireDefault(_isDefined);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function compact(arr) {
  var length = arr.length;
  var newArr = [];
  var i = -1;

  while (++i < length) {
    var value = arr[i];
    if ((0, _isDefined2.default)(value) && value !== false) {
      newArr.push(value);
    }
  }

  return newArr;
}

},{"./isDefined":78}],71:[function(require,module,exports){
'use strict';

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = createElement;

var _YolkCompositeComponent = require('./YolkCompositeComponent');

var _YolkBaseComponent = require('./YolkBaseComponent');

var _YolkCustomComponent = require('./YolkCustomComponent');

var _isString = require('./isString');

var _isString2 = _interopRequireDefault(_isString);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function createElement(tag, _props) {
  var props = _extends({}, _props);

  for (var _len = arguments.length, children = Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
    children[_key - 2] = arguments[_key];
  }

  if ((0, _isString2.default)(tag)) {
    return (0, _YolkBaseComponent.create)(tag, props, children);
  }

  if (tag._isCustomComponent) {
    return (0, _YolkCustomComponent.create)(tag, props, children);
  }

  return (0, _YolkCompositeComponent.create)(tag, props, children);
}

},{"./YolkBaseComponent":63,"./YolkCompositeComponent":65,"./YolkCustomComponent":67,"./isString":83}],72:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = createEventHandler;

var _yolk = require('./yolk');

var _isDefined = require('./isDefined');

var _isDefined2 = _interopRequireDefault(_isDefined);

var _isFunction = require('./isFunction');

var _isFunction2 = _interopRequireDefault(_isFunction);

var _addProperties = require('./addProperties');

var _addProperties2 = _interopRequireDefault(_addProperties);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function createEventHandler(mapFn, init) {
  var initIsDefined = (0, _isDefined2.default)(init);
  var mapFnIsDefined = (0, _isDefined2.default)(mapFn);
  var mapFnIsFunction = (0, _isFunction2.default)(mapFn);
  var handler = undefined;

  if (mapFnIsDefined && mapFnIsFunction) {
    handler = function handlerWithFunctionMap(value) {
      handler.onNext(mapFn(value));
    };
  } else if (mapFnIsDefined) {
    handler = function handlerWithValueMap() {
      handler.onNext(mapFn);
    };
  } else {
    handler = function handlerWithoutMap(value) {
      handler.onNext(value);
    };
  }

  (0, _addProperties2.default)(handler, _yolk.Rx.ReplaySubject.prototype);
  _yolk.Rx.ReplaySubject.call(handler, 1);

  if (initIsDefined) {
    handler.onNext(init);
  }

  return handler;
}

},{"./addProperties":69,"./isDefined":78,"./isFunction":80,"./yolk":92}],73:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = delegator;

var _domDelegator = require('dom-delegator');

var _domDelegator2 = _interopRequireDefault(_domDelegator);

var _EventsList = require('./EventsList');

var _EventsList2 = _interopRequireDefault(_EventsList);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function delegator(node) {
  var instance = (0, _domDelegator2.default)(node);

  var length = _EventsList2.default.length;
  var i = -1;

  while (++i < length) {
    instance.listenTo(_EventsList2.default[i].toLowerCase());
  }

  return instance;
}

},{"./EventsList":60,"dom-delegator":6}],74:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = flatten;
function flatten(arr) {
  var length = arr.length;
  var result = [];
  var index = -1;

  while (++index < length) {
    var member = arr[index];

    if (Array.isArray(member)) {
      result = result.concat(flatten(member));
    } else {
      result.push(member);
    }
  }

  return result;
}

},{}],75:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = generateUid;
function generateUid() {
  return (Math.random() * 0x100000000000000).toString(36);
}

},{}],76:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = hasToJS;

var _isFunction = require('./isFunction');

var _isFunction2 = _interopRequireDefault(_isFunction);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function hasToJS(obj) {
  return !!obj && (0, _isFunction2.default)(obj.toJS);
}

},{"./isFunction":80}],77:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isComponent;

var _isWidget = require('yolk-virtual-dom/vnode/is-widget');

var _isWidget2 = _interopRequireDefault(_isWidget);

var _isVnode = require('yolk-virtual-dom/vnode/is-vnode');

var _isVnode2 = _interopRequireDefault(_isVnode);

var _isVtext = require('yolk-virtual-dom/vnode/is-vtext');

var _isVtext2 = _interopRequireDefault(_isVtext);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function isComponent(obj) {
  return !!obj && ((0, _isWidget2.default)(obj) || (0, _isVnode2.default)(obj) || (0, _isVtext2.default)(obj));
}

},{"yolk-virtual-dom/vnode/is-vnode":47,"yolk-virtual-dom/vnode/is-vtext":48,"yolk-virtual-dom/vnode/is-widget":49}],78:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isDefined;
function isDefined(obj) {
  return typeof obj !== "undefined" && obj !== null;
}

},{}],79:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isEmpty;
function isEmpty(obj) {
  return Object.keys(obj).length === 0;
}

},{}],80:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isFunction;
function isFunction(obj) {
  return Object.prototype.toString.call(obj) === "[object Function]";
}

},{}],81:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isNumber;
function isNumber(num) {
  return typeof num === "number" || num instanceof Number;
}

},{}],82:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isObservable;

var _yolk = require('./yolk');

function isObservable(obj) {
  return obj instanceof _yolk.Rx.Observable;
}

},{"./yolk":92}],83:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = isString;
function isString(str) {
  return typeof str === "string" || str instanceof String;
}

},{}],84:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.emitMount = emitMount;
exports.emitUnmount = emitUnmount;

var _isFunction = require('./isFunction');

var _isFunction2 = _interopRequireDefault(_isFunction);

var _CustomEvent = require('./CustomEvent');

var _CustomEvent2 = _interopRequireDefault(_CustomEvent);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function emitMount(node, fn) {
  if ((0, _isFunction2.default)(fn) && node.parentNode) {
    var event = new _CustomEvent2.default('mount');
    node.dispatchEvent(event);
  }
}

function emitUnmount(node, fn) {
  if ((0, _isFunction2.default)(fn)) {
    var event = new _CustomEvent2.default('unmount');
    node.dispatchEvent(event);
  }
}

},{"./CustomEvent":57,"./isFunction":80}],85:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = parseDOMNodeAttributes;

var _lodash = require('lodash.camelcase');

var _lodash2 = _interopRequireDefault(_lodash);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var ALL_CHARS_ARE_DIGITS_REGEX = /^\d*$/;
var DOES_NOT_LEAD_WITH_ENCLOSING_CHAR_REGEX = /^[^\{\[\"\']/;

function isSingleton(value) {
  return value === 'true' || value === 'false' || value === 'null';
}

function parseDOMNodeAttributes(attributes) {
  var attrs = {};
  var length = attributes.length;
  var i = -1;

  while (++i < length) {
    var attr = attributes[i];
    var name = (0, _lodash2.default)(attr.name);
    var value = attr.value;

    if (!ALL_CHARS_ARE_DIGITS_REGEX.test(value) && !isSingleton(value) && DOES_NOT_LEAD_WITH_ENCLOSING_CHAR_REGEX.test(value)) {
      value = '"' + value + '"';
    }

    attrs[name] = JSON.parse(value);
  }

  return attrs;
}

},{"lodash.camelcase":20}],86:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = parseTag;

var _parseTag = require('parse-tag');

var _parseTag2 = _interopRequireDefault(_parseTag);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function parseTag(_tag) {
  var classIds = {};
  var tag = (0, _parseTag2.default)(_tag, classIds).toLowerCase();
  return { tag: tag, classIds: classIds };
}

},{"parse-tag":28}],87:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = registerElement;

var _YolkRootComponent = require('./YolkRootComponent');

var _createElement = require('./createElement');

var _createElement2 = _interopRequireDefault(_createElement);

var _parseDOMNodeAttributes = require('./parseDOMNodeAttributes');

var _parseDOMNodeAttributes2 = _interopRequireDefault(_parseDOMNodeAttributes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var INSTANCE_KEY = '__YOLK_INSTANCE_KEY__';

function registerElement(name, Component) {
  var prototype = Object.create(HTMLElement.prototype);

  prototype.createdCallback = function createdCallback() {
    var attrs = (0, _parseDOMNodeAttributes2.default)(this.attributes);
    var instance = (0, _createElement2.default)(Component, attrs);

    this[INSTANCE_KEY] = instance;
  };

  prototype.attachedCallback = function attachedCallback() {
    (0, _YolkRootComponent.render)(this[INSTANCE_KEY], this);
  };

  prototype.detachedCallback = function detachedCallback() {
    this[INSTANCE_KEY].destroy();
  };

  prototype.attributeChangedCallback = function attributeChangedCallback() {
    var attrs = (0, _parseDOMNodeAttributes2.default)(this.attributes);
    var instance = (0, _createElement2.default)(Component, attrs);
    instance.update(this[INSTANCE_KEY]);
    this[INSTANCE_KEY] = instance;
  };

  document.registerElement(name, { prototype: prototype });
}

},{"./YolkRootComponent":68,"./createElement":71,"./parseDOMNodeAttributes":85}],88:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = transformProperties;

var _DOMAttributeDescriptors = require('./DOMAttributeDescriptors');

var _DOMAttributeDescriptors2 = _interopRequireDefault(_DOMAttributeDescriptors);

var _transformStyle = require('./transformStyle');

var _transformStyle2 = _interopRequireDefault(_transformStyle);

var _transformProperty = require('./transformProperty');

var _transformProperty2 = _interopRequireDefault(_transformProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function transformProperties(props) {
  var keys = Object.keys(props);
  var length = keys.length;
  var newProps = { attributes: {} };
  var i = -1;

  while (++i < length) {
    var key = keys[i];
    var value = props[key];

    if (key === 'style') {
      (0, _transformStyle2.default)(newProps, value);
    } else {
      (0, _transformProperty2.default)(newProps, key, value, _DOMAttributeDescriptors2.default[key]);
    }
  }

  return newProps;
}

},{"./DOMAttributeDescriptors":58,"./transformProperty":89,"./transformStyle":90}],89:[function(require,module,exports){
'use strict';

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = transformProperty;

var _lodash = require('lodash.kebabcase');

var _lodash2 = _interopRequireDefault(_lodash);

var _softSetHook = require('yolk-virtual-dom/virtual-hyperscript/hooks/soft-set-hook');

var _softSetHook2 = _interopRequireDefault(_softSetHook);

var _attributeHook = require('yolk-virtual-dom/virtual-hyperscript/hooks/attribute-hook');

var _attributeHook2 = _interopRequireDefault(_attributeHook);

var _EventHook = require('./EventHook');

var _EventHook2 = _interopRequireDefault(_EventHook);

var _compact = require('./compact');

var _compact2 = _interopRequireDefault(_compact);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var NULL_DESCRIPTOR = {
  isAttribute: false,
  isStandard: false,
  usePropertyHook: false,
  useEventHook: false,
  useAttributeHook: false,
  canBeArrayOfStrings: false,
  hasBooleanValue: false,
  isStar: false,
  computed: undefined
};

var STAR_DESCRIPTOR = _extends({}, NULL_DESCRIPTOR, { isAttribute: true, isStandard: true });

function transformProperty(props, key, value) {
  var descriptor = arguments.length <= 3 || arguments[3] === undefined ? NULL_DESCRIPTOR : arguments[3];

  var _value = value;
  var _key = undefined;

  if (!descriptor.isStandard) {
    return props;
  }

  if (descriptor.hasBooleanValue && _value !== true && _value !== false) {
    return props;
  }

  if (descriptor.isStar) {
    var keys = Object.keys(value);
    var length = keys.length;
    var i = -1;

    while (++i < length) {
      var __key = keys[i];
      var __value = value[__key];
      transformProperty(props, key + '-' + (0, _lodash2.default)(__key), __value, STAR_DESCRIPTOR);
    }

    return props;
  }

  _key = descriptor.computed || key;

  if (descriptor.canBeArrayOfStrings && Array.isArray(_value)) {
    _value = (0, _compact2.default)(_value).join(' ');
  }

  if (descriptor.isAttribute) {
    props.attributes[_key] = _value;
  } else if (descriptor.usePropertyHook) {
    props[_key] = new _softSetHook2.default(_value);
  } else if (descriptor.useEventHook) {
    props[_key] = new _EventHook2.default(_value);
  } else if (descriptor.useAttributeHook) {
    props[_key] = new _attributeHook2.default(null, _value);
  } else if (descriptor.isStandard) {
    props[_key] = _value;
  }

  return props;
}

},{"./EventHook":59,"./compact":70,"lodash.kebabcase":25,"yolk-virtual-dom/virtual-hyperscript/hooks/attribute-hook":43,"yolk-virtual-dom/virtual-hyperscript/hooks/soft-set-hook":44}],90:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = transformStyle;

var _isNumber = require('./isNumber');

var _isNumber2 = _interopRequireDefault(_isNumber);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var unitlessNumberProperties = {
  animationIterationCount: true,
  boxFlex: true,
  boxFlexGroup: true,
  boxOrdinalGroup: true,
  columnCount: true,
  fillOpacity: true,
  flex: true,
  flexGrow: true,
  flexPositive: true,
  flexShrink: true,
  flexNegative: true,
  flexOrder: true,
  fontWeight: true,
  lineClamp: true,
  lineHeight: true,
  opacity: true,
  order: true,
  orphans: true,
  stopOpacity: true,
  strokeDashoffset: true,
  strokeOpacity: true,
  strokeWidth: true,
  tabSize: true,
  widows: true,
  zIndex: true,
  zoom: true
};

function transformStyle(props, style) {
  if (!style) {
    return false;
  }

  var keys = Object.keys(style);
  var length = keys.length;
  var newStyle = {};

  for (var i = 0; i < length; i++) {
    var key = keys[i];
    var isUnitlessNumber = unitlessNumberProperties[key] || false;
    var value = style[key];

    if (!isUnitlessNumber && (0, _isNumber2.default)(value)) {
      value = value + 'px';
    }

    newStyle[key] = value;
  }

  props.style = newStyle;

  return props;
}

},{"./isNumber":81}],91:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = wrapObject;

var _lodash = require('lodash.isplainobject');

var _lodash2 = _interopRequireDefault(_lodash);

var _isObservable = require('./isObservable');

var _isObservable2 = _interopRequireDefault(_isObservable);

var _isEmpty = require('./isEmpty');

var _isEmpty2 = _interopRequireDefault(_isEmpty);

var _hasToJS = require('./hasToJS');

var _hasToJS2 = _interopRequireDefault(_hasToJS);

var _yolk = require('./yolk');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _typeof(obj) { return obj && typeof Symbol !== "undefined" && obj.constructor === Symbol ? "symbol" : typeof obj; }

function wrapObject(obj) {
  var opts = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

  if ((0, _isObservable2.default)(obj)) {
    return obj.flatMapLatest(function (o) {
      return wrapObject(o, opts);
    });
  } else if ((0, _hasToJS2.default)(obj)) {
    if (opts.base) {
      // only call toJS about to render base component
      return wrapObject(obj.toJS(), opts);
    }
  } else if ((0, _lodash2.default)(obj) && !(0, _isEmpty2.default)(obj)) {
    var _ret = (function () {
      var keys = Object.keys(obj);
      var length = keys.length;
      var values = Array(length);
      var index = -1;

      while (++index < length) {
        var key = keys[index];
        values[index] = wrapObject(obj[key], opts);
      }

      return {
        v: _yolk.Rx.Observable.combineLatest(values, function combineLatest() {
          var newObj = {};
          index = -1;

          while (++index < length) {
            var key = keys[index];
            newObj[key] = arguments[index];
          }

          return newObj;
        })
      };
    })();

    if ((typeof _ret === 'undefined' ? 'undefined' : _typeof(_ret)) === "object") return _ret.v;
  } else if (Array.isArray(obj) && obj.length) {
    var _obj = obj.map(function (i) {
      return wrapObject(i, opts);
    });
    return _yolk.Rx.Observable.combineLatest(_obj);
  }

  return _yolk.Rx.Observable.just(obj);
}

},{"./hasToJS":76,"./isEmpty":79,"./isObservable":82,"./yolk":92,"lodash.isplainobject":24}],92:[function(require,module,exports){
(function (global){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.Rx = exports.render = exports.wrapObject = exports.CustomComponent = exports.registerElement = exports.DOM = exports.h = undefined;

require('document-register-element');

var _rx = (typeof window !== "undefined" ? window['Rx'] : typeof global !== "undefined" ? global['Rx'] : null);

var _rx2 = _interopRequireDefault(_rx);

var _createElement = require('./createElement');

var _createElement2 = _interopRequireDefault(_createElement);

var _HTMLHelpers = require('./HTMLHelpers');

var _HTMLHelpers2 = _interopRequireDefault(_HTMLHelpers);

var _registerElement = require('./registerElement');

var _registerElement2 = _interopRequireDefault(_registerElement);

var _YolkCustomComponent = require('./YolkCustomComponent');

var _YolkCustomComponent2 = _interopRequireDefault(_YolkCustomComponent);

var _wrapObject = require('./wrapObject');

var _wrapObject2 = _interopRequireDefault(_wrapObject);

var _YolkRootComponent = require('./YolkRootComponent');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.h = _createElement2.default;
exports.DOM = _HTMLHelpers2.default;
exports.registerElement = _registerElement2.default;
exports.CustomComponent = _YolkCustomComponent2.default;
exports.wrapObject = _wrapObject2.default;
exports.render = _YolkRootComponent.render;
exports.Rx = _rx2.default;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./HTMLHelpers":61,"./YolkCustomComponent":67,"./YolkRootComponent":68,"./createElement":71,"./registerElement":87,"./wrapObject":91,"document-register-element":3}]},{},[92])(92)
});
//# sourceMappingURL=yolk.js.map