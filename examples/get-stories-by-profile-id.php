<?php

require_once __DIR__ . '/../vendor/autoload.php';

$api = new JWorksUK\InstagramApi\Client(
    new GuzzleHttp\Client(),
    include __DIR__ . '/cookie.php'
);

$stories = $api->getStoriesByProfileId(25025320);

dump($stories);
