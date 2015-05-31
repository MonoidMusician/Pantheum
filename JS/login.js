function loginHash(username, password) {
    return hex_md5(
        loginHasher(
            loginHasher(
                password
            )
        )
        +
        loginHasher(
            loginHasher(
                username
            )
        )
    );
}

function loginHasher(text) {
    return CryptoJS.SHA512(
        Whirlpool(
            hex_md5(
                hex_md5(
                    Whirlpool(
                        CryptoJS.SHA512(
                            text
                        ).toString()
                    ).toLowerCase()
                )
            )
        ).toLowerCase()
    ).toString();
}

function loginHasher2(text) {
    return CryptoJS.SHA512(
        Whirlpool(
            hex_md5(
                hex_md5(
                    Whirlpool(
                        CryptoJS.SHA512(
                            text
                        )
                    ).toLowerCase()
                )
            )
        ).toLowerCase()
    );
}

function loginSubmit(username, password, error) {
    if ((username != '') && (password != '')) {
        password = loginHash(username, password);
        $.post("/PHP5/login.php", { u: username, p: password }, function(data) {
            if (data == 'success') {
                window.location.href = '/index.php';
                $.jStorage.flush();
            } else {
                if (data == '1') {
                    $(error).html('Already logged in.');
                } else if (data == '2') {
                    $(error).html('Error logging in.');
                } else if (data == '3') {
                    $(error).html('User is banned.');
                } else if (data == '4') {
                    $(error).html('Missing values.');
                } else {
                    $(error).html('Error logging in (' + data + ').');
                }
            }
        });
    } else {
        if (username == '') {
            $(error).html('Missing username.');
        } else {
            $(error).html('Missing password.');
        }
    }
}
