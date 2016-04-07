var stampit = require('stampit');

/**
 * Cache handling utilities. Initializes a static cache for each stamp.
 * Inheriting instances MUST provide an id member as a unique cache id.
 * Inheriting instances MAY provide a cacheable member to indicate it
 * should be cached on construction, if a cached instance is not available.
 */
var cached = stampit({
	init: function initcached({args, instance, stamp}) {
		"use strict";

		// Extend the prototype to provide a static cache
		// (As per https://github.com/stampit-org/stampit/blob/master/docs/advanced_examples.md#attach-function-to-prototype-memory-efficient-1)
		if (!stamp.fixed.methods.cache) {
			// Static (private) cache variable
			var cache = {};
			var proto = {
				cache() {
					cache[this.id] = this;
					return this;
				},
				uncache() {
					delete cache[this.id];
					return this;
				},
				fromcache() {
					return cache[this.id];
				},
				incache() {
					return this.id in cache;
				},
				getcache() {
					return cache;
				},
			};

			// Add these methods
			Object.assign(stamp.fixed.methods, proto);

			// Hide the methods added
			for (let key in proto) {
				Object.defineProperty(stamp.fixed.methods, key, {enumerable:false});
			}
		}

		// Retrieve it from the cache, or cache it, if requested
		if (instance.cacheable && instance.id != null) {
			if (instance.incache()) {
				return instance.fromcache();
			} else {
				instance.cache();
			}
		}
	},
});

module.exports = cached;
