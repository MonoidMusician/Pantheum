<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Latin</title>
        <meta name="viewport" content="width=512px, user-scalable=no">
        <meta content="charset=utf-8">
        <style type="text/css">
            html {
                border: none;
                padding: 0;
                margin: 0;
            
                width: 100%;
            }
            
            body {
                border: none;
                padding: 0;
                margin: 0;
            
                width: 100%;
                
                overflow-x: hidden;
                
                background-color: #fafafa;
            }
            
            header.nav {
                border: none;
                padding: 0;
                margin: 0;
            }
            
            nav.nav {
                border: none;
                padding: 0;
                margin: 0;
                
                height: 40px;
                width: 100%;
                
                background-color: #cc3333;
                
                position: relative;
            }
            
            nav.nav a {
                color: #efefef;
                
                text-decoration: none;
            }
            
            nav.nav ul {
                border: none;
                
                padding: 0;
                margin: 0;
                
                list-element-style: none;
                
                position: absolute;
                bottom: 0px;
            }
            
            nav.nav li {
                display: inline-block;
                
                padding: 5px;
                
                height: 20px;
                width: 100px;
                
                background-color: #aa0000;
                border: 1px solid #CC3333;
                
                text-align: center;
                font-family: sans-serif;
                font-size: 18px;
            }
            
            nav.nav li:hover {
                background-color: #BB2222;
            }
            
            section#content {
                display: block !important;
                
                width: 85% !important;
                margin: 0 auto;
                padding: 15px;
                
                margin-top: 15px;
                margin-bottom: 15px;
                
                background-color: #efefef;
            }
            
            section#content a {
                color: #4466aa;
                text-decoration: none;
            }
            
            input {
                border: 1px solid #aaaaaa;
                height: 30px;
                width: 200px;
                margin: 1px;
                border-radius: 2px;
            }
            
            input:focus, input:active {
                border: 1px solid #4466aa;
            }
            
            button {
                border: 1px solid #aaaaaa;
                height: 30px;
                width: 202px;
                margin: 1px;
                border-radius: 2px;
            }
            
            button:focus, button:active {
                border: 1px solid #4466aa;
            }
            
            footer {
                display: block !important;
                
                width: 85% !important;
                margin: 0 auto;
                padding-top: 5px;
                padding-bottom: 5px;
                padding-left: 15px;
                padding-right: 15px;
                
                background-color: #CC3333;
                color: #efefef;
                
                font-style: italic;
            }
            
            .center {
                margin: 0 auto;
            }
            
            .text-center {
                text-align: center;
            }
        </style>
        <script type="text/javascript" src="/lib/jquery.js"></script>
        <script type="text/javascript" src="/lib/jCanvas.js"></script>
        <script type="text/javascript" src="/lib/jTable.js"></script>
        <script type="text/javascript" src="/latin/JS/lib/md5.js"></script>
        <script type="text/javascript" src="/latin/JS/lib/sha512.min.js"></script>
        <script type="text/javascript" src="/latin/JS/lib/whirlpool.min.js"></script>
        <script type="text/javascript" src="/lib/jForm.js"></script>
        <script type="text/javascript" src="/latin/JS/artwork.js"></script>
        <script type="text/javascript" src="/latin/JS/login.js"></script>
        <script type="text/javascript" src="/lib/jSettings.js"></script>
        <script type="text/javascript" src="/latin/JS/url.js"></script>
        <script type="text/javascript" src="/latin/JS/lib/util.js"></script>
        <script type="text/javascript" src="/lib/jPage/jPage.js"></script>
        <script type="text/javascript">
        </script>
    </head>
    <body>
        <header class="nav">
            <nav id="navigation" class="nav">
            </nav>
        </header>
        <section id="content">
            
        </section>
        <footer>
            <p>
                Copyright (C) 2014 Alex and Nick Scheel.
            </p>
        </footer>
        <script type="text/javascript">
            var dpage = "";
            if (location.hash != '') {
                dpage = location.hash;
            } else {
                dpage = '#overview';
            }
            
            var page = new jPage();
            page.init('content');
            page.setPages([
                ['#home', 'Home', 'Home | Latin', '/Pages/index.php', true],
                ['#quiz', 'Quiz', 'Quiz | Latin', '/Pages/quiz.php', true], 
                <?php
                    global $sli, $suname;
                    if ($sli != true) {
                ?>
                ['#login', 'Login', 'Login | Latin', '/Pages/login.php', true], 
                <?php
                    } else {
                ?>
                    ['#user', '<?php echo $suname; ?>', '<?php echo $suname; ?> | Latin', '/Pages/user.php', true], 
                    ['#logout', 'Logout', 'Logout | Latin', '/PHP5/logout.php', true], 
                <?php
                    }
                ?>
                ['#reset', 'Reset', 'Reset password | Latin', '/Pages/reset.php', false],
                ['#signup', 'Sign Up', 'Sign Up | Latin', '/Pages/signup.php', false],  
                ['#validate', 'Validate', 'Validate | Latin', '/Pages/validate.php', false],  
                ['#playground', 'Play!', 'PHP Playground', '/Pages/playground.php', true], 
            ], dpage);
            page.setNavigation('navigation', 'ul');
            page.setBasepath('/latin');
            
            page.load();
        </script>
    </body>
</html>
