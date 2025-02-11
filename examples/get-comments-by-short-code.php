<?php

require_once __DIR__ . '/../vendor/autoload.php';

$api = new JWorksUK\InstagramApi\Client(
    new GuzzleHttp\Client(),
    include __DIR__ . '/cookie.php'
);

$comments = $api->getCommentsByShortCode('DFbWYP9SLE1');

dump($comments);
