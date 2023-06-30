<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverDimension;

$url = 'https://epaper-sakal-application.s3.ap-south-1.amazonaws.com/EpaperData/Sakal/Pune/2023/06/30/Main/Sakal_Pune_2023_06_30_Main_DA_001_PR.jpg';
$outputFile = 'screenshot.png';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

sleep(1); // Wait for the top-clips-box to become visible

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
