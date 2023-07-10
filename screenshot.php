<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

$url = 'https://epaper.loksatta.com/t/92/loksatta-mumbai';
$outputFile = 'screenshot.png';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

$anchors = $client->getCrawler()->filter('div.section_publication > a');
$spans = $client->getCrawler()->filter('div.section_publication span');

$data = [];
$anchors->each(function ($anchor, $i) use ($spans, &$data) {
    $href = $anchor->attr('href');
    $date = $spans->eq($i)->text();
    $data[] = [
        'href' => $href,
        'date' => $date
    ];
});

$filenamedate = date("Y-m-d", time());

// Find the href and date for the desired date
$desiredItem = null;
foreach ($data as $item) {
    $formattedDate = date('Y-m-d', strtotime($item['date']));
    if ($formattedDate === $filenamedate) {
        $desiredItem = $item;
        break;
    }
}

if ($desiredItem !== null) {
    // Echo the href and date for the desired date

    for ($page = 1; $page <= 5; $page++) {
        $url = str_replace('r/', '', $desiredItem['href'] . "/loksatta-mumbai/" . date("d-m-Y", strtotime($filenamedate)) . "#page/" . $page . "/3");

        echo $url . PHP_EOL;

        $getpath = 'screenshot' . $page . '.png';

        $client->request('GET', $url);

        $client->executeScript('document.getElementById("top-clips-box").style.display = "block";');


        $screenshot = $client->takeScreenshot($getpath);

        $croppedImage = getCroppedImage($client, 'page-div', $screenshot);
        imagepng($croppedImage, $getpath);
    }
} else {
    echo 'Date not found in the data array.';
}
$client->quit();
function getCroppedImage($client, $element, $screenshot)
{
    // Find required element
    $dePageContainer = $client->findElement(WebDriverBy::id($element));

    // Get the location and size of the de-page-container element
    $location = $dePageContainer->getLocation();
    $size = $dePageContainer->getSize();

    // Calculate the coordinates for the screenshot
    $x = $location->getX();
    $y = $location->getY();
    $width = $size->getWidth();
    $height = $size->getHeight();

    // Take a screenshot of the specified section

    // $imageData = file_get_contents($screenshot);
    $image = imagecreatefromstring($screenshot);
    $croppedImage = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
    return $croppedImage;
}
