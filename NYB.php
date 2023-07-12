<?php

if ($epapercode == "NYB") {
    $dateforlinks = date('dmY', strtotime($filenamedate));

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        if ($page == 1)
            $pageName = "index.php";
        else $pageName = "page" . $page . ".php";

        $content = file_get_contents("https://niyomiyabarta.com/epaper/" . $dateforlinks . "/" . $pageName, false, stream_context_create($arrContextOptions));

        if ($content) {
            $section1 = explode('<map name="Map2"', $content)[1];
            $section2 = explode('</map>', $section1)[0];
            $linkArray = explode("redirectme('", $section2);

            if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($linkArray)) $linkArray = array_slice($linkArray, 1, $no_of_sections_to_run_on_each_page + 1);

            for ($section = 1; $section < count($linkArray); $section++) {
                $pageName = explode("',", $linkArray[$section])[0];
                $imagelink =  "https://niyomiyabarta.com/epaper/" . $dateforlinks . "/images/p" . $page . "/" . $pageName;

                if (trim($pageName) == '')
                    break;


                $getpath = explode("&", makefilepath($epapercode, "Guwahati", $filenamedate, $page . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Guwahati", $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Section " . $section . " Completed" . $eol;
                ob_flush();
                flush();
            }
        } else break;
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
    }
}
