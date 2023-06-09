<?php
    if ($epapercode == "MC") {
        $firsturl = "https://www.mumbaichoufer.com/view/" . $datecode . "/mc";
        $content =   file_get_contents($firsturl, false, stream_context_create($arrContextOptions));
        if (strlen($content) == 0) {
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=> Looks like the paper is not yet available for this date. Try later" . $eol;
        }
        else{
            $firstId = explode('"', explode('{"mp_id":"', $content)[1])[0];
            $section1 = explode($firstId, $content)[1];
            $sectionArray = explode('{"mp_id":"', $section1);
            $filenamedate = date("Y-m-d", strtotime(trim(explode("- Page 1", explode("Mumbaichoufer -", $content)[1])[0])));

            if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionArray)) {
                $sectionArray = array_slice($sectionArray, 1, $no_of_sections_to_run_on_each_page + 1);
            }

            for ($section = 1; $section <= count($sectionArray); $section++) {
                $imageId = explode('"', $sectionArray[$section])[0];
                if (trim($imageId) == '')
                    break;
                $imagelink = "https://www.mumbaichoufer.com/map-image/" . $imageId . ".jpg";


                $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Mumbai", $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $section . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }
    }
?>