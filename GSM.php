<?php


if ($epapercode == "GSM") {

    $dateForLinks = date("d-m-Y", strtotime($filenamedate));
    $cityarray = array("ahmedabad", "baroda", "bhavnagar", "bhuj", "mumbai", "rajkot", "surat");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }


    for ($edition = 0; $edition < count($cityarray); $edition++) {

        $response = file_get_contents("https://epaper.gujaratsamachar.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/1", false, stream_context_create($arrContextOptions));
        $linkArray = explode('<imgclass="img-fluidd-inline-block"src="', str_replace(" ", "", str_replace("\n", "", $response)));


        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) {
            $linkArray = array_slice($linkArray, 1, $no_of_pages_to_run_on_each_edition + 1);
        }

        for ($page = 1; $page <= count($linkArray); $page++) {

            if ($linkArray[$page])
                $imagelink = str_replace("thumbnail/", "", explode('"', $linkArray[$page])[0]);

            $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page, $lang));
            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
}
