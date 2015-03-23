<?php
    $M_host = 'localhost';
    $M_user = 'root';
    $M_password = 'latinpassword';
    $M_database = 'latin';
    
    global $mysqli;
    
    $mysqli = new mysqli($M_host, $M_user, $M_password, $M_database);
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");

    // sassenburg.latin@gmail.com / s2a4d5l6f7j8as2l1dk5f6j%&dk#$%j^&skderfj6la#^kdjf#^skdjfa#$%#$%skdf^#jsaldkfjajj
    
    unset($M_host);
    unset($M_user);
    unset($M_password);
    unset($M_database);
?>
