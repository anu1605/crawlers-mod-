<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

if ($epapercode == "LS") {
    $linkarray = array("https://epaper.loksatta.com/t/92/loksatta-mumbai", "https://epaper.loksatta.com/t/643/loksatta-pune", "https://epaper.loksatta.com/t/8490/loksatta-nagpur", "https://epaper.loksatta.com/t/5356/loksatta-nasik", "https://epaper.loksatta.com/t/28138/loksatta-Chhatrapati-Sambhajinagar", "https://epaper.loksatta.com/t/28139/loksatta-ahmednagar");

    $cityarray = array("mumbai", "pune", "nagpur", "nasik", "Chhatrapati", "ahmednagar");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $url = $linkarray[$edition];

        // $client = Client::createChromeClient();
        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
        ]);
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
            $url = str_replace('r/', '', $desiredItem['href'] . "/loksatta-mumbai/" . date("d-m-Y", strtotime($filenamedate)) . "#page/1/3");
            setSize($client, $url);

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $url = str_replace('r/', '', $desiredItem['href'] . "/loksatta-mumbai/" . date("d-m-Y", strtotime($filenamedate)) . "#page/" . $page . "/3");
                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));
                $outputFile = $getpath[0];

                $client->request('GET', $url);

                $client->executeScript('document.getElementById("top-clips-box").style.display = "block";');

                sleep(3);

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                $screenshot = $client->takeScreenshot($outputFile);

                $croppedImage = getCroppedImage($client, 'page-div', $screenshot);
                imagepng($croppedImage, $outputFile);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Ahmedabad", $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        } else {
            echo 'Date not found in the data array.';
        }
        $client->quit();
    }
}
