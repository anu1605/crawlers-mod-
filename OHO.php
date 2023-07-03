<?php
    if ($epapercode == "OHO") {

        $dateForLinks = date('Ymd', strtotime($filenamedate));
        
        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $testcontent = file_get_contents("http://epaper.heraldgoa.in/articlepage.php?articleid=OHERALDO_GOA_" . $dateForLinks . "_" . $page . "_1", false, stream_context_create($arrContextOptions));

            $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];

            if (!getimagesize($testimagelink) and $page > 20)
                break;

            for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                $response = file_get_contents("http://epaper.heraldgoa.in/articlepage.php?articleid=OHERALDO_GOA_" . $dateForLinks . "_" . $page . "_" . $section, false, stream_context_create($arrContextOptions));

                $imagelink = explode('"', explode('id="artimg" src="', $response)[1])[0];

                if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

                if (!$imageInfo)
                    break;


                $getpath = explode("&", makefilepath($epapercode,  "Goa", $filenamedate, $page . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Goa", $page, $section, $conn, $getpath, $lang);
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" .  " Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }
