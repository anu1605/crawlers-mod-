<?php
if ($epapercode == "PN") {
    $dateForLinks = date('Ymd', strtotime($filenamedate));
    $cityarray = array("Mumbai", "Pune", "Nashik", "Nagpur", "Kolhapur", "Satara", "Solapur", "Jalgaon", "Dhule", "Nanded", "Thane", "Latur", "Ahmednagar");
    $citycode = array("PM", "PU", "NS", "NAG", "KOL", "STR", "SOL", "JAL", "DHU", "NDD", "PT", "LTR", "AH");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {
        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $testcontent = file_get_contents("http://epunyanagari.com/articlepage.php?articleid=PNAGARI_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf('%02d', $page) . "_1", false, stream_context_create($arrContextOptions));

            $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];

            if (!getimagesize($testimagelink))
                break;

            for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                $response = file_get_contents("http://epunyanagari.com/articlepage.php?articleid=PNAGARI_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf('%02d', $page) . "_" . $section, false, stream_context_create($arrContextOptions));

                $imagelink = explode('"', explode('id="artimg" src="', $response)[1])[0];

                $imageInfo = getimagesize($imagelink);

                if (!$imageInfo)
                    break;


                $getpath = explode("&", makefilepath($epapercode,  $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

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
