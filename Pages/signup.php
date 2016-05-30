<header>
    <h2 class="signup">Sign Up</h2>
</header>
<article>
    <div class="signup">
        <div class="input">
            <input id="signupusername" class="signup" type="text" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="Username"><br>
            <input id="signuppassword" class="signup" type="password" placeholder="Password"><br>
            <input id="signupconfirmpassword" class="signup" type="password" placeholder="Confirm Password"><br>
            <input id="signupemail" class="signup" type="email" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="Email"><br>
            <?php sro("/PHP5/user/classes.php") ?><br>
            <button id="signupsubmit">Sign Up</button>
            <div id="signuperror"></div>
        </div>
    </div>
    <script type="text/javascript">
        $('#signupsubmit').click(function(e) {
                var username = $('#signupusername').val();
                var password = $('#signuppassword').val();
                var cpassword = $('#signupconfirmpassword').val();
                var email = $('#signupemail').val();
                var classid = $('input[name=signupclass]:checked').val();
                if (username != '') {
                    if (password != '') {
                        if (cpassword != '') {
                            if (email != '') {
                                if (cpassword == password) {
                                    if (email.search(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i) != -1) {
                                        $('#signuperror').html('Sending&nbsp;request...');
                                        password = loginHash(username, password);
                                        cpassword = loginHash(username, cpassword);
                                        $.post("/PHP5/signup.php", { u: username, p: password, c: cpassword, e: email, l: classid }, function(data) {
                                            if (data == 'success') {
                                                $('#signuperror').html('Account&nbsp;created. Redirecting to login page...<!--<br>Please&nbsp;check&nbsp;your&nbsp;email.-->');
                                                setTimeout(function(){window.location.replace('login.php');}, 2000);
                                            } else {
                                                switch (data) {
                                                    case '1':
                                                        $('#signuperror').html('Error:&nbsp;Already&nbsp;logged&nbsp;in.');
                                                        break;
                                                    case '2':
                                                        $('#signuperror').html('Error:&nbsp;Invalid&nbsp;magic&nbsp;code.<br>Please&nbsp;try&nbsp;again.');
                                                        break;
                                                    case '3':
                                                        $('#signuperror').html('Error:&nbsp;Username&nbsp;in&nbsp;use.');
                                                        break;
                                                    case '4':
                                                        $('#signuperror').html('Error:&nbsp;Error&nbsp;processing&nbsp;request.<br>Please&nbsp;try&nbsp;again.');
                                                        break;
                                                    case '5':
                                                        $('#signuperror').html('Error:&nbsp;Passwords&nbsp;do&nbsp;not&nbsp;match.');
                                                        break;
                                                    case '6':
                                                        $('#signuperror').html('Error:&nbsp;Spamming&nbsp;sign&nbsp;up&nbsp;detected.');
                                                        break;
                                                    case '7':
                                                        $('#signuperror').html('Error:&nbsp;Error&nbsp;creating&nbsp;account.');
                                                        break;
                                                    case '8':
                                                        $('#signuperror').html('Error:&nbsp;Error&nbsp;sending&nbsp;validation&nbsp;email.');
                                                        break;
                                                    case '9':
                                                        $('#signuperror').html('Error:&nbsp;Email&nbsp;in&nbsp;use.');
                                                        break;
                                                    case '10':
                                                        $('#signuperror').html('Error:&nbsp;Invalid&nbsp;class.');
                                                        break;
                                                    default: 
                                                        $('#signuperror').html('Error:&nbsp;Unknown&nbsp;error&nbsp('+data+').');
                                                }
                                        }
                                    });
                                } else {
                                    $('#signuperror').html('Invalid&nbsp;email&nbsp;address.');
                                }
                            } else {
                                $('#signuperror').html('Passwords&nbsp;do&nbsp;not&nbsp;match.');
                            }
                        } else {
                            $('#signuperror').html('Missing&nbsp;email.');
                        }
                    } else {
                        $('#signuperror').html('Please&nbsp;confirm&nbsp;password.');
                    }
                } else {
                    $('#signuperror').html('Missing&nbsp;password.');
                }
            } else {
                $('#signuperror').html('Missing&nbsp;username.');
            }
        });

        $('input.signup').keypress(function(e){
            if (e.which == 13) {
                $('#signupsubmit').click();
            }
        });
    </script>
</article>
