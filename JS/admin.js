function adminLoadUser(event) {
    cpage.setStorage('id', event.data.id);
    cpage.toPageByHash('#user');
}

function ahDoHex() {
    $('#ahHex').val(
        hex(
            $('#ahPlain').val()
        )
    );
}

function ahDoUnHex() {
    $('#ahPlain').val(unhex($('#ahHex').val()));
}
