<header>
    <h1>Login</h1>
</header>
<article>
    <p>
        <input id="username" type="text" placeholder="Username" required><br>
        <input id="password" type="password" placeholder="Password" required><br>
        <button id="submit">Log In</button><p id="login-error"></p><br><br>
        <a href="#signup">Sign up</a> or <a href="#reset">reset</a> your password.
    </p>
    <script type="text/javascript">
        $(document).on('click', '#submit', function() {
            loginSubmit($('#username').val(), $('#password').val(), '#login-error');
        }); 
    </script>
</article>
