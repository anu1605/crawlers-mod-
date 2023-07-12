<?php
require  '/var/www/d78236gbe27823/vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

if ($epapercode == "ENDU") {
    $cityarray = array("Guntur", "Anantapur", "Chittoor", "EGodavari", "Eluru", "Kakinada", "Kurnool", "Tirupati", "Visakhapatnam", "WGodavari", "Karnataka", "Odisha", "Tamilnadu", "Adilabad", "Kothagudem", "Hanumakonda", "Hyderabad", "Jagtial", "Kamareddy", "Karimnagar", "Mahabubabad", "Mahabubnagar", "Nagarkurnool", "Nalgonda", "Sangareddy", "Secunderabad", "Siddipet", "Warangal");

    $citycode = array("10", "7", "8", "9", "492", "490", "15", "500", "22", "24", "13", "17", "20", "25", "26", "51", "27", "28", "32", "33", "36", "37", "40", "41", "46", "238", "47", "52");

    $dateforlinks = date("d/m/Y", strtotime($filenamedate));

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $url = 'https://epaper.eenadu.net/Home/Index?date=' . $dateforlinks . '&eid=' . $citycode[$edition];

        // $client = Client::createChromeClient();
        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--user-data-dir=/tmp',
        ]);

        $client->start();
        $client->request('GET', $url);

        $options = $client->getCrawler()->filter('.item > img');


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

            $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

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
