<?php

if ($epapercode == "PAP") {

    $dateforlinks = date('d-M-Y', strtotime($filenamedate));

    $data = file_get_contents("https://www.glpublications.in/PurvanchalPrahari/Guwahati/" . $dateforlinks . "/Page-2", false, stream_context_create($arrContextOptions));

    $sectionarray = explode('<div class="clip-container"', $data);

    if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionarray)) $sectionarray = array_slice($sectionarray, 1, $no_of_sections_to_run_on_each_page + 1);

    for ($section = 1; $section < count($sectionarray); $section++) {
        $imagelink = explode("'", explode("<img src='", $sectionarray[$section])[1])[0];

        if (trim($imagelink) == '')
            break;

        $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $section, $lang));

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

        writeImage($imagelink, $getpath[0]);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
        runTesseract($epapername, "Bhubaneswar", $section, 0, $conn, $getpath, $lang);
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $section . " Completed" . $eol;
        ob_flush();
        flush();
    }
}
