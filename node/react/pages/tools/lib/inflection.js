module.exports.adj12 = function(b) {
	return {
		'singular': {
			'nominative': {
				'masculine': b+'us',
				'feminine':  b+'a',
				'neuter':    b+'um'
			},
			'accusative': {
				'masculine': b+'um',
				'feminine':  b+'am',
				'neuter':    b+'um'
			},
			'ablative': {
				'masculine': b+'ō',
				'feminine':  b+'ā',
				'neuter':    b+'ō'
			},
			'dative': {
				'masculine': b+'ō',
				'feminine':  b+'æ',
				'neuter':    b+'ō'
			},
			'genitive': {
				'masculine': b+'ī',
				'feminine':  b+'æ',
				'neuter':    b+'ī'
			},
			'vocative': {
				'masculine': b+'e',
				'feminine':  b+'a',
				'neuter':    b+'um'
			}
		},
		'plural': {
			'nominative': {
				'masculine': b+'ī',
				'feminine':  b+'æ',
				'neuter':    b+'a'
			},
			'accusative': {
				'masculine': b+'ōs',
				'feminine':  b+'ās',
				'neuter':    b+'a'
			},
			'ablative': {
				'masculine': b+'īs',
				'feminine':  b+'īs',
				'neuter':    b+'īs'
			},
			'dative': {
				'masculine': b+'īs',
				'feminine':  b+'īs',
				'neuter':    b+'īs'
			},
			'genitive': {
				'masculine': b+'ōrum',
				'feminine':  b+'ārum',
				'neuter':    b+'ōrum'
			},
			'vocative': {
				'masculine': b+'ī',
				'feminine':  b+'æ',
				'neuter':    b+'a'
			}
		}
	}
};
module.exports.adj3 = function(b) {
	return {
		'singular': {
			'nominative': {
				'masculine': b+'is',
				'feminine':  b+'is',
				'neuter':    b+'e'
			},
			'accusative': {
				'masculine': b+'em',
				'feminine':  b+'em',
				'neuter':    b+'e'
			},
			'ablative': {
				'masculine': b+'ī',
				'feminine':  b+'ī',
				'neuter':    b+'ī'
			},
			'dative': {
				'masculine': b+'ī',
				'feminine':  b+'ī',
				'neuter':    b+'ī'
			},
			'genitive': {
				'masculine': b+'isī',
				'feminine':  b+'is',
				'neuter':    b+'is'
			},
			'vocative': {
				'masculine': b+'is',
				'feminine':  b+'is',
				'neuter':    b+'e'
			}
		},
		'plural': {
			'nominative': {
				'masculine': b+'ēs',
				'feminine':  b+'ēs',
				'neuter':    b+'ēia'
			},
			'accusative': {
				'masculine': b+'ēs',
				'feminine':  b+'ēs',
				'neuter':    b+'ia'
			},
			'ablative': {
				'masculine': b+'ibus',
				'feminine':  b+'ibus',
				'neuter':    b+'ibus'
			},
			'dative': {
				'masculine': b+'ibus',
				'feminine':  b+'ibus',
				'neuter':    b+'ibus'
			},
			'genitive': {
				'masculine': b+'iu',
				'feminine':  b+'ium',
				'neuter':    b+'ium'
			},
			'vocative': {
				'masculine': b+'ēs',
				'feminine':  b+'ēs',
				'neuter':    b+'ia'
			}
		}
	}
};
module.exports.modify = function(forms, list) {
	for (let l of list) {
		var f = forms, v = l.pop(), i;
		while (l.length > 1)
			f = f[l.pop()];
		i = l.pop();
		if (typeof f[i] !== 'object')
			f[i] = v;
		else {
			for (let k in f[i]) {
				f[i][k] = v;
			}
		}
	}
	return forms;
};
