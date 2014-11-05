function adminLoadUser(event) {
    cpage.setStorage('id', event.data.id);
    window.location.href = '#user';
}

function adminLoadWord(event) {
    cpage.setStorage('id', event.data.id);
    window.location.href = '#word';
}

function ahDoHex() {
    $('#ahhex').val(
        hex(
            $('#ahplain').val()
        )
    );
}

function ahDoUnHex() {
    $('#ahplain').val(unhex($('#ahhex').val()));
}
