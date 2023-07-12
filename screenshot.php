<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;

$url = 'https://epaper.jagbani.com/punjab/2023-07-12/jalandhar-main';

$client = Client::createChromeClient(null, [
    '--headless',
    '--no-sandbox',
    '--disable-dev-shm-usage',
]);

$client->start();
$client->request('GET', $url);

sleep(2);
$client->takeScreenshot('screenshot.png');
// $options = $client->getCrawler()->filter('.list-group-item > span');
// $options = $client->getCrawler()->filter('.item > img');

// $optionsCount = $options->count();

// $imagelinkarray = [];

// $options->each(function ($option) use (&$imagelinkarray) {
//     $link = $option->attr('highres');
//     // $location = $option->attr('edition-location');
//     // $id = $option->attr('edition-id');
//     $modifiedHighres = str_replace('_mr', '', $link);
//     // $imagelinkarray[] = $modifiedHighres;
//     // echo "Edition: " . $location . " id: " . $id . PHP_EOL;
//     echo 'highres: ' . $modifiedHighres;
// });

// print_r($imagelinkarray);
