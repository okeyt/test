<?php

$file = file_get_contents("/www/saas_api.log");


$ip = [];
foreach ( explode("\n",$file) as $f ){
    $a = [];
    preg_match("/[(.*?) - -/",$f,$a);
    var_dump($a);
    $r = explode("--",$f);
    if( isset($ip[$r[0]])  ) {
        $ip[$r[0]]++;
    } else{
        $ip[$r[0]]=1;
    }
}

//print_r($ip);
//sort($ip);
#var_dump($ip);
