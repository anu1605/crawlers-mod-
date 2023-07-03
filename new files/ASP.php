<?php
    if ($epapercode == "ASP") {
        $content = file_get_contents("https://epaper.asomiyapratidin.in/edition/" . $datecode . "/%E0%A6%85%E0%A6%B8%E0%A6%AE%E0%A7%80%E0%A7%9F%E0%A6%BE-%E0%A6%AA%E0%A7%8D%E0%A6%B0%E0%A6%A4%E0%A6%BF%E0%A6%A6%E0%A6%BF%E0%A6%A8", false, stream_context_create($arrContextOptions));
        $linkArray = explode('"><img src="', $content);
        $filenamedate = date('Y-m-d', strtotime(trim(explode('|', explode('Asomiya Pratidin ePaper :', $content)[1])[0])));
        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) $linkArray = array_slice($linkArray, 0, $no_of_pages_to_run_on_each_edition + 1);

        for ($page = 1; $page <= count($linkArray); $page++) {
            if ($linkArray[$page] == null)
                break;
            $imagelink = str_replace('thumb_150_auto', 'files', explode('"', $linkArray[$page])[0]);
            if (!getimagesize($imagelink))
                break;
            $getpath = explode("&", makefilepath($epapercode, "Guwahati", $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Guwahati", $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }
?>