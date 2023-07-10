<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Facebook\WebDriver\WebDriverBy;

if ($epapercode == "SAK") {
    $cityarray = array("Ananthapur", "East Godavari", "Kurnool", "SPSR Nellore", "West Godavari", "YSR Kadapa", "Tirupati", "Vijayawada", "Visakhapatnam", "Adilabad", "Greater Warangal", "Mahabubabad", "Hyderabad", "Karimnagar", "Mahabubnagar", "Nalgonda", "Warangal", "Hanamakonda", "Jangaon", "Kamareddy", "Nagarkurnool", "Sangareddy", "Siddipet", "Suryapet");

    $cityforfilepath = array("Ananthapur", "EGodavari", "Kurnool", "Nellore", "WGodavari", "Kadapa", "Tirupati", "Vijayawada", "Visakhapatnam", "Adilabad", "Warangal", "Mahabubabad", "Hyderabad", "Karimnagar", "Mahabubnagar", "Nalgonda", "Warangal", "Hanamakonda", "Jangaon", "Kamareddy", "Nagarkurnool", "Sangareddy", "Siddipet", "Suryapet");

    $citycode = array("6", "23", "52", "77", "114", "119", "89", "97", "104", "192", "157", "180", "122", "171", "183", "190", "210", "158", "162", "168", "189", "142", "144", "206");

    $dateforlinks = date("d/m/Y", strtotime($filenamedate));

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $url = 'https://epaper.sakshi.com/Home/ArticleView?eid=' . $citycode[$edition] . '&edate=' . $dateforlinks;

        // $client = Client::createChromeClient();
        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
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
