<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

if ($epapercode == "PRM") {
    $url = 'https://www.prameyaepaper.com/';

    $client = Client::createChromeClient(null, [
        '--headless',
        '--no-sandbox',
        '--disable-dev-shm-usage',
        '--disable-javascript',
    ]);

    $client->start();
    $client->request('GET', $url);

    sleep(2);

    $anchors = $client->getCrawler()->filter('a.nav-link');

    $anchors->each(function ($anchor) use (&$datecode) {
        $href = $anchor->attr('href');
        $parts = explode('/edition/', $href);
        if (isset($parts[1])) {
            $subParts = explode('/', $parts[1]);
            if (strtolower($subParts[1]) === 'bhubaneswar') {
                $datecode = $subParts[0];
            }
        }
    });


    $client->quit();

    $filenamedate = date("Y-m-d", time());
    $dateforlinks = date("Ymd", strtotime($filenamedate));

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

        $imagelink = "https://moapi.prameyanews.com/prameya/document/pdf/3_102_" . $datecode . "_" . $dateforlinks . "_" . sprintf("%02d", $page) . ".jpg";

        if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

        if (!$imageInfo)
            break;

        $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $page, $lang));

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

        writeImage($imagelink, $getpath[0]);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
        runTesseract($epapername, "Bhubaneswar", $page, $page, $conn, $getpath, $lang);
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
        ob_flush();
        flush();
    }
}
