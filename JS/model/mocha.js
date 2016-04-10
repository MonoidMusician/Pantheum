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
for (let flat of [true,false]) {
	describe('Path (FLAT_STORAGE = '+flat+')', function() {
		model.Path.FLAT_STORAGE = flat;
		it('set', function() {
			model.Path.FLAT_STORAGE = flat;
			model.Path.FLAT_STORAGE.should.be.exactly(flat);
		});
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
		describe('iteration', function() {
			var hash = {};
			var data = `
				[7846] (indicative/active/present/singular/person-1)sum Delete
				[7847] (indicative/active/present/singular/person-2)es Delete
				[7848] (indicative/active/present/singular/person-3)est Delete
				[7849] (indicative/active/present/plural/person-1)sumus Delete
				[7850] (indicative/active/present/plural/person-2)estis Delete
				[7851] (indicative/active/present/plural/person-3)sunt Delete
				[7852] (indicative/active/perfect/singular/person-1)fuī Delete
				[7853] (indicative/active/perfect/singular/person-2)fuistī Delete
				[7854] (indicative/active/perfect/singular/person-3)fuit Delete
				[7855] (indicative/active/perfect/plural/person-1)fuimus Delete
				[7856] (indicative/active/perfect/plural/person-2)fuistis Delete
				[7857] (indicative/active/perfect/plural/person-3)fuērunt Delete
				[7858] (indicative/active/pluperfect/singular/person-1)fueram Delete
				[7859] (indicative/active/pluperfect/singular/person-2)fuerās Delete
				[7860] (indicative/active/pluperfect/singular/person-3)fuerat Delete
				[7861] (indicative/active/pluperfect/plural/person-1)fuerāmus Delete
				[7862] (indicative/active/pluperfect/plural/person-2)fuerātis Delete
				[7863] (indicative/active/pluperfect/plural/person-3)fuerant Delete
				[7864] (indicative/active/future-perfect/singular/person-1)fuerō Delete
				[7865] (indicative/active/future-perfect/singular/person-2)fueris Delete
				[7866] (indicative/active/future-perfect/singular/person-3)fuerit Delete
				[7867] (indicative/active/future-perfect/plural/person-1)fuerimus Delete
				[7868] (indicative/active/future-perfect/plural/person-2)fueritis Delete
				[7869] (indicative/active/future-perfect/plural/person-3)fuerint Delete
				[7870] (subjunctive/active/perfect/singular/person-1)fuerim Delete
				[7871] (subjunctive/active/perfect/singular/person-2)fuerīs Delete
				[7872] (subjunctive/active/perfect/singular/person-3)fuerit Delete
				[7873] (subjunctive/active/perfect/plural/person-1)fuerīmus Delete
				[7874] (subjunctive/active/perfect/plural/person-2)fuerītis Delete
				[7875] (subjunctive/active/perfect/plural/person-3)fuerint Delete
				[7876] (subjunctive/active/pluperfect/singular/person-1)fuissem Delete
				[7877] (subjunctive/active/pluperfect/singular/person-2)fuissēs Delete
				[7878] (subjunctive/active/pluperfect/singular/person-3)fuisset Delete
				[7879] (subjunctive/active/pluperfect/plural/person-1)fuissēmus Delete
				[7880] (subjunctive/active/pluperfect/plural/person-2)fuissētis Delete
				[7881] (subjunctive/active/pluperfect/plural/person-3)fuissent Delete
				[7882] (infinitive/active/perfect)fuisse Delete
				[7883] (participle/active/future)futūrus‣ Delete
				[7884] (infinitive/active/future)futūrus‣ esse Delete
				[7885] (participle/active/future/singular/feminine/nominative)futūra Delete
				[7886] (participle/active/future/singular/feminine/vocative)futūra Delete
				[7887] (participle/active/future/singular/feminine/ablative)futūrā Delete
				[7888] (participle/active/future/singular/feminine/dative)futūræ Delete
				[7889] (participle/active/future/singular/feminine/genitive)futūræ Delete
				[7890] (participle/active/future/plural/feminine/nominative)futūræ Delete
				[7891] (participle/active/future/plural/feminine/vocative)futūræ Delete
				[7892] (participle/active/future/plural/neuter/nominative)futūra Delete
				[7893] (participle/active/future/plural/neuter/vocative)futūra Delete
				[7894] (participle/active/future/plural/neuter/accusative)futūra Delete
				[7895] (participle/active/future/singular/feminine/accusative)futūram Delete
				[7896] (participle/active/future/singular/masculine/nominative)futūrus Delete
				[7897] (participle/active/future/singular/masculine/vocative)futūre Delete
				[7898] (participle/active/future/singular/masculine/accusative)futūrum Delete
				[7899] (participle/active/future/singular/masculine/ablative)futūrō Delete
				[7900] (participle/active/future/singular/masculine/dative)futūrō Delete
				[7901] (participle/active/future/singular/masculine/genitive)futūrī Delete
				[7902] (participle/active/future/singular/neuter/genitive)futūrī Delete
				[7903] (participle/active/future/singular/neuter/nominative)futūrum Delete
				[7904] (participle/active/future/singular/neuter/accusative)futūrum Delete
				[7905] (participle/active/future/singular/neuter/vocative)futūrum Delete
				[7906] (participle/active/future/singular/neuter/ablative)futūrō Delete
				[7907] (participle/active/future/singular/neuter/dative)futūrō Delete
				[7908] (participle/active/future/plural/feminine/accusative)futūrās Delete
				[7909] (participle/active/future/plural/feminine/genitive)futūrārum Delete
				[7910] (participle/active/future/plural/masculine/genitive)futūrōrum Delete
				[7911] (participle/active/future/plural/neuter/genitive)futūrōrum Delete
				[7912] (participle/active/future/plural/masculine/accusative)futūrōs Delete
				[7913] (participle/active/future/plural/masculine/nominative)futūrī Delete
				[7914] (participle/active/future/plural/masculine/vocative)futūrī Delete
				[7915] (participle/active/future/plural/masculine/ablative)futūrīs Delete
				[7916] (participle/active/future/plural/masculine/dative)futūrīs Delete
				[7917] (participle/active/future/plural/feminine/dative)futūrīs Delete
				[7918] (participle/active/future/plural/feminine/ablative)futūrīs Delete
				[7919] (participle/active/future/plural/neuter/ablative)futūrīs Delete
				[7920] (participle/active/future/plural/neuter/dative)futūrīs Delete
				[7921] (infinitive/active/future/singular/feminine/nominative)futūra esse Delete
				[7922] (infinitive/active/future/singular/feminine/accusative)futūram esse Delete
				[7923] (infinitive/active/future/singular/masculine/accusative)futūrum esse Delete
				[7924] (infinitive/active/future/singular/neuter/accusative)futūrum esse Delete
				[7925] (infinitive/active/future/singular/neuter/nominative)futūrum esse Delete
				[7926] (infinitive/active/future/singular/masculine/nominative)futūrus esse Delete
				[7927] (infinitive/active/future/plural/feminine/nominative)futūræ esse Delete
				[7928] (infinitive/active/future/plural/feminine/accusative)futūrās esse Delete
				[7929] (infinitive/active/future/plural/masculine/accusative)futūrōs esse Delete
				[7930] (infinitive/active/future/plural/masculine/nominative)futūrī esse Delete
				[7931] (infinitive/active/future/plural/neuter/nominative)futūra esse Delete
				[7932] (infinitive/active/future/plural/neuter/accusative)futūra esse Delete
				[7933] (infinitive/active/present)esse Delete
				[7934] (subjunctive/active/present/singular/person-1)sim Delete
				[7935] (subjunctive/active/present/singular/person-2)sīs Delete
				[7936] (subjunctive/active/present/singular/person-3)sit Delete
				[7937] (subjunctive/active/present/plural/person-1)sīmus Delete
				[7938] (subjunctive/active/present/plural/person-2)sītis Delete
				[7939] (subjunctive/active/present/plural/person-3)sint Delete
				[7940] (indicative/active/imperfect/singular/person-1)eram Delete
				[7941] (indicative/active/imperfect/singular/person-3)erat Delete
				[7942] (indicative/active/imperfect/plural/person-3)erant Delete
				[7943] (indicative/active/imperfect/plural/person-1)erāmus Delete
				[7944] (indicative/active/imperfect/plural/person-2)erātis Delete
				[7945] (indicative/active/imperfect/singular/person-2)erās Delete
				[7946] (imperative/active/present/singular/person-2)es Delete
				[7947] (imperative/active/present/plural/person-2)este Delete
				[7948] (imperative/active/future/singular/person-2)estō Delete
				[7949] (imperative/active/future/singular/person-3)estō Delete
				[7950] (imperative/active/future/plural/person-2)estōte Delete
				[7951] (imperative/active/future/plural/person-3)suntō Delete
				[7952] (indicative/active/future/singular/person-1)erō Delete
				[7953] (indicative/active/future/singular/person-2)eris Delete
				[7954] (indicative/active/future/singular/person-3)erit Delete
				[7955] (indicative/active/future/plural/person-2)eritis Delete
				[7956] (indicative/active/future/plural/person-1)erimus Delete
				[7957] (indicative/active/future/plural/person-3)erunt Delete
				[7958] (subjunctive/active/imperfect/singular/person-1)essem Delete
				[7959] (subjunctive/active/imperfect/singular/person-3)esset Delete
				[7960] (subjunctive/active/imperfect/plural/person-3)essent Delete
				[7961] (subjunctive/active/imperfect/singular/person-2)essēs Delete
				[7962] (subjunctive/active/imperfect/plural/person-2)essētis Delete
				[7963] (subjunctive/active/imperfect/plural/person-1)essēmus Delete
			`.trim().split('\n');
			for (let [tag, value] of data.map(a => (/\((.+?)\)(.+?)\s*Delete/g).exec(a).slice(1)))
				try {model.Path({mgr, tag}).set(value, hash);} catch(e) {}
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
					var p = new model.Path({mgr}, tag);
					var res = {};
					for (let k of mgr.all_sub_keys) {
						res[k] = p.iterate(k, hash);
					}
					res.should.deepEqual(paths[tag]);
				});
			}
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
}

