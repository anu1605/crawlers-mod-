<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverDimension;

$url = 'https://epaper.navgujaratsamay.com/3725080/Ahmedabad#page/5/3';
$outputFile = 'screenshot.png';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

$client->wait(10, 500)->until(function () use ($client) {
    $element = $client->getCrawler()->filter('#top-container')->first();
    $style = $element->attr('style');
    return strpos($style, 'width: 2250px;') !== false;
});

$client->executeScript('window.scrollTo(0, document.body.scrollHeight);');
$client->executeScript('window.scrollTo(1000, 0);');
$window = $client->getWebDriver()->manage()->window();
$scrollWidth = $client->executeScript('return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);');
$scrollHeight = $client->executeScript('return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);');
$window->setSize(new WebDriverDimension($scrollWidth, $scrollHeight));

usleep(3000000); // Wait for 3 seconds (adjust the sleep time as needed)

$client->takeScreenshot($outputFile);
$window->setSize(new WebDriverDimension(1920, 1080));
$client->quit();
