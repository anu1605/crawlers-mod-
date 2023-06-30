<?php
if ($epapercode == "DC") {
    $cityarray = array("Hyderabad", "Vijayawada", "Karimnagar", "Nellore", "Ananthapur");
    $citycode = array("HYD", "VIJ", "KRM", "NEL", "ATP");

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

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $testlink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_1.jpg";
            if (!file_get_contents($testlink, false, stream_context_create($arrContextOptions)))
                break;

            for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                $imagelink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_" . $section . ".jpg";

                if (!file_get_contents($imagelink, false, stream_context_create($arrContextOptions)))
                    break;


                $getpath = explode("&", makefilepath($epapercode, $city, $filenamedate, $page . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
    }
}
