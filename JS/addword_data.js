var changes = {
    "impersonal": {
        "conj-1": {
            "ending0": "at",
            "ending2": "it"
        },
        "conj-2": {
            "ending0": "et",
            "ending2": "it"
        },
        "conj-3": {
            "ending0": "it",
            "ending2": "it"
        },
        "conj-3-io": {
            "ending0": "it",
            "ending2": "it"
        },
        "conj-4": {
            "ending0": "it",
            "ending2": "it"
        },
        "conj-1-deponent": {
            "ending0": "ātur",
            "ending2": "us est"
        },
        "conj-2-deponent": {
            "ending0": "ētur",
            "ending2": "us est"
        },
        "conj-3-deponent": {
            "ending0": "itur",
            "ending2": "us est"
        },
        "conj-3-io-deponent": {
            "ending0": "itur",
            "ending2": "us est"
        },
        "conj-4-deponent": {
            "ending0": "ītur",
            "ending2": "us est"
        }
    },
    "personal": {
        "conj-1": {
            "ending0": "ō",
            "ending2": "ī"
        },
        "conj-2": {
            "ending0": "eō",
            "ending2": "ī"
        },
        "conj-3": {
            "ending0": "ō",
            "ending2": "ī"
        },
        "conj-3-io": {
            "ending0": "iō",
            "ending2": "ī"
        },
        "conj-4": {
            "ending0": "iō",
            "ending2": "ī"
        },
        "conj-1-deponent": {
            "ending0": "or",
            "ending2": "us sum"
        },
        "conj-2-deponent": {
            "ending0": "eor",
            "ending2": "us sum"
        },
        "conj-3-deponent": {
            "ending0": "or",
            "ending2": "us sum"
        },
        "conj-3-io-deponent": {
            "ending0": "ior",
            "ending2": "us sum"
        },
        "conj-4-deponent": {
            "ending0": "ior",
            "ending2": "us sum"
        }
    },
    "s": {
        "decl-1": {
            "ending0": "a",
            "ending1": "æ"
        },
        "decl-1-e": {
            "ending0": "ē",
            "ending1": "ēs"
        },
        "decl-1-es": {
            "ending0": "ēs",
            "ending1": "æ"
        },
        "decl-1-as": {
            "ending0": "ās",
            "ending1": "æ"
        },
        "decl-2": {
            "ending0": "us",
            "ending1": "ī"
        },
        "decl-2-r": {
            "ending0": "r",
            "ending1": "rī"
        },
        "decl-2-os": {
            "ending0": "os",
            "ending1": "ī"
        },
        "decl-2-neuter": {
            "ending0": "um",
            "ending1": "ī"
        },
        "decl-2-on-neuter": {
            "ending0": "on",
            "ending1": "ī"
        },
        "decl-3": {
            "ending0": "",
            "ending1": "is"
        },
        "decl-3-i": {
            "ending0": "",
            "ending1": "is"
        },
        "decl-3-eos": {
            "ending0": "",
            "ending1": "eos"
        },
        "decl-3-neuter": {
            "ending0": "",
            "ending1": "is"
        },
        "decl-3-i-neuter": {
            "ending0": "",
            "ending1": "is"
        },
        "decl-4": {
            "ending0": "us",
            "ending1": "ūs"
        },
        "decl-4-neuter": {
            "ending0": "ū",
            "ending1": "ūs"
        },
        "decl-5": {
            "ending0": "ēs",
            "ending1": "eī"
        }
    },
    "pl": {
        "decl-1": {
            "ending0": "æ",
            "ending1": "ārum"
        },
        "decl-1-e": {
            "ending0": "æ",
            "ending1": "ārum"
        },
        "decl-1-es": {
            "ending0": "æ",
            "ending1": "ārum"
        },
        "decl-1-as": {
            "ending0": "æ",
            "ending1": "ārum"
        },
        "decl-2": {
            "ending0": "ī",
            "ending1": "ōrum"
        },
        "decl-2-r": {
            "ending0": "rī",
            "ending1": "rōrum"
        },
        "decl-2-os": {
            "ending0": "ī",
            "ending1": "ōrum"
        },
        "decl-2-neuter": {
            "ending0": "a",
            "ending1": "ōrum"
        },
        "decl-2-on-neuter": {
            "ending0": "a",
            "ending1": "ōrum"
        },
        "decl-3": {
            "ending0": "ēs",
            "ending1": "um"
        },
        "decl-3-i": {
            "ending0": "ēs",
            "ending1": "ium"
        },
        "decl-3-eos": {
            "ending0": "ēs",
            "ending1": "um"
        },
        "decl-3-neuter": {
            "ending0": "a",
            "ending1": "um"
        },
        "decl-3-i-neuter": {
            "ending0": "ia",
            "ending1": "ium"
        },
        "decl-4": {
            "ending0": "ūs",
            "ending1": "uum"
        },
        "decl-4-neuter": {
            "ending0": "ua",
            "ending1": "uum"
        },
        "decl-5": {
            "ending0": "ēs",
            "ending1": "ērum"
        }
    }
};
var copy_before = {
	"pls":"pl",
	"spl":"s",
	"impersonal-passive": "personal",
};
$.each(copy_before, function(i,j) {
	changes[i] = changes[j];
})
$.each(changes, function(i,h) {
	$.each(h, function(j,v) {
		if (changes[j] === undefined) changes[j] = {};
		changes[j][i] = v;
	});
});
var copy_after = {
};
$.each(copy_after, function(i,j) {
	changes[i] = changes[j];
})
