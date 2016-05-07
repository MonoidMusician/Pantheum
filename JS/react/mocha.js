"use strict";
var should = require('should');
var expand = require('./expand');



describe('basic functionality', function() {
	var test = {
		data:{
			a: 1,
			b: 2,
			c: 3,
		},
		'data-d': 4,
		style: {
			margin: ' 1 2  3 5',
			marginLeft: '4',
		}
	};
	var testthis = {
		tru: true
	};
	for (let live of ['', '.live']) {
		for (let make of ['.make', ' (new hash)', '']) {
			describe('expand'+live+(make), function() {
				var result, style, c;
				if (make === '.make') {
					if (live) c = expand.live.make;
					else c = expand.make;
				} else {
					if (live) c = expand.live;
					else c = expand;
				}
				it('should process objects', function() {
					var style = c({style:{
						margin: {bottomright:'none',topleft:'2'},
					}}).style;
					style.marginBottom.should.equal('none');
					style.marginRight.should.equal('none');
					style.marginTop.should.equal('2');
					style.marginLeft.should.equal('2');
				});
				it('should process arrays', function() {
					c({style:{
						marginLeft: ['4', '2'],
					}}).style.marginLeft.should.equal('2');
				});
				it('should keep this context', function() {
					c.call(testthis, {style:{
						marginLeft: function() {
							this.should.be.exactly(testthis);
							return this.tru ? '2' : '4';
						},
					}}).style.marginLeft.should.equal('2');
				});
				it('should '+(make==='.make'?'not ':'')+'return the first argument', function() {
					result = {};
					if (make === '.make') {
						result = c(test);
						result.should.not.equal(test);
					} else if (!make) {
						Object.assign(result, test);
						result.style = Object.assign({}, test.style);
						c(result).should.be.exactly(result);
					} else {
						c(result, test).should.be.exactly(result);
					}
				});
				describe('data', function() {
					if (live) {
						it('should have data = {a:1,b:2,c:3,d:4}', function() {
							result.data.should.deepEqual({a:1,b:2,c:3,d:4});
						});
					} else {
						it('should not have a data attribute', function() {
							result.should.not.have.property('data');
						});
					}
					it('should keep attribute data-d', function() {
						result['data-d'].should.be.exactly(test['data-d']);
					});
					it('should have attributes data-a, data-b, data-c', function() {
						result['data-a'].should.be.exactly(test.data.a);
						result['data-b'].should.be.exactly(test.data.b);
						result['data-c'].should.be.exactly(test.data.c);
					});
				});
				describe('style', function() {
					var style;
					it('should exist', function() {
						(style = result.style).should.be.ok();
					});
					describe('margin', function() {
						var margin;
						if (live) {
							it('should equal {top: 1, right: 2, bottom: 3, left: 4}', function() {
								(margin = style.margin).should.deepEqual({
									top: '1', right: '2', bottom: '3', left: '4'
								});
							});
							it('should be iterable as [1,2,3,4]', function() {
								[...margin].should.deepEqual(['1','2','3','4']);
							});
						} else {
							it('should not exist as a key', function() {
								style.should.not.have.property('margin');
							})
						}
						it('should keep attribute marginLeft', function() {
							style.marginLeft.should.equal('4');
						});
						it('should have attributes for the other edges', function() {
							style.marginTop.should.equal('1');
							style.marginRight.should.equal('2');
							style.marginBottom.should.equal('3');
						});
					});
				});
			});
		}
	}
});

describe('data manipulation', function() {
	it('should process regular functions', function() {
		expand.live({style:{
			margin: '4',
		}}, {style:{
			margin: function(margin, style, property) {
				margin.should.equal('4');
				style.margin.should.equal(margin);
				property.should.equal('margin');
				return margin/2;
			}
		}}).style.margin.toString().should.equal('2');
	});
	it('should process generator functions', function() {
		expand.live({style:{
			margin: '4',
		}}, {style:{
			margin: function*(margin, style, property) {
				margin.should.equal('4');
				style.margin.should.equal(margin);
				property.should.equal('margin');
				var m1 = yield '5';
				var m2 = yield 'unset';
				m1.should.equal('5');
				m2.should.equal('0');
				return '2';
			}
		}}).style.margin.toString().should.equal('2');
	});
});


