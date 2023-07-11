<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;

$url = 'https://epaper.thehindu.com/ccidist-ws/th//?json=true&fromDate=2023-07-10&toDate=2023-07-10&skipSections=true&os=web&includePublications=th_Delhi';

$client = Client::createChromeClient();
$client->start();
$client->request('GET', $url);

$jsonContent = $client->waitFor('body')->getText();
$data = json_decode($jsonContent, true);

if (isset($data['publications'][0]['issues']['web'][0]['readerUrl'])) {
    $mainURL = $data['publications'][0]['issues']['web'][0]['readerUrl'];
} else {
    echo 'Reader URL not found in the JSON response';
}

for ($page = 1; $page < 50; $page++) {
    $nextbtn = $client->waitFor('next-page-button[display=none]');
    $mainURL = str_replace('page=1', 'page=' . $page, $mainURL);
    $client->request('GET', $mainURL);
    $images = $client->getCrawler()->filter('img.page-left-bitmap');
    $imageCount = count($images);
    $src = $images->attr('src');
    echo 'Image src: ' . $src . PHP_EOL;
}
$client->quit();
