<?php
    if ($epapercode == "DJ") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

            //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
            //     continue;
            // }

            $response = file_get_contents("https://epaper.jagran.com/epaper/" . $dateForLinks . $linkarray[$edition] . ".html", false, stream_context_create($arrContextOptions));
            $a = explode('<ul id="menu-toc" class="menu-toc">', $response);
            $b = explode('</ul>', $a[1]);
            $pagesHTML = $b[0];
            $a = explode('ss.png">', $pagesHTML);

            if ($no_of_pages_to_run_on_each_edition > 0 and  $no_of_pages_to_run_on_each_edition < count($a) - 1) $a = array_slice($a, 0, $no_of_pages_to_run_on_each_edition);
            for ($page = 0; $page <= count($a) - 1; $page++) {
                $b = explode('data-src="', $a[$page]);
                $url = $b[1];
                $url_parts = explode('/', $url);
                $last_part = end($url_parts);
                $modified_last_part = 'M-' . $last_part . '.png';
                $url_parts[count($url_parts) - 1] = $modified_last_part;
                $imagelink = implode('/', $url_parts);

                if (!getimagesize($imagelink))
                    break;


                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page + 1, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, $cityarray[$edition], $page + 1, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page + 1 . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }
?>