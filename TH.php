<?php

use Symfony\Component\HttpClient\HttpClient;

if ($epapercode == "TH") {
    $cityarray = array("Bangalore", "Chennai", "Coimbatore", "Delhi", "Erode", "Hyderabad", "Kochi", "Kolkata", "Kozhikode", "Madurai", "Mangalore", "Mumbai", "Thiruvananthapuram", "Tiruchirapalli", "Vijayawada", "Visakhapatnam");

    $dateforlinks = date("Y-m-d", strtotime($filenamedate));

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }


    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $url = 'https://epaper.thehindu.com/ccidist-ws/th//?json=true&fromDate=' . $dateforlinks . '&toDate=' . $dateforlinks . '&skipSections=true&os=web&includePublications=th_' . $cityarray[$edition];

        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
        ]);

        $client->start();
        $client->request('GET', $url);

        $jsonContent = $client->waitFor('body')->getText();
        $data = json_decode($jsonContent, true);

        if (isset($data['publications'][0]['issues']['web'][0]['readerUrl'])) {
            $mainurl = $data['publications'][0]['issues']['web'][0]['readerUrl'];
        } else {
            echo 'Reader URL not found in the JSON response';
        }


        for ($page = 1; $page < $no_of_sections_to_run_on_each_page; $page++) {
            $mainurl = str_replace('page=1', 'page=' . $page, $mainurl);
            $client->request('GET', $mainurl);
            sleep(3);
            $images = $client->getCrawler()->filter('img.page-left-bitmap');
            $imagelink = explode('?rev', $images->attr('src'))[0];
            echo $imagelink;

            $nextButton = $client->waitFor('#next-page-button');

            $nextButtonStyle = $client->executeScript('return document.getElementById("next-page-button").style.display');
            if ($nextButtonStyle == 'none') {
                break;
            }
            $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            // writeImageWithCurl($imagelink, $getpath[0]);
            writeimageusingstring($imagelink, $getpath[0]);
            die();

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;

        $client->quit();
    }
}

function writeimageusingstring($imagelink, $path)
{
    $im = imagecreatefromstring(file_get_contents($imagelink));

    if (empty($im))
        return;

    imagepng($im, $path);
}
