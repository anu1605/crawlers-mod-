<?php
if ($epapercode == "NVR") {

    $dateForLinks = date('d-M-Y', strtotime($filenamedate));
    $cityarray = array("mumbai", "nagpur", "nashik", "pune");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < count($cityarray); $edition++) {

        // if ($_REQUEST['city']) {
        //     if (strtolower($cityarray[$edition]) != strtolower($_REQUEST['city'])) continue;
        // }

        echo "Edition: " . $edition . ", " . $cityarray[$edition] . $eol . $eol;

        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            echo "Page: " . $page . $eol . $eol;

            echo $testurl = "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-1/";

            $testcontent = getdata($testurl);

            $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];

            if (!empty($testimagelink)) $imageInfo = @getimagesize($testimagelink);

            if (!$imageInfo)
                break;

            for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {

                echo "Section: " . $section . $eol . $eol;

                echo $link =   "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-" . $section . "/";
                $content = getdata($link);
                // $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                $imagelink = explode('"', explode('src="', explode("ImageArticle", $content)[1])[1])[0];

                if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

                if (!$imageInfo)
                    break;


                $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                // writeImage($imagelink, $getpath[0]);
                writeImageWithCurl($imagelink, $getpath[0]);

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
