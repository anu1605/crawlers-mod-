<?php
require  '/var/www/d78236gbe27823/vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverDimension;

if ($epapercode == "HTV") {

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $url = 'https://www.ehitavada.com/index.php?edition=NCpage&date=' . $filenamedate . '&page=' . $page;

        $getpath = explode("&", makefilepath($epapercode, "Nagpur", $filenamedate, $page, $lang));

        $outputFile = $getpath[0];

        //$client = Client::createChromeClient();

        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--user-data-dir=/tmp',
        ]);

        try {
            $client->start();
            $client->request('GET', $url);
            $client->wait(10, 500)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.map.maphilighted'))
            );
            $client->wait(10, 500)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('img.map.maphilighted'))
            );

            if ($client->getCrawler()->filter('.custom-popup')->count() > 0) {
                $client->executeScript("document.querySelector('.custom-popup span').click()");
                echo $eol . "popup1" . $eol;
            }
            sleep(2);

            if ($client->getCrawler()->filter('#tour_popup_container')->count() > 0) {
                $client->wait(10, 500)->until(
                    WebDriverExpectedCondition::presenceOfElementLocated(
                        WebDriverBy::cssSelector('#tour_popup_container .btn.btn_wide.btn_black')
                    )
                );
                $client->executeScript("document.querySelector('#tour_popup_container .btn.btn_wide.btn_black').click()");
                echo $eol . "popup2" . $eol;
            }

            sleep(2);

            $client->wait(10, 500)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('zoom_btn'))
            );

            $client->executeScript("document.getElementById('zoom_btn').click()");
            $client->wait(10, 500)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.icon_btn.selected'))
            );

            $client->executeScript('window.scrollTo(0, document.body.scrollHeight);');
            $client->executeScript('window.scrollTo(1000, 0);');
            $scrollWidth = $client->executeScript('return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);');
            $scrollHeight = $client->executeScript('return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);');
            $window = $client->getWebDriver()->manage()->window();
            $window->setSize(new WebDriverDimension($scrollWidth, $scrollHeight));
            $client->takeScreenshot($outputFile);
            $window->setSize(new WebDriverDimension(1920, 1080));

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Mumbai", $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        } catch (TimeoutException $e) {
            echo "last page";
            $client->quit();
            exit;
        }
    }
}
