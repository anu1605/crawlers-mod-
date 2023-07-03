<?php
if ($epapercode == "DJ") {

    $dateForLinks = date('d-M-Y', strtotime($filenamedate));
    $cityarray = array("Delhi", "Kanpur", "Patna", "Lucknow", "Varanasi", "Prayagraj", "Gorakhpur", "Agra", "Meerut", "Bhagalpur", "Muzaffarpur");

    if ($cityarray != null) {

        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
    }

    $linkarray = array("-4-Delhi-City-edition-Delhi-City", "-64-Kanpur-edition-Kanpur", "-84-Patna-Nagar-edition-Patna-Nagar", "-11-Lucknow-edition-Lucknow", "-45-Varanasi-City-edition-Varanasi-City", "-79-Prayagraj-City-edition-Prayagraj-City", "-56-Gorakhpur-City-edition-Gorakhpur-City", "-193-Agra-edition-Agra", "-29-Meerut-edition-Meerut", "-205-Bhagalpur-City-edition-Bhagalpur-City", "-203-Muzaffarpur-Nagar-edition-Muzaffarpur-Nagar");

    for ($edition = 0; $edition < count($cityarray); $edition++) {

        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        // if($_REQUEST['city']){
        //         if(strtolower($cityarray[$edition])!=strtolower($_REQUEST['city'])) continue;
        //     }

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

            if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

            if (!$imageInfo)
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
