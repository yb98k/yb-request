<?php

//fSock request demo

include (__DIR__.'/../../vendor/autoload.php');

$url = 'http://freeapi.ipip.net/118.28.8.8';

$fs = new \Yb\handle\FSockOpen();

$res = $fs->get($url)->getContent();

fwrite(STDOUT, $res);