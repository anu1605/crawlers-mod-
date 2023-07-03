<?php
if ($epapercode == "ND") {

    $dateForLinks = date('d-M-Y', strtotime($filenamedate));
    $cityarray = array("indore", "bhopal", "gwalior", "jabalpur", "raipur", "bilaspur");
    $citycode = array("74", "33", "52", "59", "50", "71");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {

        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        $code = $citycode[$edition];
        $city = $cityarray[$edition];

        // if ($_REQUEST['city']) {
        //     if (strtolower($cityarray[$edition]) != strtolower($_REQUEST['city'])) continue;
        // }

        // if($_REQUEST['city']){
        //     if(strtolower($city)!=strtolower($_REQUEST['city'])) continue;
        // }

        $pageURL = "https://epaper.naidunia.com/epaper/" . $dateForLinks . "-" . $code . "-" . $city . "-edition-" . $city . ".html";
        $response = file_get_contents($pageURL, false, stream_context_create($arrContextOptions));
        $a =  number_format(explode('"', explode('<input type="hidden" name="totalpage" id="totalpage" value="', $response)[1])[0]);
        $array = (explode('<img data-src="',  explode('<div class="slidebox" id="item-zoom1">', $response)[1]));

        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $a) $a = $no_of_pages_to_run_on_each_edition;

        for ($page = 1; $page <= $a; $page++) {
            $imagelink = trim(explode('" title=', $array[$page])[0]);

            $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
    }
}
