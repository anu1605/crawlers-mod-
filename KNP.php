<?php
require_once 'vendor/autoload.php';


use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverDimension;


if ($epapercode == "KNP") {

    $filenamedate = date("Y-m-d", time());
    $cityarray = array("Bengaluru", "MYSORE CITY", "Hubballi", "Mangaluru", "DAVANAGERE");
    $linkarray = array("https://kpepaper.asianetnews.com/datecode/DAVANAGERE/DAVANAGERE#page/1/3", "https://kpepaper.asianetnews.com/datecode/Bengaluru/Bengaluru#page/1/3", "https://kpepaper.asianetnews.com/datecode/KANNADA-PRABHA/MYSORE-CITY#page/1/3", "https://kpepaper.asianetnews.com/datecode/Hubballi/Hubballi#page/1/3", "https://kpepaper.asianetnews.com/datecode/Mangaluru/MANGALORE#page/1/3");
    $data = getdata("https://kpepaper.asianetnews.com/");

    $contentarray = explode('https://kpepaper.asianetnews.com/r/', $data);

    $codearray = array();
    for ($code = 1; $code < count($contentarray); $code++) {
        $getcode = explode('"', $contentarray[$code])[0];
        $getcity = explode('</h3>', explode("<h3>", $contentarray[$code])[1])[0];
        $codearray[$getcity] = $getcode;
    }


    if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);

    $client = Client::createChromeClient();
    $client->start();
    $link = str_replace("datecode", $codearray[$cityarray[0]], $linkarray[0]);

    $client->request('GET', $link);


    $window = $client->getWebDriver()->manage()->window();

    $window->setSize(new WebDriverDimension(2500, 3500));

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $city = str_replace(' ', '', $cityarray[$edition]);
        $code = $codearray[$cityarray[$edition]];

        // if ($_REQUEST['city']) {
        //     if (ucwords(explode('-', strtolower($city))[0]) != strtolower($_REQUEST['city'])) continue;
        // }

        $filenamedate = date("Y-m-d", time());
        $link = str_replace("datecode", $code, $linkarray[$edition]);


        $response = getdata($link);


        $totalPages = number_format(trim(end(explode("of ", explode('<i class="fa fa-caret-down"',  $response)[0]))));

        if (!($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $totalPages)) $no_of_pages_to_run_on_each_edition = $totalPages;
        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $url = str_replace("page", $page, str_replace("datecode", $code, $linkarray[$edition]));
            $getpath = explode("&", makefilepath($epapercode, ucwords(explode(' ', strtolower($city))[0]), $filenamedate, $page, $lang));
            $outputFile = $getpath[0];

            $client->request('GET', $url);

            $client->executeScript('document.getElementById("top-clips-box").style.display = "block";');

            sleep(3);

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;
            $screenshot = $client->takeScreenshot($outputFile);

            $croppedImage = getCroppedImage($client, 'page-div', $screenshot);
            imagepng($croppedImage, $outputFile);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, ucwords(explode('-', strtolower($city))[0]), $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        $client->quit();
    }
}
