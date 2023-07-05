<?php
    if ($epapercode == "AU") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

            //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
            //     continue;
            // }

            $response = file_get_contents("https://epaper.amarujala.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/01.html?format=img&ed_code=" . $cityarray[$edition], false, stream_context_create($arrContextOptions));
            $a = explode('/hdimage.jpg"', $response);
            $b = explode('<link rel="preload" href="', $a[0]);
            $imageLinkPage1 = $b[1] . "/hdimage.jpg";


            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = str_replace("/01/hdimage.jpg", "/" . sprintf('%02d', $page) . "/hdimage.jpg", $imageLinkPage1);


                if (!getimagesize($imagelink)) {
                    break;
                }

                $getpath = explode("&", makefilepath($epapercode, ucwords(explode("-", $cityarray[$edition])[0]), $filenamedate, $page, $lang));

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