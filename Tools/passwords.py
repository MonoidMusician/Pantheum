import hashlib

def hasher(text):
    return hashlib.sha512(hashlib.new('whirlpool', hashlib.md5(hashlib.md5(hashlib.new('whirlpool', hashlib.sha512(text).hexdigest()).hexdigest()).hexdigest()).hexdigest()).hexdigest()).hexdigest()

def hashpasswd(username, passwd):
    al=[hasher(passwd), '', '', '', '', '']
    al[1] = hasher(al[0])
    al[2] = hasher(username)
    al[3] = hasher(al[2])
    al[4] = al[1] + al[3]
    al[5] = hashlib.md5(al[4]).hexdigest()
    return al[5]
    
def hashfull(username, passwd, createip, cid):
    return hashlib.md5(hasher(hasher(createip + hashpasswd(username, passwd) + cid))).hexdigest()
