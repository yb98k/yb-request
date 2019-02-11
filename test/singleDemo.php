<?php

//single request demo

include (__DIR__.'/../vendor/autoload.php');

$url = 'http://freeapi.ipip.net/118.28.8.8';

$requestData = [
    'url' => $url,
//    'requestData' => [],
//    'header' => [],
//    'options' => []
];

$ybCurl = new \Yb\YbCurl($requestData);
$result = $ybCurl::get();

fwrite(STDOUT, $result);