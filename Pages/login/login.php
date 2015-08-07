<header>
    <h1>Login</h1>
</header>
<article>
    <form action="/login.php" method="POST" id="loginform">
        <input id="username" type="text" placeholder="Username" autocapitalize="off" required><br>
        <input id="password" type="password" placeholder="Password" required><br>
        <button id="submit">Log In</button><p id="login-error"><br>
    </form>
    <p class="signup">
        <a href="/signup.php">Sign up</a> or <a href="/reset.php">reset</a> your password.
    </p>
    <script type="text/javascript">
        $(function() {
            $(document).on('submit', '#loginform', function(event) {
                loginSubmit($('#username').val(), $('#password').val(), '#login-error');
                event.preventDefault();
            });
        });
    </script>
</article>
