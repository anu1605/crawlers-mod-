<?php

if ($epapercode == "JPS") {

    $dateforlinks = date('dmy', strtotime($filenamedate));
    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $imagelink = "https://www.janpathsamachar.com/epaper/janpath/" . $dateforlinks . "/page" . $page . ".jpg";

        if (file_get_contents($imagelink, false, stream_context_create($arrContextOptions))) {


            $getpath = explode("&", makefilepath($epapercode, "Siliguri", $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Siliguri", $page, 0, $conn, $getpath, $lang);
        } else break;
        ob_flush();
        flush();

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
    }
}
