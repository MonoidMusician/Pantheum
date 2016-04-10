"use strict";
var should = require('should');
var connection = require('./mysql');
var model = require('./model');
var ty = require('then-yield');


var parts = {
	"number": ["singular","plural"],
	"case":["nominative","accusative","ablative","dative","genitive"],
	"gender":["feminine","masculine","neuter"],
	"person":["person-1","person-2","person-3"],
	"voice":["active","passive"],
	"tense":["present","imperfect","future","perfect","pluperfect","future-perfect"]
};
var mood = {
	"indicative":{
		"voice": parts["voice"],
		"tense": parts["tense"],
		"number": parts["number"],
		"person": parts["person"],
		"gender": parts["gender"]
	},
	"subjunctive":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"imperfect",
			"perfect",
			"pluperfect"
		],
		"number": parts["number"],
		"person": parts["person"],
		"gender": parts["gender"]
	},
	"participle":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"perfect",
			"future"
		],
		"number": parts["number"],
		"gender": parts["gender"],
		"case": parts["case"]
	},
	"infinitive":{
		"voice": parts["voice"],
		"tense":[
			"present",
			"perfect",
			"future"
		],
		"number": parts["number"],
		"gender": parts["gender"],
		"case":[
			"nominative",
			"accusative"
		]
	},
	"supine":{
		"case":[
			"accusative",
			"ablative"
		]
	}
};
var verb = new model.Depath("verb", {mood});
model.Depath.add("la", "verb", verb);

