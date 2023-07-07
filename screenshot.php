<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

$url = 'https://www.bhaskar.com/epaper/detail-page/ranchi/109/2023-07-06?pid=2';
$outputFile = 'screenshot.png';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

$hasImage = $client->getCrawler()->filter('div.react-transform-component.transform-component-module_content__2jYgh img')->count() > 0;
if ($hasImage) {
    $imageSource = $client->getCrawler()->filter('div.react-transform-component.transform-component-module_content__2jYgh img')->attr('src');
    echo 'Image source: ' . $imageSource;
} else {
    echo 'Attempt failed';
}
