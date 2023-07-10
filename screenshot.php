<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

$url = 'https://epaper.hindustantimes.com/delhi?eddate=09/07/2023';
$outputFile = 'screenshot.png';

$client = \Symfony\Component\Panther\Client::createChromeClient(null, [
    '--headless',
    '--no-sandbox',
    '--disable-dev-shm-usage',
]);
$client->start();
$client->request('GET', $url);

$crawler = $client->waitFor('.img_pagename');
$crawler->filter('.img_pagename')->each(function ($node) {
    $highres = str_replace('_mr', '', $node->attr('highres'));
    echo $highres . PHP_EOL;
});
