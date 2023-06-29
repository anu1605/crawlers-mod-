<?php
    if ($epapercode == "HTV") {

        for ($page = 1; $page < $no_of_pages_to_run_on_each_edition; $page++) {

            $url = 'https://www.ehitavada.com/index.php?edition=NCpage&date=' . $filenamedate . '&page=' . $page;

            $getpath = explode("&", makefilepath($epapercode, "Nagpur", $filenamedate, $page, $lang));

            $outputFile = $getpath[0];

            $client = Client::createChromeClient();

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
                }

                if ($client->getCrawler()->filter('#tour_popup_container')->count() > 0) {
                    $client->executeScript("document.querySelector('#tour_popup_container button').click()");
                }

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
