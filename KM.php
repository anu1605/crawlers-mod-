<?php

if ($epapercode == "KM") {

    if (date("d", strtotime($filenamedate)) == date("d", time())) {
        $dateForLinks = date('Ymd', strtotime($filenamedate) - (24 * 3600));
    } else
        $dateForLinks = date("Ymd", strtotime($filenamedate));

    $filenamedate = date("Y-m-d", strtotime($dateForLinks));

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $testlink = "http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_1";
        $testcontent = file_get_contents($testlink, false, stream_context_create($arrContextOptions));
        $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
        if (!empty($testimagelink)) $imageInfo = @getimagesize($testimagelink);

        if (!$imageInfo)
            break;

        for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
            $content = file_get_contents("http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_" . $section, false, stream_context_create($arrContextOptions));

            if ($content) {
                $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];

                if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

                if (!$imageInfo)
                    break;


                $getpath = explode("&", makefilepath($epapercode, "Karnataka", $filenamedate, $page . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Karnataka", $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
        }
    }
}
