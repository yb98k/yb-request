<?php

//multi request demo

include (__DIR__.'/../../vendor/autoload.php');

$url = 'http://freeapi.ipip.net/118.28.8.8';

$requestData = [
    [
        'url' => $url
    ],
    [
        'url' => $url,
    ],
    [
        'url' => $url
    ]
];

$ybCurl = new \Yb\YbCurl($requestData, function ($output, $info, $error, $request){
    fwrite(STDOUT, $output);
});
$result = $ybCurl::get();
