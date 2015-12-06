<?php 
@ini_set("output_buffering", "Off");
@ini_set('implicit_flush', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('max_execution_time',1200);


header( 'Content-type: text/html; charset=utf-8' );
for ($i = 0; $i < 3600; $i++) {
    echo "a";

    if(sleep(1)!=0)
    {
        echo "b"; 
        break;
    }
    flush();
    ob_flush();
}

?>
