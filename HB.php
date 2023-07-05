<?php
    if ($epapercode == "HB") {

        $array = explode(',',  file_get_contents("./dependencies/hb.txt", false, stream_context_create($arrContextOptions)));

        $datecode = array();
        $newdatecode = array();

        foreach ($array as $val) {
            $codeFromString = explode('=>', $val)[1];
            array_push($datecode, $codeFromString);
        }

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

            //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
            //     continue;
            // }

            $code = $datecode[$edition];
            $link = getHBeditionlink($cityarray[$edition], $dateForLinks, $citylinkcode[$edition], $code);

            if (!file_get_contents($link . $code, false, stream_context_create($arrContextOptions))) {

                $newcode = $code;

                for ($i = 40; $i < 300; $i++) {

                    $newcode = $code + $i;
                    $link = getHBeditionlink($cityarray[$edition], $dateForLinks, $citylinkcode[$edition], $newcode);

                    if (file_get_contents($link . $newcode, false, stream_context_create($arrContextOptions))) {

                        $code = $newcode;
                        array_push($newdatecode, strval($code));
                        break;
                    } else continue;
                }
            }

            $content = file_get_contents($link . $code, false, stream_context_create($arrContextOptions));
            $section1 = explode('id="slider-epaper" class="imageGalleryWrapper"><li data-index="0"', $content)[1];
            $section2 = explode('class="page-toolbar"><div id="page-level-nav"', $section1)[0];
            $linkArray = explode('data-big="', trim($section2));

            if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) $linkArray = array_slice($linkArray, 1, $no_of_pages_to_run_on_each_edition + 1);

            for ($page = 1; $page <= count($linkArray); $page++) {
                $imagelink =  explode('"', $linkArray[$page])[0];

                if (trim($imagelink) == '')
                    break;

                $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }

        if (count($newdatecode) == 3) {
            $file = fopen("./dependencies/hb.txt", 'w');
            $txt =  "raipur=>" . $newdatecode[0] . ",bilaspur=>" . $newdatecode[1] . ",bhopal=>" . $newdatecode[2] . "";
            fwrite($file, $txt);
            fclose($file);
        }
    }
?>