<?php
    if ($epapercode == "SOM") {
        $content = file_get_contents("https://epaper.starofmysore.com/epaper/edition/" . $datecode . "/star-mysore/page/1", false, stream_context_create($arrContextOptions));
        $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));
        $linkArray =   explode('"><img src="', $content);

        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) $linkArray = array_slice($linkArray, 0, $no_of_pages_to_run_on_each_edition + 1);

        for ($page = 1; $page <= count($linkArray); $page++) {

            $imagelink = explode('"', explode('"><img src="', $content)[$page])[0];

            if (trim($imagelink) == '')
                break;

            $getpath = explode("&", makefilepath($epapercode, "Mysore", $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Mysore", $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }
?>