describe('Depath', function() {
	var mgr = verb;
	it('should list all keys in the correct order', function() {
		mgr.all_sub_keys.should.deepEqual(["mood", "voice", "tense", "number", "person", "gender", "case"]);
		mgr.recursive_keys.should.deepEqual(["mood"]);
		mgr.simple_keys.should.be.empty;
	});
	describe('levels', function() {
		var level = mgr.level.mood;
		for (let k in level) {
			it('should have correct depaths in level of mood/'+k, function() {
				level[k].should.be.an.instanceOf(model.Depath);
				level[k].level.should.deepEqual(mood[k]);
			});
		}
	});
	describe('key2values & value2key', function() {
		var partz = Object.assign({mood:Object.keys(mood)}, parts);
		for (let k in partz) {
			let v = partz[k];
			describe(k, function() {
				it('should have values '+v.join(', '), function() {
					mgr.is_key(k).should.be.true();
					let i = 0;
					for (let n of v) {
						mgr.is_value(n).should.be.true();
						mgr.find_key(n).should.equal(k);
						mgr.value_index(n).should.equal(i++);
					}
				});
			});
		}
	});
});
describe('Path', function() {
	var mgr = verb;
	var word = model.Word({spart:'verb', lang:'la'});
	// no tag
	var p0 = model.Path({mgr,word});
	// tag as argument
	var p1 = model.Path({mgr}, '//infinitive///');
	var gat = 'present/person-1/singular/subjunctive/active';
	var tag = 'subjunctive/active/present/singular/person-1';
	// tag as object key
	var p2 = model.Path({mgr, tag:gat});
	var h0 = {}; var h1 = {}; var h2 = {};
	describe('p0 = /', function() {
		var p = p0;
		it('should reference word', function() {
			p.word.should.equal(word);
		});
		it('should have 7 slots', function() {
			p.map.should.have.lengthOf(7);
		});
		it('should be blank', function() {
			p.toString().should.equal('');
		});
		it('should be valid', function() {
			p.valid().should.be.ok();
		});
		it('should have length 0', function() {
			p.should.have.property('keylength', 0);
		});
		it('should have null/undefined value at first', function() {
			should(p.get()).be.equalOneOf([null,undefined]);
		});
		it('should remember a string value', function() {
			var string = 'hello paths!';
			p.set(string);
			p.get().should.equal(string);
			p.get(word).should.equal(string);
			p.get(p.word).should.equal(string);
			p.get(p.word.path_storage).should.equal(string);
		});
		it('should exist', function() {
			p.exists().should.be.ok();
			p.exists(word).should.be.ok();
			p.hasvalue().should.be.ok();
		});
		it('should not exist in another word', function() {
			p.exists(model.Word({spart:'verb', lang:'la'})).should.be.false();
		});
		it('should remember null/undefined values exactly (and still exist)', function() {
			var string = null;
			for (let string of [null, undefined]) {
				p.set(string);
				should(p.get()).be.exactly(string);
				p.exists().should.be.ok();
				p.hasvalue().should.not.be.ok();
			}
		});
		it('should be removeable', function() {
			var string = 'TEMPORARY';
			p.set(string);
			p.exists().should.be.ok();
			p.hasvalue().should.be.ok();
			p.get().should.be.exactly(string);

			p.remove();
			p.exists().should.not.be.ok();
			p.hasvalue().should.not.be.ok();
			should(p.get()).be.equalOneOf([null,undefined]);
			word.path_storage.should.be.empty;
		});
		it('should have valid moods (only)', function() {
			p._calculate_valid_values().should.deepEqual({mood:Object.keys(mood)});
		});
	});
	describe('p1 = infinitive/', function() {
		var p = p1;
		it('should be valid', function() {
			p.valid().should.be.ok();
		});
		it('should say infinitive', function() {
			p.toString().should.equal('infinitive');
		});
		it('should have length 1', function() {
			p.should.have.property('keylength', 1);
		});
		it('should say certain values are valid', function() {
			p._calculate_valid_values().should.deepEqual(Object.assign({
				mood: Object.keys(mood),
			}, mood[p.toString()]));
		});
	});
	describe('p2 = '+gat, function() {
		var p = p2;
		it('should be valid', function() {
			p.valid().should.be.ok();
		});
		it('should say '+tag, function() {
			p.toString().should.equal(tag);
		});
		it('should have length 5', function() {
			p.should.have.property('keylength', 5);
		});
		it('should not have property .tag', function() {
			p.should.not.have.property('tag');
		});
		it('should have map in correct order', function() {
			var map = tag.split('/');
			map.length = 7;
			p.map.should.deepEqual(map);
		});
		it('should map well', function() {
			var m = {};
			for (let k of mgr.all_sub_keys) m[k] = p.key_value(k);
			m.should.deepEqual({
				mood: 'subjunctive',
				voice: 'active',
				tense: 'present',
				number: 'singular',
				person: 'person-1',
				gender: undefined,
				case: undefined
			});
		});
		it('should say certain values are valid', function() {
			p._calculate_valid_values().should.deepEqual(Object.assign({
				mood: Object.keys(mood),
			}, mood['subjunctive']));
		});
	});
	describe('otherwise', function() {
		var p = p0;
		it('should support addp', function() {
			p.addp(p2);
			p.toString().should.equal(tag);
		});
		it('should support add.ifvalid', function() {
			p.add.ifvalid('accusative').should.not.be.ok();
			p.toString().should.equal(tag);
			p.add.ifvalid('supine').should.not.be.ok();
			p.toString().should.equal(tag);
			p.add.ifvalid('perfect').should.be.ok();
			p.toString().should.not.equal(tag);
		});
		it('should support take', function() {
			for (let k of mgr.all_sub_keys)
				p.take(k);
			p.should.have.property('keylength', 0);
		});
		it('should support take2', function() {
			p.addp(p2);
			p.should.have.property('keylength', 5);
			p.take2(mgr.all_sub_keys);
			p.should.have.property('keylength', 0);
		});
		it('should support reset', function() {
			p.addp(p2);
			p.should.have.property('keylength', 5);
			p.reset();
			p.should.have.property('keylength', 0);
		});
		it('should normalize '+gat+' to '+tag, function() {
			model.Path.normalize(mgr, gat).should.equal(tag);
		})
	});
});


