<?php
    if ($epapercode == "AP") {
        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $imagelink = "https://epaper.anandabazar.com/epaperimages////" . $dateForLinks . "////" . $dateForLinks . "-md-hr-" . $page . "ll.png";
            if (!getimagesize($imagelink))
                break;

            $getpath = explode("&", makefilepath($epapercode,  "Kolkata", $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Kolkata", $page, $section, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }
?>