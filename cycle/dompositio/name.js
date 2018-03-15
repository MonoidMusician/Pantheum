function Tag(name) {
	var matches = tagexp.exec(name);
	this.ns = matches[1];
	this.namespace = matches[1] && d3.namespaces[matches[1]];
	this.base = new Name(matches[2]);
	this.classes = ((matches[3]||'')+(matches[5]||'')).split('.').filter(Boolean);
	this.id = matches[4];
}
Tag.prototype.toString = function() {
	var t = this.base;
	if (this.ns) t = this.ns+':'+t;
	if (this.id) t += '#'+this.id;
	t += this.classes.map(c => '.'+c).join('');
	return t;
};
function Name(...parts) {
	this.parts = [];
	for (let part of parts) {
		this.parts.push(...part.split(/-|(?=[A-Z][a-z])/g).map(s => s.toLowerCase()));
	}
};
Name.prototype.lower = function() {
	return this.parts.join('');
};
Name.prototype.kebab = function() {
	return this.parts.join('-');
};
Name.prototype.camel = function() {
	return this.parts[0] + this.parts.slice(1).map(s => s[0].toUpperCase() + s.slice(1)).join('');
};
Name.prototype.on = function() {
	return new Name('on', ...this.parts);
};
