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
	var p0 = model.Path({mgr, word});
	// tag as argument
	var p1 = model.Path({mgr}, '//infinitive///');
	var gat = 'present/person-1/singular/subjunctive/active';
	var tag = 'subjunctive/active/present/singular/person-1';
	// tag as object key
	var p2 = model.Path({mgr, tag:gat});
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
		it('should have valid moods (only)', function() {
			p.values().should.deepEqual({mood:Object.keys(mood)});
		});
		it('should have ord=0', function() {
			p.ord().should.be.exactly(0);
		});
		it('should have ordlen=mgr.length', function() {
			//p.ord(0, false).should.be.exactly(mgr.length);
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
			p.values().should.deepEqual(Object.assign({
				mood: Object.keys(mood),
			}, mood[p.toString()]));
		});
		it('should have ord=2593', function() {
			p.ord().should.be.exactly(2593);
		});
		it('should have ordlen=mgr.length', function() {
			p.ord(0, false).should.be.exactly(mgr.length);
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
			p.values().should.deepEqual(Object.assign({
				mood: Object.keys(mood),
			}, mood['subjunctive']));
		});
		it('should have ord=1317', function() {
			p.ord().should.be.exactly(1317);
		});
		it('should have ordlen=mgr.length', function() {
			p.ord(0, false).should.be.exactly(mgr.length);
		});
	});
	describe('api', function() {
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
	for (let flat of [true,false/**/]) {
		describe('FLAT_STORAGE = '+flat, function() {
			model.Path.FLAT_STORAGE = flat;
			it('set', function() {
				model.Path.FLAT_STORAGE = flat;
				model.Path.FLAT_STORAGE.should.be.exactly(flat);
			});
			describe('getting/setting/deleting', function() {
				var ps = [p0, p1, p2];
				for (let i in ps) (function(){
					var pi = ps[i];
					describe('p'+i+' = '+(pi||'/'), function() {
						var h, p=pi;
						it('should have null/undefined value at first', function() {
							h = {};
							should(p.get(h)).be.equalOneOf([null,undefined]);
						});
						it('should remember a string value', function() {
							var string = 'hello paths!';
							p.set(string, h);
							p.get(h).should.equal(string);
						});
						it('should exist', function() {
							p.exists(h).should.be.ok();
							p.hasvalue(h).should.be.ok();
						});
						it('should not exist in another word', function() {
							p.exists(model.Word({spart:'verb', lang:'la'})).should.be.false();
						});
						it('should remember null/undefined values exactly (and still exist)', function() {
							var string = null;
							for (let string of [null, undefined]) {
								p.set(string, h);
								should(p.get(h)).be.exactly(string);
								p.exists(h).should.be.ok();
								p.hasvalue(h).should.not.be.ok();
							}
						});
						it('should be removeable', function() {
							var string = 'TEMPORARY';
							p.set(string, h);
							p.exists(h).should.be.ok();
							p.hasvalue(h).should.be.ok();
							p.get(h).should.be.exactly(string);

							p.remove(h);
							p.exists(h).should.not.be.ok();
							p.hasvalue(h).should.not.be.ok();
							should(p.get(h)).be.equalOneOf([null,undefined]);
							h.should.be.empty();
						});
						if (!i) it('should store in its word', function() {
							var string = 'hello paths!';
							p.set(string);
							p.get().should.equal(string);
							p.get(word).should.equal(string);
							p.get(p.word).should.equal(string);
							p.get(p.word.path_storage).should.equal(string);

							p.remove();
							p.exists().should.not.be.ok();
							p.hasvalue().should.not.be.ok();
							should(p.get()).be.equalOneOf([null,undefined]);
							word.path_storage.should.be.empty();
						})
					});
				}());
			});
			describe('iteration', function() {
				var hash = {};
				var data = {
					'indicative/active/present/singular/person-1': 'sum',
					'indicative/active/present/singular/person-2': 'es',
					'indicative/active/present/singular/person-3': 'est',
					'indicative/active/present/plural/person-1': 'sumus',
					'indicative/active/present/plural/person-2': 'estis',
					'indicative/active/present/plural/person-3': 'sunt',
					'indicative/active/perfect/singular/person-1': 'fuī',
					'indicative/active/perfect/singular/person-2': 'fuistī',
					'indicative/active/perfect/singular/person-3': 'fuit',
					'indicative/active/perfect/plural/person-1': 'fuimus',
					'indicative/active/perfect/plural/person-2': 'fuistis',
					'indicative/active/perfect/plural/person-3': 'fuērunt',
					'indicative/active/pluperfect/singular/person-1': 'fueram',
					'indicative/active/pluperfect/singular/person-2': 'fuerās',
					'indicative/active/pluperfect/singular/person-3': 'fuerat',
					'indicative/active/pluperfect/plural/person-1': 'fuerāmus',
					'indicative/active/pluperfect/plural/person-2': 'fuerātis',
					'indicative/active/pluperfect/plural/person-3': 'fuerant',
					'indicative/active/future-perfect/singular/person-1': 'fuerō',
					'indicative/active/future-perfect/singular/person-2': 'fueris',
					'indicative/active/future-perfect/singular/person-3': 'fuerit',
					'indicative/active/future-perfect/plural/person-1': 'fuerimus',
					'indicative/active/future-perfect/plural/person-2': 'fueritis',
					'indicative/active/future-perfect/plural/person-3': 'fuerint',
					'subjunctive/active/perfect/singular/person-1': 'fuerim',
					'subjunctive/active/perfect/singular/person-2': 'fuerīs',
					'subjunctive/active/perfect/singular/person-3': 'fuerit',
					'subjunctive/active/perfect/plural/person-1': 'fuerīmus',
					'subjunctive/active/perfect/plural/person-2': 'fuerītis',
					'subjunctive/active/perfect/plural/person-3': 'fuerint',
					'subjunctive/active/pluperfect/singular/person-1': 'fuissem',
					'subjunctive/active/pluperfect/singular/person-2': 'fuissēs',
					'subjunctive/active/pluperfect/singular/person-3': 'fuisset',
					'subjunctive/active/pluperfect/plural/person-1': 'fuissēmus',
					'subjunctive/active/pluperfect/plural/person-2': 'fuissētis',
					'subjunctive/active/pluperfect/plural/person-3': 'fuissent',
					'infinitive/active/perfect': 'fuisse',
					'participle/active/future': 'futūrus‣',
					'infinitive/active/future': 'futūrus‣ esse',
					'participle/active/future/singular/feminine/nominative': 'futūra',
					'participle/active/future/singular/feminine/ablative': 'futūrā',
					'participle/active/future/singular/feminine/dative': 'futūræ',
					'participle/active/future/singular/feminine/genitive': 'futūræ',
					'participle/active/future/plural/feminine/nominative': 'futūræ',
					'participle/active/future/plural/neuter/nominative': 'futūra',
					'participle/active/future/plural/neuter/accusative': 'futūra',
					'participle/active/future/singular/feminine/accusative': 'futūram',
					'participle/active/future/singular/masculine/nominative': 'futūrus',
					'participle/active/future/singular/masculine/accusative': 'futūrum',
					'participle/active/future/singular/masculine/ablative': 'futūrō',
					'participle/active/future/singular/masculine/dative': 'futūrō',
					'participle/active/future/singular/masculine/genitive': 'futūrī',
					'participle/active/future/singular/neuter/genitive': 'futūrī',
					'participle/active/future/singular/neuter/nominative': 'futūrum',
					'participle/active/future/singular/neuter/accusative': 'futūrum',
					'participle/active/future/singular/neuter/ablative': 'futūrō',
					'participle/active/future/singular/neuter/dative': 'futūrō',
					'participle/active/future/plural/feminine/accusative': 'futūrās',
					'participle/active/future/plural/feminine/genitive': 'futūrārum',
					'participle/active/future/plural/masculine/genitive': 'futūrōrum',
					'participle/active/future/plural/neuter/genitive': 'futūrōrum',
					'participle/active/future/plural/masculine/accusative': 'futūrōs',
					'participle/active/future/plural/masculine/nominative': 'futūrī',
					'participle/active/future/plural/masculine/ablative': 'futūrīs',
					'participle/active/future/plural/masculine/dative': 'futūrīs',
					'participle/active/future/plural/feminine/dative': 'futūrīs',
					'participle/active/future/plural/feminine/ablative': 'futūrīs',
					'participle/active/future/plural/neuter/ablative': 'futūrīs',
					'participle/active/future/plural/neuter/dative': 'futūrīs',
					'infinitive/active/future/singular/feminine/nominative': 'futūra esse',
					'infinitive/active/future/singular/feminine/accusative': 'futūram esse',
					'infinitive/active/future/singular/masculine/accusative': 'futūrum esse',
					'infinitive/active/future/singular/neuter/accusative': 'futūrum esse',
					'infinitive/active/future/singular/neuter/nominative': 'futūrum esse',
					'infinitive/active/future/singular/masculine/nominative': 'futūrus esse',
					'infinitive/active/future/plural/feminine/nominative': 'futūræ esse',
					'infinitive/active/future/plural/feminine/accusative': 'futūrās esse',
					'infinitive/active/future/plural/masculine/accusative': 'futūrōs esse',
					'infinitive/active/future/plural/masculine/nominative': 'futūrī esse',
					'infinitive/active/future/plural/neuter/nominative': 'futūra esse',
					'infinitive/active/future/plural/neuter/accusative': 'futūra esse',
					'infinitive/active/present': 'esse',
					'subjunctive/active/present/singular/person-1': 'sim',
					'subjunctive/active/present/singular/person-2': 'sīs',
					'subjunctive/active/present/singular/person-3': 'sit',
					'subjunctive/active/present/plural/person-1': 'sīmus',
					'subjunctive/active/present/plural/person-2': 'sītis',
					'subjunctive/active/present/plural/person-3': 'sint',
					'indicative/active/imperfect/singular/person-1': 'eram',
					'indicative/active/imperfect/singular/person-3': 'erat',
					'indicative/active/imperfect/plural/person-3': 'erant',
					'indicative/active/imperfect/plural/person-1': 'erāmus',
					'indicative/active/imperfect/plural/person-2': 'erātis',
					'indicative/active/imperfect/singular/person-2': 'erās',
					'indicative/active/future/singular/person-1': 'erō',
					'indicative/active/future/singular/person-2': 'eris',
					'indicative/active/future/singular/person-3': 'erit',
					'indicative/active/future/plural/person-2': 'eritis',
					'indicative/active/future/plural/person-1': 'erimus',
					'indicative/active/future/plural/person-3': 'erunt',
					'subjunctive/active/imperfect/singular/person-1': 'essem',
					'subjunctive/active/imperfect/singular/person-3': 'esset',
					'subjunctive/active/imperfect/plural/person-3': 'essent',
					'subjunctive/active/imperfect/singular/person-2': 'essēs',
					'subjunctive/active/imperfect/plural/person-2': 'essētis',
					'subjunctive/active/imperfect/plural/person-1': 'essēmus'
				};
				var Paths = [], Ordered = [];
				Ordered.length = mgr.length;
				for (let tag in data) {
					var p = model.Path({mgr, tag})
					Paths.push(p);
					p.set(data[tag], hash);
				}
				for (let p of Paths) Ordered[p.ord()] = p.tag;
				if (flat) it('hash should equal data', function() {
					hash.should.deepEqual(data);
				});
				var paths = {
					'subjunctive/': {
						mood: [ 'subjunctive' ],
						voice: [ 'active' ],
						tense: [ 'present', 'imperfect', 'perfect', 'pluperfect' ],
						number: [ 'singular', 'plural' ],
						person: [ 'person-1', 'person-2', 'person-3' ],
						gender: [],
						case: undefined
					},
					'infinitive/': {
						mood: [ 'infinitive' ],
						voice: [ 'active' ],
						tense: [ 'present', 'perfect', 'future' ],
						number: [ 'singular', 'plural' ],
						person: undefined,
						gender: [ 'feminine', 'masculine', 'neuter' ],
						case: [ 'nominative', 'accusative' ]
					}
				};
				paths['infinitive/active/'] = paths['infinitive/'];
				for (let tag in paths) {
					it('should iterate correctly under '+tag, function() {
						var p = model.Path({mgr}, tag);
						var res = {};
						for (let k of mgr.all_sub_keys) {
							res[k] = p.iterate(k, hash);
						}
						res.should.deepEqual(paths[tag]);
					});
				}
			});
		});
	}
	describe('ordinality', function() {
		var mgr = new model.Depath('test', {
			type: {
				literal: {
					case: {
						upper: {value:['A','B']},
						lower: {value:['a','b']},
					}
				},
				numeral: {
					value:['0','1'],
				},
			}
		});
		var paths = [
			'',
			'literal',
				'literal/upper',
					'literal/upper/A',
					'literal/upper/B',
				'literal/lower',
					'literal/lower/a',
					'literal/lower/b',
			'numeral',
				'numeral/0',
				'numeral/1',
		];
		it('should have '+paths.length+' possibilities', function() {
			mgr.length.should.equal(paths.length);
		});
		it('should have corresponding ordinalities in order', function() {
			var p = model.Path({mgr});
			for (let i in paths) {
				p.reset().add2(paths[i]).ord().should.equal(+i);
			}
		});
	});
	describe('deal with changing order', function() {
		var mgr = new model.Depath('test', {
			type: {
				numeral: {
					value:['0','1'],
				},
				literal: {
					case: {
						upper: {value:['A','B']},
						lower: {value:['a','b']},
					}
				},
			}
		});
		var paths = [
			'',
			'numeral',
				'numeral/0',
				'numeral/1',
			'literal',
				'literal/upper',
					'literal/upper/A',
					'literal/upper/B',
				'literal/lower',
					'literal/lower/a',
					'literal/lower/b',
		];
		it('should have '+paths.length+' possibilities', function() {
			mgr.length.should.equal(paths.length);
		});
		it('should have corresponding ordinalities in order', function() {
			var p = model.Path({mgr});
			for (let i in paths) {
				var ord = p.reset().add2(paths[i]).ord();
				ord.should.equal(+i);
			}
		});
	});
});
