<?php
    global $SDIR, $RDIR;
    $SDIR = '/var/www';
    $RDIR = '';
    
    function sro($path) {
        global $SDIR;
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }
        if (file_exists($SDIR . $path)) {
            require_once($SDIR . $path);
        } else {
            error_log("File ($SDIR$path) not found!");
        }
    }
    
    function rgd($path) {
        global $RDIR;
        return $RDIR . $path;
    }
?>
