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
									$.post("/PHP5/signup.php", { u: username, p: password, c: cpassword, e: email, l: classid }, function(raw) {
										var data = JSON.parse(raw)['result'];
										if (data == 'success') {
											$('#signuperror').html('Account&nbsp;created.&nbsp;Redirecting&nbsp;to&nbsp;login&nbsp;page...');
											setTimeout(function(){window.location.replace('login.php');}, 2000);
										} else {
											$('#signuperror').html("Error creating account: " + data);
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
