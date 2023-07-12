<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;

$url = 'https://epaper.andhrajyothy.com/Home/FullPage?eid=34&edate=12/07/2023';

$client = Client::createChromeClient(null, [
    '--headless',
    '--no-sandbox',
    '--disable-dev-shm-usage',
    '--disable-javascript', // Disable JavaScript execution
]);

$client->start();
$client->request('GET', $url);

$options = $client->getCrawler()->filter('.list-group-item > span');

// $optionsCount = $options->count();

$imagelinkarray = [];

$options->each(function ($option) use (&$imagelinkarray) {
    $location = $option->attr('edition-location');
    $id = $option->attr('edition-id');
    // $modifiedHighres = str_replace('_mr', '', $imagelink);
    // $imagelinkarray[] = $modifiedHighres;
    echo "Edition: " . $location . " id: " . $id . PHP_EOL;
});

print_r($imagelinkarray);
