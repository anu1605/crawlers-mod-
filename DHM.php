<?php

if ($epapercode == "DHM") {
    $dateForLinks = date('Ymd', strtotime($filenamedate));

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $testcontent = file_get_contents("http://webmilap.com/articlepage.php?articleid=HINDIMIL_HIN_" . $dateForLinks . "_" . $page . "_1", false, stream_context_create($arrContextOptions));
        $testimagelink = explode('"', explode('d="artimg" src="', $testcontent)[1])[0];
        $testimageInfo = getimagesize($testimagelink);

        if (!$testimageInfo and $page > 20)
            break;

        for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
            $content = file_get_contents("http://webmilap.com/articlepage.php?articleid=HINDIMIL_HIN_" . $dateForLinks . "_" .  $page . "_" . $section, false, stream_context_create($arrContextOptions));

            $imagelink = explode('"', explode('d="artimg" src="', $content)[1])[0];
            $imageInfo = getimagesize($imagelink);

            if (!$imageInfo)
                continue;

            $getpath = explode("&", makefilepath($epapercode, "Hyderabad", $filenamedate, $page . "00" . $section, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Mumbai", $page, $section, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
    }
}
