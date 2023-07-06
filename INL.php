<?php
if ($epapercode == "INL") {
    $cityarray = array("Kanpur", "Agra", "Prayagraj", "Bareilly", "Gorakhpur", "Jamshedpur", "Lucknow", "Meerut", "Patna", "Ranchi", "Varanasi", "Dehradun");
    $codearray = array("6", "21", "19", "15", "17", "29", "33", "13", "36", "3", "24", "38");
    // $secondcodearray = array("knp", "agr", "ald", "inb", "ing", "jmd", "lko", "inm", "pat", "rch", "inv", "inm");
    $dateForLinks = date("d-M-Y", strtotime($filenamedate));
    $pagelist = array();

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    for ($edition = 0; $edition < 1; $edition++) {
        $firsturl = "https://epaper.inextlive.com/epaper/" . $dateForLinks . "-" . $codearray[$edition] . "-" . $cityarray[$edition] . "-edition-" . $cityarray[$edition] . "-page-1-page-1.html";

        $content =   @file_get_contents($firsturl, false, stream_context_create($arrContextOptions));
        $secondlinkcode = end(explode('/', explode('-', explode('data-src="https://epaperapi.jagran.com/inextEpaper/', $content)[1])[0]));

        if (strlen($content) == 0) {
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=> Looks like the paper is not yet available for this date. Try later" . $eol;
        } else {
            $contentarray = explode("show_pop('", $content);

            for ($i = 1; $i < count($contentarray); $i++) {
                $idandpagearray = explode("',", $contentarray[$i]);
                $pageid = explode("'", $idandpagearray[1])[1];
                $page = explode(",", $idandpagearray[4])[0];

                $imagelink = "https://epaperapi.jagran.com/inextEpaper/" . date("dmY", strtotime($filenamedate)) . "/" . str_replace("prayagraj", "allahabad", strtolower($cityarray[$edition])) . "/" . $secondlinkcode  . "-pg" . $page . "-0/d" . $pageid . ".png";

                if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

                if (!$imageInfo)
                    continue;

                if (isset($pagelist[$page])) {
                    $pagelist[$page] += 1;
                    $section = $pagelist[$page];
                } else {
                    if ($page > 1)
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page - 1 . " Completed" . $eol;
                    else echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                    $pagelist[$page] = 1;
                    $section = 1;
                }

                if ($page > $no_of_pages_to_run_on_each_edition or $section > $no_of_sections_to_run_on_each_page) {
                    break;
                }
                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;

                ob_flush();
                flush();
            }

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }
}
