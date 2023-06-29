<?php
if ($epapercode == "PBK") {
    $cityarray = array("RANCHI - City", "PATNA - City", "KOLKATA - City", "JAMSHEDPUR - City", "DHANBAD - City", "DEOGHAR - City", "MUZAFFARPUR - City", "SAMSTIPUR", "BHAGALPUR - City", "GAYA - City", "AURANGABAD", "BOKARO", "BHUBANESWAR");
    $data = getdata("https://epaper.prabhatkhabar.com/");

    $contentarray = explode('<a href="https://epaper.prabhatkhabar.com/r/', $data);
    $codearray = array();
    for ($code = 1; $code < count($contentarray); $code++) {
        $getcode = explode('"', $contentarray[$code])[0];
        $getcity = explode('</h3>', explode("<h3>", $contentarray[$code])[1])[0];
        $codearray[$getcity] = $getcode;
    }

    if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        $city = str_replace(' ', '', $cityarray[$edition]);
        $code = $codearray[$cityarray[$edition]];

        $filenamedate = date("Y-m-d", time());
        $link = "https://epaper.prabhatkhabar.com/" . $code . "/" . $city . "/CITY#page/1/3";
        $client = Client::createChromeClient();
        $client->start();
        setSize($client, $link);

        $response = getdata("https://epaper.prabhatkhabar.com/" . $code . "/" . $city . "/CITY#page/1/3");


        $totalPages = number_format(end(explode("of ", explode('<i class="fa fa-caret-down"',  $response)[0])));
        if (!($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $totalPages)) $no_of_pages_to_run_on_each_edition = $totalPages;
        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $url = "https://epaper.prabhatkhabar.com/" . $code . "/" . $city . "/CITY#page/" . $page . "/3";
            $getpath = explode("&", makefilepath($epapercode, ucwords(explode('-', strtolower($city))[0]), $filenamedate, $page, $lang));
            $outputFile = $getpath[0];

            $client->request('GET', $url);

            $client->executeScript('document.getElementById("top-clips-box").style.display = "block";');

            sleep(3);

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;
            $screenshot = $client->takeScreenshot($outputFile);

            $croppedImage = getCroppedImage($client, 'page-div', $screenshot);
            imagepng($croppedImage, $outputFile);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            // runTesseract($epapername, ucwords(explode('-', strtolower($city))[0]), $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        $client->quit();
    }
}
