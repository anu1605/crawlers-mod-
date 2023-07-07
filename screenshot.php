<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

$url = 'https://epaper.patrika.com/Home/ArticleView?eid=20&edate=06/07/2023';
$outputFile = 'screenshot.png';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

// Find the select element and count its options
$options = $client->getCrawler()->filter('#ddl_Pages option');

$optionsCount = $options->count();
echo 'Number of options: ' . $optionsCount . PHP_EOL;

// Echo highres attribute for each option
$options->each(function ($option) {
    $highres = $option->attr('highres');
    echo 'Highres: ' . $highres . PHP_EOL;
});

// Rest of your code...

$client->quit();
