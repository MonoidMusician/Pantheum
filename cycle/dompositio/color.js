function gather(items, ...sources) {
	var gathered = {}
	for (let item in items) {
		for (let source in sources) {
			if (source && source[item]) {
				gathered[item] = source[item]
				break
			}
		}
		if (!(item in gathered)) {
			if (items[item] === null)
				throw new Error('Required parameter not found: '+item)
			gathered[item] = items[item]
		}
	}
	return gathered
}
