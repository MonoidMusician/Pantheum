<?php
    global $SDIR, $RDIR;
    $SDIR = '/var/www/latin';
    $RDIR = '/latin';
    
    function sro($path) {
        global $SDIR;
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }
        if (file_exists($SDIR . $path)) {
            require_once($SDIR . $path);
        } else {
            print "File ($SDIR$path) not found!";
        }
    }
    
    function rgd($path) {
        global $RDIR;
        return $RDIR . $path;
    }
?>
