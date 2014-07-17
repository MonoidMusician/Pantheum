function hex(text) {
    var result = "";
    var tsplit = text.split('');
    for (var pos in tsplit) {
        result += tsplit[pos].charCodeAt(0).toString(16);
    }
    return result;
}

function unhex(text) {
    last = text;
    text = text.replace(/[^0-9a-fA-F]/, '', 'gm');
    while (text = text.replace(/[^0-9a-fA-F]/, '', 'gm'))
    {
        if (last != text) {
            last = text;
        } else {
            break;
        }
    }
    if (text.length % 2 != 0) {
        return "";
    } else {
        var result = "";
        var stext = text.split('');
        for (var i = 0; i < text.length; i += 2) {
            result += String.fromCharCode(parseInt("0x" + stext[i] + '' + stext[i+1]));
        }
        return result;
    }
}
