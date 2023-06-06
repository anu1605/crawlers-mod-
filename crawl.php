<?php

// Set run environment first

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set("display_errors", "1");
set_time_limit(0);

// $eol = "\n";
// if ($_REQUEST['browser'] == "Yes")
$eol = "<br>";

$static_date = ''; // Production Value is ''
$no_of_papers_to_run = 0; // Production Value is 0
$no_of_editions_to_run = 0; // Production Value is 0
$no_of_pages_to_run_on_each_edition = 50; // Production Value is 50
$no_of_sections_to_run_on_each_page = 100; // Production Value is 100

//// code starts below ///

include "/var/www/d78236gbe27823/includes/connect.php";
include "/var/www/d78236gbe27823/marketing/Whatsapp/Crawlers/dependencies/crawl_functions.php";

// Pending Papers: "MM" => "Mysore Mithra,kan";

$epapers = array("AU" => "Amar Ujala,hin", "DC" => "Deccan Chronicle,eng", "HB" => "Hari Bhumi,hin", "DJ" => "Danik Jagran,hin", "JPS" => "Janpath Samachar,hin", "KM" => "Karnataka Malla,kan", "LM" => "Lokmat,mar", "MC" => "Mumbai Chaufer,mar", "NB" => "Navbharat,hin", "NBT" => "Navbharat Times,hin", "ND" => "Nai Dunia,hin", "NVR" => "Navrasthra,mar", "NYB" => "Niyomiya Barta,asm", "PAP" => "Purvanchal Prahari,ori", "RS" => "Rashtriya Sahara,hin", "SAM" => "Sambad,ori", "SMJ" => "Samaja,ori", "SY" => "Samyukta Karnataka,kan", "VV" => "Vijayavani,kan", "YB" => "yashobhumi,hin", "SBP" => "Sangbad Pratidin,ben", "POD" => "Pratidin Odia Daily,ori");  

if ($no_of_papers_to_run > 0 and $no_of_papers_to_run < count($epapers)) $epapers = array_slice($epapers, 0, $no_of_papers_to_run);

