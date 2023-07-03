<?php
    if ($epapercode == "DN") {

        $citycode = array();
        $cityarray = array();
        $citycovered = array();

        for ($pagination = 1; $pagination <= 3; $pagination++) {
            $response = file_get_contents("https://epaper.navajyoti.net/archive/date/" . $dateForLinks . "/" . $pagination . "?forcesingle=yes", false, stream_context_create($arrContextOptions));
            $contentarray = explode('<a data-linktype="edition-link" data-cat_ids="', $response);
            for ($i = 1; $i < count($contentarray); $i++) {
                $codecityarray = explode('"', $contentarray[$i]);

                array_push($citycode, str_replace("200x200/", "", $codecityarray[4]));
                array_push($cityarray, end(explode("/", $codecityarray[2])));
            }
        }
        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $city = ucwords(explode("-", $cityarray[$edition])[0]);

            // if (!cityofinterest($city, $cities_of_interest, $eol)) {
            //     continue;
            // }

            $content = file_get_contents("https://epaper.navajyoti.net/view/" . $citycode[$edition] . "/" . $cityarray[$edition] . "", false, stream_context_create($arrContextOptions));

            $link = $citycode[$edition];


            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $pageforfile = $page;

                if ($citycovered[$city]) {
                    $citycovered[$city] += 1;
                    $pageforfile = $citycovered[$city];
                }
                $imagelink = str_replace("01.", sprintf("%02d", $page) . ".", $link);

                if (!getimagesize($imagelink))
                    break;

                echo $imagelink . $eol;
                $getpath = explode("&", makefilepath($epapercode,  $city, $filenamedate, $pageforfile, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, $city, $pageforfile, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $city . " Page " . $pageforfile . " Completed" . $eol;
                ob_flush();
                flush();

                $pageforfile++;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $city . " Completed" . $eol;
            $citycovered[$city] = $pageforfile - 1;
        }
    }
?>