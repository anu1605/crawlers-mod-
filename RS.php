<?php
    if ($epapercode == "RS") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

            //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
            //     continue;
            // }

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = str_replace("md-1", "md-" . $page, str_replace("dateForLinks", $dateForLinks, $linkarray[$edition]));

                if (file_get_contents($imagelink, false, stream_context_create($arrContextOptions))) {


                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                } else break;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }
?>