
<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

$epapers = array("AU" => "Amar Ujala", "DC" => "Deccan Chronicle", "HB" => "Hari Bhumi", "DJ" => "Danik Jagran", "JPS" => "Janpath Samachar", "KM" => "Karnataka Malla", "LM" => "Lokmat", "MC" => "Mumbai Chaufer", "MM" => "Mysore Mithra", "NB" => "Navbharat", "NBT" => "Navbharat Times", "ND" => "Nai Dunia", "NVR" => "Navrasthra", "NYB" => "Niyomiya Barta", "PAP" => "Purvanchal Prahari", "POD" => "Pratidin Odia Daily", "RS" => "Rashtriya Sahara", "SAM" => "Sambad", "SBP" => "Sangbad Pratidin", "SMJ" => "Samaja", "SY" => "Samyukta Karnataka", "VV" => "Vijayavani", "YB" => "yashobhumi");

include dirname(__FILE__) . "/dependencies/crawl_functions.php";

foreach ($epapers as $epapercode => $epapername) {
    $filenamedate = filenamedate($epapercode);

    // if ($epapercode == "AU") {
    //     $lang = "hin";
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);
    //     $cityarray = cityArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         $response = file_get_contents("https://epaper.amarujala.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/01.html?format=img&ed_code=" . $cityarray[$edition]);
    //         $a = explode('/hdimage.jpg"', $response);
    //         $b = explode('<link rel="preload" href="', $a[0]);
    //         $imageLinkPage1 = $b[1] . "/hdimage.jpg";

    //         for ($page = 1; $page <= 50; $page++) {
    //             $imagelink = str_replace("/01/hdimage.jpg", "/" . sprintf('%02d', $page) . "/hdimage.jpg", $imageLinkPage1);

    //             if (!getimagesize($imagelink)) {
    //                 break;
    //             }

    //             $getpath = explode("&", makefilepath($epapercode, ucwords(explode("-", $cityarray[$edition])[0]), $filenamedate, $page, $lang));
    //             writeImage($imagelink, $getpath[0]);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
    //             runTesseract($getpath, $lang);
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }
    // }

    // if ($epapercode == "DC") {
    //     $lang = "eng";
    //     $cityarray = cityArray($epapercode);
    //     $citycode = cityCodeArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {

    //         $code = $citycode[$edition];
    //         $city = $cityarray[$edition];

    //         for ($page = 1; $page <= 50; $page++) {
    //             $testlink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_1.jpg";
    //             if (!file_get_contents($testlink))
    //                 break;

    //             for ($section = 1; $section < 100; $section++) {
    //                 $imagelink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_" . $section . ".jpg";

    //                 if (!file_get_contents($imagelink))
    //                     break;

    //                 $getpath = explode("&", makefilepath($epapercode, $city, $filenamedate, $page . "00" . $section, $lang));
    //                 writeImage($imagelink, $getpath[0]);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
    //                 runTesseract($getpath, $lang);
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }
    // }

    // if ($epapercode == "HB") {
    //     $lang = "hin";
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);
    //     $cityarray = cityArray($epapercode);
    //     $citylinkcode = cityCodeArray($epapercode);
    //     $array = explode(',',  file_get_contents("./dependencies/hb.txt"));

    //     $datecode = array();
    //     $newdatecode = array();

    //     foreach ($array as $val) {
    //         $codeFromString = explode('=>', $val)[1];
    //         array_push($datecode, $codeFromString);
    //     }

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         $code = $datecode[$edition];
    //         $link = getHBeditionlink($cityarray[$edition], $dateForLinks, $citylinkcode[$edition], $code);
    //         if (!file_get_contents($link . $code)) {
    //             $newcode = $code;
    //             for ($i = 40; $i < 300; $i++) {
    //                 $newcode = $code + $i;
    //                 $link = getHBeditionlink($cityarray[$edition], $dateForLinks, $citylinkcode[$edition], $newcode);
    //                 if (file_get_contents($link . $newcode)) {
    //                     $code = $newcode;
    //                     array_push($newdatecode, strval($code));
    //                     break;
    //                 } else continue;
    //             }
    //         }

    //         $content = file_get_contents($link . $code);
    //         $section1 = explode('id="slider-epaper" class="imageGalleryWrapper"><li data-index="0"', $content)[1];
    //         $section2 = explode('class="page-toolbar"><div id="page-level-nav"', $section1)[0];
    //         $linkArray = explode('data-big="', trim($section2));

    //         for ($page = 1; $page < count($linkArray); $page++) {
    //             $imagelink =  explode('"', $linkArray[$page])[0];

    //             $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page, $lang));
    //             writeImage($imagelink, $getpath[0]);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
    //             runTesseract($getpath, $lang);
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     if (count($newdatecode) == 3) {
    //         $file = fopen("./dependencies/hb.txt", 'w');
    //         $txt =  "raipur=>" . $newdatecode[0] . ",bilaspur=>" . $newdatecode[1] . ",bhopal=>" . $newdatecode[2] . "";
    //         fwrite($file, $txt);
    //         fclose($file);
    //     }
    // }

    if ($epapercode == "DJ") {
        $lang = "hin";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray = cityArray($epapercode);
        $linkarray = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $response = file_get_contents("https://epaper.jagran.com/epaper/" . $dateForLinks . $linkarray[$edition] . ".html");
            $a = explode('<ul id="menu-toc" class="menu-toc">', $response);
            $b = explode('</ul>', $a[1]);
            $pagesHTML = $b[0];
            $a = explode('ss.png">', $pagesHTML);

            for ($page = 0; $page < count($a) - 1; $page++) {
                $b = explode('data-src="', $a[$page]);
                $url = $b[1];
                $url_parts = explode('/', $url);
                $last_part = end($url_parts);
                $modified_last_part = 'M-' . $last_part . '.png';
                $url_parts[count($url_parts) - 1] = $modified_last_part;
                $imagelink = implode('/', $url_parts);

                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page + 1, $lang));
                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                runTesseract($getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page + 1 . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "JPS") {
        $lang = "hin";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);

        for ($page = 1; $page <= 50; $page++) {
            $imagelink = "https://www.janpathsamachar.com/epaper/janpath/" . $dateForLinks . "/page" . $page . ".jpg";

            if (file_get_contents($imagelink)) {
                $getpath = explode("&", makefilepath($epapercode, "Siliguri", $filenamedate, $page, $lang));
                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                runTesseract($getpath, $lang);
            } else break;

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed\n";
        }
    }

    if ($epapercode == "KM") {
        $lang = "kan";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $filenamedate = date("Y-m-d", strtotime($dateForLinks));

        for ($page = 1; $page < 50; $page++) {
            $testlink = "http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_1";
            $testcontent = file_get_contents($testlink);
            $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
            $testimageInfo = getimagesize($testimagelink);

            if (!$testimageInfo)
                break;

            for ($section = 1; $section < 100; $section++) {
                $content = file_get_contents("http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_" . $section);

                if ($content) {
                    $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;

                    $getpath = explode("&", makefilepath($epapercode, "Karnataka", $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed\n";
            }
        }
    }

    if ($epapercode == "LM") {
        $lang = "mar";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray  = cityArray($epapercode);
        $citycode = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page < 50; $page++) {
                $testcontent = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1");

                if (!strpos($testcontent, "ArticleImages"))
                    break;

                for ($section = 1; $section < 100; $section++) {
                    $content = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section);

                    if (!strpos($content, "ArticleImages"))
                        break;

                    $imagelink = explode("'", explode("src='", $content)[1])[0];
                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    if (empty($imagelink))
                        break;

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "MC") {
        $lang = "mar";
        $datecode = dateForLinks($epapercode, $filenamedate);
        $content =   file_get_contents("https://www.mumbaichoufer.com/view/" . $datecode . "/mc");
        $firstId = explode('"', explode('{"mp_id":"', $content)[1])[0];
        $section1 = explode($firstId, $content)[1];
        $sectionArray = explode('{"mp_id":"', $section1);
        $filenamedate = date("Y-m-d", strtotime(trim(explode("- Page 1", explode("Mumbaichoufer -", $content)[1])[0])));

        for ($section = 1; $section < count($sectionArray) - 1; $section++) {
            $imageId = explode('"', $sectionArray[$section])[0];
            $imagelink = "https://www.mumbaichoufer.com/map-image/" . $imageId . ".jpg";

            $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $section, $lang));
            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
            runTesseract($getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $section . " Completed\n";
        }
    }

    // if ($epapercode == "MM") {
    //     $lang = "kan";
    //     $datecode = dateForLinks($epapercode, $filenamedate);
    //     $content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra/page/1");
    //     $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));
    //     $imagelinks =   explode('"><img src="', $content);

    //     for ($page = 1; $page < count($imagelinks); $page++) {
    //         $imagelink = explode('"', explode('"><img src="', $content)[$page])[0];

    //         $getpath = explode("&", makefilepath($epapercode, "Mysore", $filenamedate, $page, $lang));
    //         writeImage($imagelink, $getpath[0]);

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
    //         runTesseract($getpath, $lang);
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed\n";
    //     }
    // }

    if ($epapercode == "NB") {
        $lang = "hin";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray  =  cityArray($epapercode);
        $citycode = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page < 50; $page++) {
                $testcontent = file_get_contents("https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-1/");
                $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];
                $testimageInfo = getimagesize($testimagelink);

                if (!$testimageInfo)
                    break;

                for ($section = 1; $section < 100; $section++) {
                    $link =   "https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-" . $section . "/";
                    $content = file_get_contents($link);
                    $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;

                    $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "NBT") {
        $lang = "hin";
        $cityarray = cityArray($epapercode);
        $citycode = cityCodeArray($epapercode);

        for ($edition = 0; $edition < 1; $edition++) {
            $code = str_replace("dateForLinks", $filenamedate, $citycode[$edition]);
            $city = $cityarray[$edition];
            $pageURL = "https://epaper.navbharattimes.com/" . $code  . "/page-1.html";
            $content = file_get_contents($pageURL);
            $section1 = explode("class='orgthumbpgnumber'>1</div>", $content)[1];
            $section2 = explode('<div id="rsch"', $section1)[0];
            $linkArray = explode("<div class='imgd'><img src='", $section2);

            for ($link = 1; $link < 5; $link++) {
                $imagelink =  str_replace('ss', '', trim(explode("' class='pagethumb'", $linkArray[$link])[0]));

                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $link, $lang));
                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                runTesseract($getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $link . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "ND") {
        $lang = "hin";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray = cityArray($epapercode);
        $citycode = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $code = $citycode[$edition];
            $city = $cityarray[$edition];
            $pageURL = "https://epaper.naidunia.com/epaper/" . $dateForLinks . "-" . $code . "-" . $city . "-edition-" . $city . ".html";
            $response = file_get_contents($pageURL);
            $a =  number_format(explode('"', explode('<input type="hidden" name="totalpage" id="totalpage" value="', $response)[1])[0]);
            $array = (explode('<img data-src="',  explode('<div class="slidebox" id="item-zoom1">', $response)[1]));

            for ($page = 1; $page <= $a; $page++) {
                $imagelink = trim(explode('" title=', $array[$page])[0]);

                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));
                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                runTesseract($getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "NVR") {
        $lang = "mar";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray  =  cityArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page < 50; $page++) {
                $testcontent = file_get_contents("https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-1/");
                $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];
                $imageInfo = getimagesize($testimagelink);

                if (!$imageInfo)
                    break;

                for ($section = 1; $section < 100; $section++) {
                    $link =   "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-" . $section . "/";
                    $content = file_get_contents($link);
                    $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;

                    $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "NYB") {
        $lang = "asm";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);

        for ($page = 1; $page <= 50; $page++) {
            if ($page == 1)
                $page = "index.php";
            else $page = "page" . $page . ".php";

            $content = file_get_contents("https://niyomiyabarta.com/epaper/" . $dateForLinks . "/" . $page);

            if ($content) {
                $section1 = explode('<map name="Map2"', $content)[1];
                $section2 = explode('</map>', $section1)[0];
                $linkArray = explode("redirectme('", $section2);

                for ($section = 1; $section < count($linkArray); $section++) {
                    $pageName = explode("',", $linkArray[$section])[0];
                    $imagelink =  "https://niyomiyabarta.com/epaper/" . $dateForLinks . "/images/p" . $page . "/" . $pageName;

                    $getpath = explode("&", makefilepath($epapercode, "Guwahati", $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Section " . $section . " Completed\n";
                }
            } else break;
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
        }
    }

    if ($epapercode == "PAP") {
        $lang = "ori";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $data = file_get_contents("https://www.glpublications.in/PurvanchalPrahari/Guwahati/" . $dateForLinks . "/Page-2");
        $sectionarray = explode('<div class="clip-container"', $data);

        for ($section = 1; $section < count($sectionarray); $section++) {
            $link = explode("'", explode("<img src='", $sectionarray[$section])[1])[0];

            $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $section, $lang));
            writeImage($link, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
            runTesseract($getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $section . " Completed\n";
        }
    }

    if ($epapercode == "POD") {
        $lang = "ori";
        $datecode = dateForLinks($epapercode, $filenamedate);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, "https://e2india.com/pratidin/epaper/edition/" . $datecode . "/pratidin-odia-daily");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
        $data = curl_exec($ch);
        curl_close($ch);
        $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $data)[1])[0]));
        $contentArray = explode('</div><img class="" src="', $data);

        for ($page = 1; $page < count($contentArray); $page++) {
            $imagelink =  str_replace("&", "&amp;", explode('"',  $contentArray[$page])[0]);

            $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $page, $lang));
            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
            runTesseract($getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed\n";
        }
    }

    if ($epapercode == "RS") {
        $lang = "hin";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray = cityArray($epapercode);
        $linkarray = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page < 50; $page++) {
                $imagelink = str_replace("md-1", "md-" . $page, str_replace("dateForLinks", $dateForLinks, $linkarray[$edition]));

                if (file_get_contents($imagelink)) {
                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
                }
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "SAM") {
        $lang = "ori";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray = cityArray($epapercode);
        $citycode = cityCodeArray("SAM1");
        $imagelinkcitycode = cityCodeArray("SAM2");

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $link = "https://sambadepaper.com/epaper/1/" . $citycode[$edition] . "/" . $filenamedate . "/1";
            $content = file_get_contents($link);
            $pagearray = explode("id='imgpage_", $content);

            for ($page = 1; $page < count($pagearray); $page++) {
                $pageno = explode("'", $pagearray[$page])[0];
                $sectionArray = explode("show_pop('", $pagearray[$page]);

                for ($section = 1; $section < count($sectionArray); $section++) {
                    $imagelinkid = explode(",", $sectionArray[$section])[1];
                    $imagelink = "https://sambadepaper.com/epaperimages/" . $dateForLinks . "/" . $dateForLinks . "-md-" . $imagelinkcitycode[$edition] . "-" . $pageno . "/" . str_replace("'", "", $imagelinkid) . ".jpg";

                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "SBP") {
        $lang = "ben";
        $datecode = dateForLinks($epapercode, $filenamedate);
        $data = file_get_contents("https://epaper.sangbadpratidin.in/epaper/edition/" . $datecode . "/sangbad-pratidin");
        $contentArray = explode('<div class="item">', $data);
        $filenamedate = date("Y-m-d", strtotime(trim(explode('<', explode('p">', $data)[1])[0])));

        for ($page = 1; $page < count($contentArray); $page++) {
            $imagelink = str_replace('&', '&amp;', explode('"', explode('src="', $contentArray[$page])[1])[0]);

            $getpath = explode("&", makefilepath($epapercode, "Kolkata", $filenamedate, $page, $lang));
            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
            runTesseract($getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed\n";
        }
    }

    if ($epapercode == "SMJ") {
        $lang = "ori";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $content = file_get_contents("https://samajaepaper.in/epaper/1/73/" . $filenamedate . "/1");
        $pageArray = explode("class='map", $content);

        for ($page = 1; $page < count($pageArray); $page++) {
            $sectionArray = explode("show_pop('", $pageArray[$page]);

            for ($section = 1; $section < count($sectionArray); $section++) {
                $name = explode("','", $sectionArray[$section])[1];
                $link = "https://samajaepaper.in/epaperimages/" . $dateForLinks . "/" . $dateForLinks . "-md-bh-" . $page . "/" . $name . ".jpg";

                $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $page . "00" . $section, $lang));
                writeImage($link, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                runTesseract($getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
        }
    }

    if ($epapercode == "SY") {
        $lang = "kan";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityArray  =  cityArray($epapercode);
        $citycode = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityArray); $edition++) {
            for ($page = 1; $page < 50; $page++) {
                $testcontent  = file_get_contents("http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_1");
                $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];
                $testimageInfo = getimagesize($testimagelink);

                if (!$testimageInfo)
                    break;

                for ($section = 1; $section < 100; $section++) {
                    $link =   "http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_" . $section;
                    $content = file_get_contents($link);
                    $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;

                    $getpath = explode("&", makefilepath($epapercode, $cityArray[$edition], $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "VV") {
        $lang = "kan";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);
        $cityarray  =  cityArray($epapercode);
        $citycode = cityCodeArray($epapercode);

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page < 50; $page++) {
                if ($page > 1) {
                    $testcontent = file_get_contents("https://epapervijayavani.in/ArticlePage/APpage.php?edn=" . $cityarray[$edition] . "&articleid=VVAANINEW_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1");
                    $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];
                    $testimageInfo = getimagesize($testimagelink);

                    if (!$testimageInfo)
                        break;
                }

                for ($section = 1; $section < 100; $section++) {
                    $content = file_get_contents("https://epapervijayavani.in/ArticlePage/APpage.php?edn=" . $cityarray[$edition] . "&articleid=VVAANINEW_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section);

                    if ($content) {
                        $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];
                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo && $section > 3)
                            break;

                        $getpath = explode("&", makefilepath($epapercode, str_replace($cityarray[0], "Bangalore", $cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));
                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                        runTesseract($getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
                    }
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
        }
    }

    if ($epapercode == "YB") {
        $lang = "hin";
        $dateForLinks = dateForLinks($epapercode, $filenamedate);

        for ($page = 1; $page < 50; $page++) {
            for ($section = 1; $section < 100; $section++) {
                $content = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $dateForLinks . "_" .  $page . "_" . $section);

                if ($content) {
                    $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;
                    $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $page . "00" . $section, $lang));
                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved\n";
                    runTesseract($getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
                }
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
        }
    }
    exec("rm -f ./nvme/*");
    // mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('" . $epapername . "','" . $epapercode . "','" . $filenamedate . "')");
}
?>