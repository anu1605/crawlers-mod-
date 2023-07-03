<?php
    if ($epapercode == "LM") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

            //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
            //     continue;
            // }

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

                $testcontent = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1", false, stream_context_create($arrContextOptions));

                if (!strpos($testcontent, "ArticleImages"))
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $content = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section, false, stream_context_create($arrContextOptions));

                    if (!strpos($content, "ArticleImages"))
                        break;

                    $imagelink = explode("'", explode("src='", $content)[1])[0];


                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    if (empty($imagelink))
                        break;

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                }
                ob_flush();
                flush();
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }
?>