<?php
    require_once('/var/www/latin/config.php');
    sro('/Includes/mysql.php');
    sro('/Includes/session.php');
    
    function sendEmailTo($from, $to, $subject, $body) {
        sro('/PHP5/lib/Swift/lib/swift_required.php');
        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')->setUsername("sassenburg.latin@gmail.com")->setPassword("s2a4d5l6f7j8as2l1dk5f6j%&dk#$%j^&skderfj6la#^kdjf#^skdjfa#$%#$%skdf^#jsaldkfjajj");
        $mailer = Swift_Mailer::newInstance($transport);
        
        if (!is_array($to)) {
            $to = array($to);
        }
        
        $message = Swift_Message::newInstance()->setSubject($subject)->setFrom(array('sassenburg.latin@gmail.com' => $from))->setTo($to)->setBody($body, 'text/html');
        
        if(!$mailer->send($message)) {
            return -1;
        } else {
            return 0;
        }
    }
    
    function sendEmailBcc($from, $to, $bcc, $subject, $body) {
        sro('/PHP5/lib/Swift/lib/swift_required.php');
        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')->setUsername("sassenburg.latin@gmail.com ")->setPassword("s2a4d5l6f7j8as2l1dk5f6j%&dk#$%j^&skderfj6la#^kdjf#^skdjfa#$%#$%skdf^#jsaldkfjajj");
        $mailer = Swift_Mailer::newInstance($transport);
        
        if (!is_array($to)) {
            $to = array($to);
        }

        if (!is_array($bcc)) {
            $bcc = array($bcc);
        }
        
        $message = Swift_Message::newInstance()->setSubject($subject)->setFrom(array('sassenburg.latin@gmail.com' => $from))->setTo($to)->setBcc($bcc)->setBody($body, 'text/html');
        
        if(!$mailer->send($message)) {
            return -1;
        } else {
            return 0;
        }
    }
?>