$counter = 0;
foreach ($epapers as $epapercode => $epaperArray) {

    echo "<br>" . $epapercode . "=" . ++$counter . "<br>";

    if ($static_date <> '') $filenamedate = $static_date;
    else {
        $filenamedate = filenamedate($epapercode, $conn);
        if ($filenamedate > date('Y-m-d', time())) continue;
    }

    $lang = explode(",", $epaperArray)[1];
    $epapername = explode(",", $epaperArray)[0];
    $dateForLinks = dateForLinks($epapercode, $filenamedate);
    $cityarray = cityArray($epapercode);
    $citycode = cityCodeArray($epapercode);

    if ($cityarray != null)
        if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);


    $citylinkcode = cityCodeArray($epapercode);
    $linkarray = cityCodeArray($epapercode);

    $datecode = dateForLinks($epapercode, $filenamedate);


    if ($epapercode == "AU") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $response = file_get_contents("https://epaper.amarujala.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/01.html?format=img&ed_code=" . $cityarray[$edition]);
            $a = explode('/hdimage.jpg"', $response);
            $b = explode('<link rel="preload" href="', $a[0]);
            $imageLinkPage1 = $b[1] . "/hdimage.jpg";


            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = str_replace("/01/hdimage.jpg", "/" . sprintf('%02d', $page) . "/hdimage.jpg", $imageLinkPage1);


                if (!getimagesize($imagelink)) {
                    break;
                }

                $getpath = explode("&", makefilepath($epapercode, ucwords(explode("-", $cityarray[$edition])[0]), $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "DC") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            $code = $citycode[$edition];
            $city = $cityarray[$edition];

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $testlink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_1.jpg";
                if (!file_get_contents($testlink))
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $imagelink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_" . $section . ".jpg";

                    if (!file_get_contents($imagelink))
                        break;


                    $getpath = explode("&", makefilepath($epapercode, $city, $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "HB") {

        $array = explode(',',  file_get_contents("./dependencies/hb.txt"));

        $datecode = array();
        $newdatecode = array();

        foreach ($array as $val) {
            $codeFromString = explode('=>', $val)[1];
            array_push($datecode, $codeFromString);
        }

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            $code = $datecode[$edition];
            $link = getHBeditionlink($cityarray[$edition], $dateForLinks, $citylinkcode[$edition], $code);

            if (!file_get_contents($link . $code)) {

                $newcode = $code;

                for ($i = 40; $i < 300; $i++) {

                    $newcode = $code + $i;
                    $link = getHBeditionlink($cityarray[$edition], $dateForLinks, $citylinkcode[$edition], $newcode);

                    if (file_get_contents($link . $newcode)) {

                        $code = $newcode;
                        array_push($newdatecode, strval($code));
                        break;
                    } else continue;
                }
            }

            $content = file_get_contents($link . $code);
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
                runTesseract($cityarray[$edition], $page, 0, $conn, $getpath, $lang);
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

    if ($epapercode == "DJ") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $response = file_get_contents("https://epaper.jagran.com/epaper/" . $dateForLinks . $linkarray[$edition] . ".html");
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
                runTesseract($cityarray[$edition], $page + 1, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page + 1 . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "JPS") {

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $imagelink = "https://www.janpathsamachar.com/epaper/janpath/" . $dateForLinks . "/page" . $page . ".jpg";

            if (file_get_contents($imagelink)) {


                $getpath = explode("&", makefilepath($epapercode, "Siliguri", $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract("Siliguri", $page, 0, $conn, $getpath, $lang);
            } else break;
            ob_flush();
            flush();

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
        }
    }

    if ($epapercode == "KM") {

        $filenamedate = date("Y-m-d", strtotime($dateForLinks));

        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $testlink = "http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_1";
            $testcontent = file_get_contents($testlink);
            $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
            $testimageInfo = getimagesize($testimagelink);

            if (!$testimageInfo)
                break;

            for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                $content = file_get_contents("http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_" . $section);

                if ($content) {
                    $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;


                    $getpath = explode("&", makefilepath($epapercode, "Karnataka", $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract("Karnataka", $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
            }
        }
    }

    if ($epapercode == "LM") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

                $testcontent = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1");

                if (!strpos($testcontent, "ArticleImages"))
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $content = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section);

                    if (!strpos($content, "ArticleImages"))
                        break;

                    $imagelink = explode("'", explode("src='", $content)[1])[0];


                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    if (empty($imagelink))
                        break;

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                }
                ob_flush();
                flush();
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "MC") {
        $firsturl = "https://www.mumbaichoufer.com/view/" . $datecode . "/mc";
        $content =   file_get_contents($firsturl);
        $firstId = explode('"', explode('{"mp_id":"', $content)[1])[0];
        $section1 = explode($firstId, $content)[1];
        $sectionArray = explode('{"mp_id":"', $section1);
        $filenamedate = date("Y-m-d", strtotime(trim(explode("- Page 1", explode("Mumbaichoufer -", $content)[1])[0])));

        if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionArray)) {
            $sectionArray = array_slice($sectionArray, 1, $no_of_sections_to_run_on_each_page + 1);
        }

        for ($section = 1; $section <= count($sectionArray); $section++) {
            $imageId = explode('"', $sectionArray[$section])[0];
            if (trim($imageId) == '')
                break;
            $imagelink = "https://www.mumbaichoufer.com/map-image/" . $imageId . ".jpg";


            $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $section, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract("Mumbai", $page, $section, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $section . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }

    // if ($epapercode == "MM") {

    //     $content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra/page/1");
    //     $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));
    //     $imagelinks =   explode('"><img src="', $content);

    //     if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($imagelinks)) $imagelinks = array_slice($imagelinks, 0, $no_of_pages_to_run_on_each_edition + 1);

    //     for ($page = 1; $page <= count($imagelinks); $page++) {
    //         $imagelink = explode('"', explode('"><img src="', $content)[$page])[0];

    // $getpath = explode("&", makefilepath($epapercode, "Mysore", $filenamedate, $page, $lang));

    // if (alreadyDone($getpath[0], $conn) == "Yes") continue;

    // writeImage($imagelink, $getpath[0]);

    // echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
    // runTesseract("Mysore", $page, 0, $conn, $getpath, $lang);
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
    //         ob_flush();
    //         flush();
    //     }
    // }

    if ($epapercode == "NB") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $testcontent = file_get_contents("https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-1/");
                $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];
                $testimageInfo = getimagesize($testimagelink);

                if (!$testimageInfo)
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $link =   "https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-" . $section . "/";
                    $content = file_get_contents($link);
                    $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;


                    $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "NBT") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $code = str_replace("dateForLinks", $filenamedate, $citycode[$edition]);
            $city = $cityarray[$edition];
            $pageURL = "https://epaper.navbharattimes.com/" . $code  . "/page-1.html";
            $content = file_get_contents($pageURL);
            $section1 = explode("class='orgthumbpgnumber'>1</div>", $content)[1];
            $section2 = explode('<div id="rsch"', $section1)[0];
            $pageArray = explode("<div class='imgd'><img src='", $section2);

            if ($no_of_pages_to_run_on_each_edition > 0 && $no_of_pages_to_run_on_each_edition < count($pageArray))
                $pageArray = array_slice($pageArray, 1, $no_of_pages_to_run_on_each_edition + 1);

            for ($page = 1; $page <= count($pageArray); $page++) {
                $imagelink =  str_replace('ss', '', trim(explode("' class='pagethumb'", $pageArray[$page])[0]));

                if (trim($imagelink) == '')
                    break;


                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "ND") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $code = $citycode[$edition];
            $city = $cityarray[$edition];
            $pageURL = "https://epaper.naidunia.com/epaper/" . $dateForLinks . "-" . $code . "-" . $city . "-edition-" . $city . ".html";
            $response = file_get_contents($pageURL);
            $a =  number_format(explode('"', explode('<input type="hidden" name="totalpage" id="totalpage" value="', $response)[1])[0]);
            $array = (explode('<img data-src="',  explode('<div class="slidebox" id="item-zoom1">', $response)[1]));

            if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $a) $a = $no_of_pages_to_run_on_each_edition;

            for ($page = 1; $page <= $a; $page++) {
                $imagelink = trim(explode('" title=', $array[$page])[0]);

                $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "NVR") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $testcontent = file_get_contents("https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-1/");
                $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];
                $imageInfo = getimagesize($testimagelink);

                if (!$imageInfo)
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $link =   "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-" . $section . "/";
                    $content = file_get_contents($link);
                    $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;


                    $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "NYB") {
        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            if ($page == 1)
                $pageName = "index.php";
            else $pageName = "page" . $page . ".php";

            $content = file_get_contents("https://niyomiyabarta.com/epaper/" . $dateForLinks . "/" . $pageName);

            if ($content) {
                $section1 = explode('<map name="Map2"', $content)[1];
                $section2 = explode('</map>', $section1)[0];
                $linkArray = explode("redirectme('", $section2);

                if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($linkArray)) $linkArray = array_slice($linkArray, 1, $no_of_sections_to_run_on_each_page + 1);

                for ($section = 1; $section <= count($linkArray); $section++) {
                    $pageName = explode("',", $linkArray[$section])[0];
                    $imagelink =  "https://niyomiyabarta.com/epaper/" . $dateForLinks . "/images/p" . $page . "/" . $pageName;

                    if (trim($pageName) == '')
                        break;


                    $getpath = explode("&", makefilepath($epapercode, "Guwahati", $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract("Guwahati", $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
            } else break;
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
        }
    }

    if ($epapercode == "PAP") {

        $data = file_get_contents("https://www.glpublications.in/PurvanchalPrahari/Guwahati/" . $dateForLinks . "/Page-2");
        $sectionarray = explode('<div class="clip-container"', $data);

        if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionarray)) $sectionarray = array_slice($sectionarray, 1, $no_of_sections_to_run_on_each_page + 1);

        for ($section = 1; $section <= count($sectionarray); $section++) {
            $imagelink = explode("'", explode("<img src='", $sectionarray[$section])[1])[0];

            if (trim($imagelink) == '')
                break;


            $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $section, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract("Bhubaneswar", $section, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $section . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }

    if ($epapercode == "POD") {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, "https://e2india.com/pratidin/epaper/edition/" . $datecode . "/pratidin-odia-daily");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
        $data = curl_exec($ch);
        curl_close($ch);
        $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $data)[1])[0]));
        $contentArray = explode('</div><img class="" src="', $data);

        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($contentArray)) $contentArray = array_slice($contentArray, 1, $no_of_pages_to_run_on_each_edition + 1);

        for ($page = 1; $page <= count($contentArray); $page++) {
            $imagelink =  str_replace("&", "&amp;", explode('"',  $contentArray[$page])[0]);

            if (trim($imagelink) == '')
                break;


            $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract("Bhubaneswar", $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }

    if ($epapercode == "RS") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = str_replace("md-1", "md-" . $page, str_replace("dateForLinks", $dateForLinks, $linkarray[$edition]));

                if (file_get_contents($imagelink)) {


                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                } else break;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "SAM") {

        $citycode = cityCodeArray("SAM1");
        $imagelinkcitycode = cityCodeArray("SAM2");

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            $link = "https://sambadepaper.com/epaper/1/" . $citycode[$edition] . "/" . $filenamedate . "/1";
            $content = file_get_contents($link);
            $pagearray = explode("id='imgpage_", $content);

            if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($pagearray)) $pagearray = array_slice($pagearray, 1, $no_of_pages_to_run_on_each_edition + 1);

            for ($page = 1; $page <= count($pagearray); $page++) {
                $pageno = explode("'", $pagearray[$page])[0];
                $sectionArray = explode("show_pop('", $pagearray[$page]);

                if (trim($pageno) == '')
                    break;

                if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionArray)) $sectionArray = (array_slice($sectionArray, 1, $no_of_sections_to_run_on_each_page + 1));

                for ($section = 1; $section <=  count($sectionArray); $section++) {
                    $imagelinkid = explode(",", $sectionArray[$section])[1];
                    $imagelink = "https://sambadepaper.com/epaperimages/" . $dateForLinks . "/" . $dateForLinks . "-md-" . $imagelinkcitycode[$edition] . "-" . $pageno . "/" . str_replace("'", "", $imagelinkid) . ".jpg";

                    if (trim($imagelinkid) == '')
                        break;


                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "SBP") {

        $data = file_get_contents("https://epaper.sangbadpratidin.in/epaper/edition/" . $datecode . "/sangbad-pratidin");
        $contentArray = explode('<div class="item">', $data);
        $filenamedate = date("Y-m-d", strtotime(trim(explode('<', explode('p">', $data)[1])[0])));

        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($contentArray)) $contentArray = array_slice($contentArray, 1, $no_of_pages_to_run_on_each_edition + 1);

        for ($page = 1; $page <= count($contentArray); $page++) {
            $imagelink = str_replace('&', '&amp;', explode('"', explode('src="', $contentArray[$page])[1])[0]);

            if (trim($imagelink) == '')
                break;


            $getpath = explode("&", makefilepath($epapercode, "Kolkata", $filenamedate, $page, $lang));

            if (alreadyDone($getpath[0], $conn) == "Yes") continue;

            writeImage($imagelink, $getpath[0]);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract("Kolkata", $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        }
    }

    if ($epapercode == "SMJ") {

        $content = file_get_contents("https://samajaepaper.in/epaper/1/73/" . $filenamedate . "/1");
        $pageArray = explode("id='imgpage_", $content);

        if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($pageArray)) $pageArray = array_slice($pageArray, 0, $no_of_pages_to_run_on_each_edition + 1);

        for ($page = 1; $page <= count($pageArray); $page++) {
            $sectionArray = explode("show_pop('", $pageArray[$page]);

            if ($no_of_sections_to_run_on_each_page > 0 and $no_of_sections_to_run_on_each_page < count($sectionArray)) $sectionArray = array_slice($sectionArray, 1, $no_of_sections_to_run_on_each_page + 1);

            for ($section = 1; $section <= count($sectionArray); $section++) {
                $name = explode("','", $sectionArray[$section])[1];

                if (trim($name) == '')
                    break;

                $pageno = explode("'", $pageArray[$page])[0];
                $imagelink = "https://samajaepaper.in/epaperimages/" . $dateForLinks . "/" . $dateForLinks . "-md-bh-" . $pageno . "/" . $name . ".jpg";


                $getpath = explode("&", makefilepath($epapercode, "Bhubaneswar", $filenamedate, $pageno . "00" . $section, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract("Bhubaneswar", $pageno, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $pageno . " Section " . $section . " Completed" . $eol;
                ob_flush();
                flush();
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $pageno . " Completed" . $eol;
        }
    }

    if ($epapercode == "SY") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $testcontent  = file_get_contents("http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_1");
                $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];
                $testimageInfo = getimagesize($testimagelink);

                if (!$testimageInfo)
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $link =   "http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_" . $section;
                    $content = file_get_contents($link);
                    $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;


                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }

    if ($epapercode == "VV") {

        for ($edition = 0; $edition < count($cityarray); $edition++) {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                if ($page > 1) {
                    $testcontent = file_get_contents("https://epapervijayavani.in/ArticlePage/APpage.php?edn=" . $cityarray[$edition] . "&articleid=VVAANINEW_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1");
                    $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];
                    $testimageInfo = getimagesize($testimagelink);

                    if (!$testimageInfo)
                        break;
                }

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $content = file_get_contents("https://epapervijayavani.in/ArticlePage/APpage.php?edn=" . $cityarray[$edition] . "&articleid=VVAANINEW_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section);

                    if ($content) {
                        $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];
                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo && $section > 3)
                            break;


                        $getpath = explode("&", makefilepath($epapercode, str_replace($cityarray[0], "Bangalore", $cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract(str_replace($cityarray[0], "Bangalore", $cityarray[$edition]), $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    }
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }
    }


    if ($epapercode == "YB") {
        for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
            $testcontent = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $dateForLinks . "_" .  $page . "_1");
            $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
            $testimageInfo = getimagesize($testimagelink);

            if (!$testimageInfo)
                break;

            for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                $content = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $dateForLinks . "_" .  $page . "_" . $section);

                if ($content) {
                    $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        break;


                    $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract("Mumbai", $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
        }
    }
    //exec("rm -f /nvme/*");
    mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('" . $epapername . "','" . $epapercode . "','" . $filenamedate . "')");
}
