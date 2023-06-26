<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverWindow;

// Set the URL of the web page you want to capture
$url = 'https://epaper.loksatta.com/3724497/loksatta-mumbai/26-06-2023#page/13/3';

// Define the output file path for the screenshot
$outputFile = 'screenshot.png';

// Create a new instance of Panther Client
$client = Client::createChromeClient();

// Start the WebDriver
$client->start();

// Navigate to the URL
$client->request('GET', $url);

// Scroll to the bottom of the page
$client->executeScript('window.scrollTo(0, document.body.scrollHeight);');

// Scroll horizontally to the right
$client->executeScript('window.scrollTo(1000, 0);');

// Wait for the specific element at the bottom of the page to become visible
$locator = WebDriverBy::cssSelector('#top-clips-box');
$wait = $client->getWebDriver()->wait(10);
$wait->until(WebDriverExpectedCondition::visibilityOfElementLocated($locator));

// Get the dimensions of the page
$window = $client->getWebDriver()->manage()->window();
$scrollWidth = $client->executeScript('return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);');
$scrollHeight = $client->executeScript('return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);');

// Set the window size to capture the entire page
$window->setSize(new WebDriverDimension($scrollWidth, $scrollHeight));

// Capture the screenshot of the entire page
$client->takeScreenshot($outputFile);

// Reset the window size to the default
$window->setSize(new WebDriverDimension(1920, 1080));

// Close the client
$client->quit();
