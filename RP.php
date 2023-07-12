<?php
require  '/var/www/d78236gbe27823/vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

if ($epapercode == "RP") {
    $cityarray = array("Bhind", "Bhopal", "Gwalior City", "Indore City", "jabalpur", "Ujjain", "Ajmer City", "Chittorgarh", "Jaipur City", "Jodhpur City", "Kota", "Udaipur City", "Bilaspur", "Raigarh", "Raipur City", "Ahmedabad", "Bangalore", "Chennai", "Coimbatore", "Hubli", "Kolkata", "New Delhi", "Surat");

    $cityforfilepath = array("Bhind", "Bhopal", "Gwalior", "Indore", "Jabalpur", "Ujjain", "Ajmer", "Chittorgarh", "Jaipur", "Jodhpur", "Kota", "Udaipur", "Bilaspur", "Raigarh", "Raipur", "Ahmedabad", "Bangalore", "Chennai", "Coimbatore", "Hubli", "Kolkata", "Delhi", "Surat");

    $citycode = array("77", "64", "78", "85", "123", "95", "3", "14", "20", "23", "26", "52", "100", "105", "109", "55", "56", "58", "139", "57", "135", "59", "60");

    $dateforlinks = date("d/m/Y", strtotime($filenamedate));

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $url = 'https://epaper.patrika.com/Home/ArticleView?eid=' . $citycode[$edition] . '&edate=' . $dateforlinks;

        // $client = Client::createChromeClient();
        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--user-data-dir=/tmp',
        ]);

        $client->start();
        $client->request('GET', $url);

        $options = $client->getCrawler()->filter('#ddl_Pages option');

        $optionsCount = $options->count();

        $imagelinkarray = [];

        $options->each(function ($option) use (&$imagelinkarray) {
            $imagelink = $option->attr('highres');
            $modifiedHighres = str_replace('_mr', '', $imagelink);
            $imagelinkarray[] = $modifiedHighres;
        });

        $page = 1;
        foreach ($imagelinkarray as $imagelink) {
            if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

            if (!$imageInfo)
                break;

            $getpath = explode("&", makefilepath($epapercode, $cityforfilepath[$edition], $filenamedate, $page, $lang));

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
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;

        $client->quit();
    }
}
