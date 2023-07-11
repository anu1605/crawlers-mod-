<?php

require_once 'vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;

$url = 'https://epaper.thehindu.com/ccidist-ws/th//?json=true&fromDate=2023-07-10&toDate=2023-07-10&skipSections=true&os=web&includePublications=th_Delhi';

$client = HttpClient::create();
$response = $client->request('GET', $url);

if ($response->getStatusCode() === 200) {
    $content = $response->getContent();
    $data = json_decode($content, true);

    if (isset($data['publications'][0]['issues']['web'][0]['readerUrl'])) {
        echo 'Reader URL: ' . $data['publications'][0]['issues']['web'][0]['readerUrl'];
    } else {
        echo 'Reader URL not found in the JSON response';
    }
} else {
    echo 'Request failed with status code: ' . $response->getStatusCode();
}
