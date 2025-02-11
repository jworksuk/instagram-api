<?php

require_once __DIR__ . '/../vendor/autoload.php';

$api = new JWorksUK\InstagramApi\Client(
    new GuzzleHttp\Client(),
    include __DIR__ . '/cookie.php'
);

$media = $api->getMedia('ishowspeed');

dump($media);
