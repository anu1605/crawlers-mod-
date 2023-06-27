<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Exception\TimeoutException; // Import the TimeoutException class

// Set the URL of the web page you want to capture
$url = 'https://epaper.navgujaratsamay.com/3725080/Ahmedabad/27-JUNE-2023#page/12/3';

// Define the output file path for the screenshot
$outputFile = 'screenshot.png';

// Create a new instance of Panther Client
$client = Client::createChromeClient();

try {
    // Start the WebDriver
    $client->start();

    // Navigate to the URL
    $client->request('GET', $url);



    // Scroll to the bottom of the page
    $client->executeScript('window.scrollTo(0, document.body.scrollHeight);');

    // Scroll horizontally to the right
    $client->executeScript('window.scrollTo(1000, 0);');

    // Get the dimensions of the page
    $scrollWidth = $client->executeScript('return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);');
    $scrollHeight = $client->executeScript('return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);');

    // Set the window size to capture the entire page
    $window = $client->getWebDriver()->manage()->window();
    $window->setSize(new WebDriverDimension($scrollWidth, $scrollHeight));

    // Capture the screenshot of the entire page
    $client->takeScreenshot($outputFile);

    // Reset the window size to the default
    $window->setSize(new WebDriverDimension(1920, 1080));
} catch (TimeoutException $e) {
    echo "last page";
} finally {
    // Close the client
    $client->quit();
}
