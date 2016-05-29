<?php
    require_once('/var/www/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    sro('/Includes/functions.php');

    requireRank('1');

    $new_uid = cleanInput('/[^0-9]/', $_GET['uid']);

    global $mysqli;

    $suid = $_SESSION['uid'];

    $M_query = "SELECT * FROM users WHERE id='$suid';";
    $M_result = $mysqli->query($M_query);
    $M_row = $M_result->fetch_assoc();
    $current = json_decode($M_row['currentip'] ? $M_row['currentip'] : '[]', true);
    foreach ($current as $key=>$ip) {
        if ($ip == $_SERVER['REMOTE_ADDR']) {
            unset($current[$key]);
        }
    }
    $current = json_encode($current);

    $M_query2 = "UPDATE users SET currentip='$current' WHERE id='$suid';";
    $M_result2 = $mysqli->query($M_query2);

    $_SESSION = array();

    $M_query3 = "SELECT * FROM users WHERE id='$new_uid';";
    $M_result3 = $mysqli->query($M_query3);
    $M_count3 = $M_result3->num_rows;

    if ($M_count3 != 1) {
        die("Found $M_count3 users with that uid...");
    }

    $M_row = $M_result3->fetch_assoc();
    $_SESSION['li'] = 'true';
    $_SESSION['username'] = $M_row['username'];
    $_SESSION['uid'] = $M_row['id'];
    $_SESSION['rank'] = $M_row['rank'];

    $ip = $_SERVER['REMOTE_ADDR'];
    $current = json_decode($M_row['currentip'] ? $M_row['currentip'] : '[]', true);
    $current[] = $ip;
    $current = json_encode($current);

    $M_query6 = "UPDATE users SET currentip='$current' WHERE id='" . $M_row['id'] . "';";
    $M_result6 = $mysqli->query($M_query6);

    if ($M_result6) {
        print "success";
    }
?>
