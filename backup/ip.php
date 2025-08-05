<?php

$filename = 'ips.txt';

if (file_exists($filename)) 
    {
       
        $fp = fopen($filename, "a");
        fputs ($fp, $_SERVER[REMOTE_ADDR] . " --- ");
        fclose ($fp);
    } 

else 
    {
        $fh = fopen($filename, "w");
        if($fh==false)
            die("unable to create file");
        fputs ($fh,  $_SERVER[REMOTE_ADDR]);
        fclose ($fh);
        $count = file($filename); 
    }

?>

OK