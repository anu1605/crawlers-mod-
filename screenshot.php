<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$filenamedate = date('Y-m-d', strtotime("2023-07-03"));
$url = 'https://sandesh.com/epaper/ahmedabad&date=' . $filenamedate;
$outputFile = 'screenshot.png';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

// Convert $filenamedate to the desired format


// Wait for the select element to be visible
$client->waitFor('.form-control.form-control-lg.page_menu');

// Find the select element
$selectElement = $client->getCrawler()->filter('.form-control.form-control-lg.page_menu')->first();

// Get the count of options
$optionsCount = $selectElement->filter('option')->count();

// Display the count of options
echo 'Number of options: ' . $optionsCount . PHP_EOL;

// Find the image elements with class 'epaper-news-img' and get the image links
$imageElements = $client->getCrawler()->filter('.epaper-news-img')->slice(0, $optionsCount);
$imageLinks = $imageElements->extract(['src']);

// Replace and remove unwanted parts from the image links
$replacedImageLinks = [];
foreach ($imageLinks as $imageLink) {
    $replacedImageLink = str_replace('resize-img.sandesh.com/', '', $imageLink);
    $replacedImageLink = str_replace('s-', '', $replacedImageLink);
    $replacedImageLink = str_replace('?w=800', '', $replacedImageLink);
    $replacedImageLinks[] = $replacedImageLink;
}

// Display the modified image links
echo 'Image Links:' . PHP_EOL;
foreach ($replacedImageLinks as $replacedImageLink) {
    echo $replacedImageLink . PHP_EOL;
}

$client->quit();
