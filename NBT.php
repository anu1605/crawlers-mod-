<?php

    if ($epapercode == "NBT") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

            //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
            //     continue;
            // }

            $code = str_replace("dateForLinks", $filenamedate, $citycode[$edition]);
            $city = $cityarray[$edition];
            $pageURL = "https://epaper.navbharattimes.com/" . $code  . "/page-1.html";
            $content = file_get_contents($pageURL, false, stream_context_create($arrContextOptions));
            $section1 = explode("class='orgthumbpgnumber'>1</div>", $content)[1];
            $section2 = explode('<div id="rsch"', $section1)[0];
            $pageArray = explode("<div class='imgd'><img src='", $section2);

            if ($no_of_pages_to_run_on_each_edition > 0 && $no_of_pages_to_run_on_each_edition < count($pageArray))
                $pageArray = array_slice($pageArray, 1, $no_of_pages_to_run_on_each_edition + 1);

            for ($page = 1; $page <= count($pageArray); $page++) {
                $imagelink =  str_replace('ss', '', trim(explode("' class='pagethumb'", $pageArray[$page])[0]));

                if (trim($imagelink) == '')
                    break;


                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }
?>