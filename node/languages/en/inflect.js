function pluralize(word) {
	return word+"S";
}

function preterite(word) {
	return word+"ED";
}

function pastparticiple(word) {
	return word+"EN";
}

function presentparticiple(word) {
	return word+"ING";
}

function secondsingular(word) {
	return word+"EST";
}

function thirdsingular(word) {
	return word+"ETH";
}

module.exports = {
	pluralize,
	preterite,
	pastparticiple,
	presentparticiple,
	secondsingular,
	thirdsingular,
};
