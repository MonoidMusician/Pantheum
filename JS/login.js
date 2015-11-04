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
                if (window.ga)
                    ga('send', 'event', 'User', 'login', username);
                if ($.jStorage) $.jStorage.flush();
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

function changePassword(username, old, new1, new2, error) {
    console.log(username, old, new1, new2, error);
    if ((username != '') && (old != '') && (new1 != '') && (new1 === new2)) {
        old = loginHash(username, old);
        new1 = loginHash(username, new1);
        new2 = loginHash(username, new2);
        $.post("/PHP5/user/change-password.php", { u: username, p: old, n1: new1, n2: new2 }, function(data) {
            if (data == 'success') {
                window.location.href = '/index.php';

                $.jStorage.flush();
            } else {
                if (data == '1') {
                    $(error).html('Unknown error.');
                } else if (data == '2') {
                    $(error).html('Wrong password.');
                } else if (data == '3') {
                    $(error).html('Bad username.');
                } else if (data == '4') {
                    $(error).html('Passwords did not match.');
                } else if (data == '5') {
                    $(error).html('Missing form.');
                } else if (data == '6') {
                    $(error).html('You\'ve been logged out.');
                } else {
                    $(error).html('Error logging in (' + data + ').');
                }
            }
        });
    } else {
        if (username == '') {
            $(error).html('Missing username.');
        } else if (old == '') {
            $(error).html('Missing password (current).');
        } else if (new1 == '') {
            $(error).html('Missing password (new).');
        } else if (new2 == '') {
            $(error).html('Please confirm your password.');
        } else if (new1 != new2) {
            $(error).html('Passwords did not match');
        }
    }
}
