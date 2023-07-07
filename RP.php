<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;


$cityarray = array("Bhind", "Bhopal", "Gwalior City", "Indore City", "jabalpur", "Ujjain", "Ajmer City", "Chittorgarh", "Jaipur City", "Jodhpur City", "Kota", "Udaipur City", "Bilaspur", "Raigarh", "Raipur City", "Ahmedabad", "Bangalore", "Chennai", "Coimbatore", "Hubli", "Kolkata", "New Delhi", "Surat", "Uttar Pradesh");

$citycode = array("77", "64", "78", "85", "123", "95", "3", "14", "20", "23", "26", "52", "100", "105", "109", "55", "56", "58", "139", "57", "135", "59", "60", "117");
$dateforlinks = date("d/m/Y", strtotime($filenamedate));

if ($cityarray != null) {

    if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
}

for ($edition = 0; $edition < count($cityarray); $edition++) {
    $url = 'https://epaper.patrika.com/Home/ArticleView?eid=20&edate=06/07/2023';
    $outputFile = 'screenshot.png';

    $client = Client::createChromeClient();
    $client->start();
    $client->request('GET', $url);

    // Find the select element and count its options
    $options = $client->getCrawler()->filter('#ddl_Pages option');

    $optionsCount = $options->count();
    echo 'Number of options: ' . $optionsCount . PHP_EOL;

    // Echo highres attribute for each option
    $options->each(function ($option) {
        $highres = $option->attr('highres');
        echo 'Highres: ' . $highres . PHP_EOL;
    });

    // Rest of your code...

    $client->quit();
}
