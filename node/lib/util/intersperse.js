module.exports = function intersperse(arr, sep) {
	return arr.length ? arr.slice(1).reduce((xs, x, i) => (xs.push(sep, x), xs), [arr[0]]) : [];
}
