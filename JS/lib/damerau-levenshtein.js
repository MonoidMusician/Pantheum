// Adapted from https://github.com/lzrski/node-damerau-levenshtein/blob/master/index.js
// Original code by by TheSpanishInquisition here: http://jsperf.com/damerau-levenshtein-distance

function damerau_levenshtein(__this, that, limit) {
	var thisLength = __this.length,
	    thatLength = that.length;
	var matrix = [];
	for (var i = 0; i < thisLength+1; i++) {
		matrix[i] = [i];
		matrix[i].length = thatLength;
	}
	for (var j = 0; j < thatLength+1; j++) {
		matrix[0][j] = j;
	}


	prepare = function (steps) {
		distance            = {};
		distance.steps      = steps;
		distance.relative   = steps / Math.max(thisLength, thatLength);
		distance.similarity = 1 - distance.relative;

		return distance;
	}

	if (Math.abs(thisLength - thatLength) > (limit || 32)) return prepare(limit || 32);
	if (thisLength === 0) return prepare(thatLength);
	if (thatLength === 0) return prepare(thisLength);

	// Calculate matrix.
	var this_i, that_j, cost, min, t;
	for (i = 1; i <= thisLength; ++i) {
		this_i = __this[i-1];

		// Step 4
		for (j = 1; j <= thatLength; ++j) {
			// Check the jagged ld total so far
			if (i === j && matrix[i][j] > 4) return prepare(thisLength);

			that_j = that[j-1];
			cost = (this_i === that_j) ? 0 : 1; // Step 5
			// Calculate the minimum (much faster than Math.min(...)).
			min    = matrix[i - 1][j    ] + 1;                    // Deletion.
			if ((t = matrix[i    ][j - 1] + 1   ) < min) min = t; // Insertion.
			if ((t = matrix[i - 1][j - 1] + cost) < min) min = t; // Substitution.

			// Update matrix.
			matrix[i][j] = (i > 1 && j > 1 && this_i === that[j-2] && this[i-2] === that_j && (t = matrix[i-2][j-2]+cost) < min) ? t : min; // Transposition.
		}
	}

	return prepare(matrix[thisLength][thatLength]);
}