<?php

if ($epapercode == "SAM") {

    $dateforlinks = date('dmY', strtotime($filenamedate));
    $cityarray = array("Bhubaneswar", "Cuttack", "Rourkela", "Berhampur");
    $citycode = array("71", "72", "79", "77");
    $imagelinkcitycode = array("hr", "km", "ro", "be");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {

        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        $link = "https://sambadepaper.com/epaper/1/" . $citycode[$edition] . "/" . $filenamedate . "/1";
        $content = file_get_contents($link, false, stream_context_create($arrContextOptions));
        $pagearray = explode("id='imgpage_", $content);

        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($pagearray)) $pagearray = array_slice($pagearray, 1, $no_of_pages_to_run_on_each_edition + 1);

        for ($page = 1; $page < count($pagearray); $page++) {
            $pageno = explode("'", $pagearray[$page])[0];
            $sectionArray = explode("show_pop('", $pagearray[$page]);

            if (trim($pageno) == '')
                break;

            if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionArray)) $sectionArray = (array_slice($sectionArray, 1, $no_of_sections_to_run_on_each_page + 1));

            for ($section = 1; $section <  count($sectionArray); $section++) {
                $imagelinkid = explode(",", $sectionArray[$section])[1];
                $imagelink = "https://sambadepaper.com/epaperimages/" . $dateforlinks . "/" . $dateforlinks . "-md-" . $imagelinkcitycode[$edition] . "-" . $pageno . "/" . str_replace("'", "", $imagelinkid) . ".jpg";

                if (trim($imagelinkid) == '')
                    break;


                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

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
