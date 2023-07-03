<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverDimension;

if ($epapercode == "NGS") {
    $dateForLinks = strtoupper(date("d-F-Y", time()));

    $data = getdata("https://epaper.navgujaratsamay.com/");

    $datecode = explode('"', explode('"https://epaper.navgujaratsamay.com/r/', $data)[1])[0];
    $filenamedate = date("Y-m-d", time());
    $link = "https://epaper.navgujaratsamay.com/" . $datecode . "/Ahmedabad/" . $dateForLinks . "#page/1/3";

    $response = getdata($link);
    $totalPages = number_format(end(explode("of ", explode('<i class="fa fa-caret-down"',  $response)[0])));

    if (!($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $totalPages)) $no_of_pages_to_run_on_each_edition = $totalPages;

    $client = Client::createChromeClient();
    $client->start();
    setSize($client, $link);

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $url = "https://epaper.navgujaratsamay.com/" . $datecode . "/Ahmedabad/" . $dateForLinks . "#page/" . $page . "/3";
        $getpath = explode("&", makefilepath($epapercode, "Ahmedabad", $filenamedate, $page, $lang));
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

    $client->quit();
}
