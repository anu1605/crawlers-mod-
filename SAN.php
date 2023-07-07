<?php
require_once 'vendor/autoload.php';
// require_once '/var/www/d78236gbe27823/vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

if ($epapercode == "SAN") {
    $cityarray = array("ahmedabad", "gandhinagar", "surat", "rajkot", "vadodara", "bhuj", "bhavnagar");
    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {

        $url = 'https://sandesh.com/epaper/' . $cityarray[$edition] . '&date=' . $filenamedate;

        $client = Client::createChromeClient();

        $client->start();
        $client->request('GET', $url);

        $client->waitFor('.form-control.form-control-lg.page_menu');

        $selectElement = $client->getCrawler()->filter('.form-control.form-control-lg.page_menu')->first();

        $optionsCount = $selectElement->filter('option')->count();

        $imageElements = $client->getCrawler()->filter('.epaper-news-img')->slice(0, $optionsCount);
        $imageLinks = $imageElements->extract(['src']);

        $imagelinkarray = [];
        foreach ($imageLinks as $imageLink) {
            $imagelink = str_replace('resize-img.sandesh.com/', '', $imageLink);
            $imagelink = str_replace('s-', '', $imagelink);
            $imagelink = str_replace('?w=800', '', $imagelink);
            $imagelinkarray[] = $imagelink;
        }

        $page = 1;
        foreach ($imagelinkarray as $imagelink) {

            if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

            if (!$imageInfo)
                break;

            $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();

            if ($page == $no_of_pages_to_run_on_each_edition)
                break;

            $page++;
        }

        $client->quit();
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] .  " Completed" . $eol;
    }
}
