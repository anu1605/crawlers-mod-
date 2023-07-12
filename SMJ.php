<?php
if ($epapercode == "SMJ") {

    $dateforlinks = date('dmY', strtotime($filenamedate));
    $content = file_get_contents("https://samajaepaper.in/epaper/1/73/" . $filenamedate . "/1", false, stream_context_create($arrContextOptions));
    $pageArray = explode("id='imgpage_", $content);

    if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($pageArray)) $pageArray = array_slice($pageArray, 0, $no_of_pages_to_run_on_each_edition + 1);

    for ($page = 1; $page < count($pageArray); $page++) {
        $sectionArray = explode("show_pop('", $pageArray[$page]);

        if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionArray)) $sectionArray = array_slice($sectionArray, 1, $no_of_sections_to_run_on_each_page + 1);

        for ($section = 1; $section < count($sectionArray); $section++) {
            $name = explode("','", $sectionArray[$section])[1];

            if (trim($name) == '')
                break;

            $pageno = explode("'", $pageArray[$page])[0];
            $imagelink = "https://samajaepaper.in/epaperimages/" . $dateforlinks . "/" . $dateforlinks . "-md-bh-" . $pageno . "/" . $name . ".jpg";


            $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $pageno . "00" . $section, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Bhubaneswar", $pageno, $section, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $pageno . " Section " . $section . " Completed" . $eol;
            ob_flush();
            flush();
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $pageno . " Completed" . $eol;
    }
}
