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

        foreach ($page) {
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
