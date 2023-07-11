<?php
if ($epapercode = "HT") {
    $cityarray = array("Delhi", "Mumbai", "Chandigarh", "Lucknow", "Patna", "Bengaluru", "Pune", "Gurgaon", "Ludhiana", "Rajasthan", "Amritsar", "Haryana", "Jammu", "Noida", "Punjab", "Patiala", "Jalandhar", "Ranchi", "Thane", "Uttarakhand");

    $dateforlinks = date("d/m/Y", strtotime($filenamedate));

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $url = 'https://epaper.hindustantimes.com/' . $cityarray[$edition] . '?eddate=' . $dateforlinks;

        // $client = Client::createChromeClient();
        $client = \Symfony\Component\Panther\Client::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
        ]);

        $client->start();
        $client->request('GET', $url);
        $client->request('GET', $url);

        $crawler = $client->waitFor('.img_pagename');
        $imagelinkarray = array();
        $crawler->filter('.img_pagename')->each(function ($node) use (&$imagelinkarray) {
            $highres = str_replace('_mr', '', $node->attr('highres'));
            $imagelinkarray[] = $highres;
        });

        $client->quit();

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
