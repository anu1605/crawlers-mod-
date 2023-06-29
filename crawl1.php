    <?php
    // som - date for links, and main code, name in $epapers
    require_once 'vendor/autoload.php';

    use Symfony\Component\Panther\Client;
    use Facebook\WebDriver\WebDriverBy;
    use Facebook\WebDriver\WebDriverExpectedCondition;
    use Facebook\WebDriver\Exception\TimeoutException;
    use Facebook\WebDriver\WebDriverDimension;

    // Set run environment first

    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
    ini_set("display_errors", "1");
    set_time_limit(0);

    $check_if_extract_running = shell_exec("ps aux | grep extractPhoneNumber.php");
    $extract_runtimes = explode("extractPhoneNumber.php", $check_if_extract_running);
    $extract_howmanyruns = count($extract_runtimes);

    $check_if_crawl_running = shell_exec("ps aux | grep crawl.php");
    $crawl_runtimes = explode("crawl.php", $check_if_crawl_running);
    $howmanyruns_of_crawl = count($crawl_runtimes);

    if ($extract_howmanyruns > 3 or $howmanyruns_of_crawl > 5) die("Other related programmes are running");

    if (php_sapi_name() == "cli") $eol = "\n";
    else  $eol = "<br>";

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $static_date = ''; // Production Value is ''
    $no_of_papers_to_run = 0; // Production Value is 0
    $no_of_editions_to_run = 1; // Production Value is 0
    $no_of_pages_to_run_on_each_edition = 50; // Production Value is 50
    $no_of_sections_to_run_on_each_page = 100; // Production Value is 100

    //// code starts below ///

    // include "/var/www/d78236gbe27823/includes/connect.php";
    // include "/var/www/d78236gbe27823/marketing/Whatsapp/Crawlers/dependencies/crawl_functions.php";
    include "./dependencies/crawl_functions1.php";

    //$epapers = array("AU" => "Amar Ujala,hin", "DC" => "Deccan Chronicle,eng", "HB" => "Hari Bhumi,hin", "DJ" => "Danik Jagran,hin", "JPS" => "Janpath Samachar,hin", "KM" => "Karnataka Malla,kan", "LM" => "Lokmat,mar", "MC" => "Mumbai Chaufer,mar", "NB" => "Navbharat,hin", "NBT" => "Navbharat Times,hin", "ND" => "Nai Dunia,hin", "NVR" => "Navrasthra,mar", "NYB" => "Niyomiya Barta,asm", "PAP" => "Purvanchal Prahari,ori", "RS" => "Rashtriya Sahara,hin", "SAM" => "Sambad,ori", "SMJ" => "Samaja,ori", "SY" => "Samyukta Karnataka,kan", "VV" => "Vijayavani,kan", "YB" => "yashobhumi,hin", "SBP" => "Sangbad Pratidin,ben", "POD" => "Pratidin Odia Daily,ori","MM" => "Mysore Mithra,kan");  

    $epapers = array("HTV" => "Hitavada,eng");
    // $epapers = array( "AU" => "Amar Ujala,hin", "DC" => "Deccan Chronicle,eng", "HB" => "Hari Bhumi,hin", "DJ" => "Danik Jagran,hin", "LM" => "Lokmat,mar", "MC" => "Mumbai Chaufer,mar", "NB" => "Navbharat,hin", "NBT" => "Navbharat Times,hin", "ND" => "Nai Dunia,hin", "RS" => "Rashtriya Sahara,hin", "YB" => "yashobhumi,hin", "NVR" => "Navrasthra,mar", "GSM" => "Gujarat Samachar,guj", "PN" => "Punayanagri,mar", "TOI" => "Times of India,eng", "ET" => "Economic Times,eng", "MT" => "Maharashtra Times,eng", "Mirror" => "Mirror,eng", "DN" => "Dainik Navjyoti,hin","DST" => "Dainik Savera times,hin","SOM" => "Star of Mysore,kan","NHT" => "Nav Hind Times,eng","OHO" => "O Heral O,eng","AP" => "Anandabazar Patrika,ben","ASP" => "Asomiya Pratidin,asm","BS" => "Bombay Samachar,guj","DHM" => "Daily Hindi Milap,hin","DNS" => "Danik Sambad,ben","ESM" => "EiSamay,ben","HTV" => "Hitavada,eng","NGS" => "Nav Gujarat Samay,guj","PBK" => "Prabhat Khabar,hin");

    $cities_of_interest = array("Delhi", "Jaipur", "Jodhpur", "Udaipur", "Kota", "Bhopal", "Ahmedabad", "Surat", "Vadodara", "Bhavnagar", "Rajkot", "Mumbai", "Pune", "Thane", "Nashik");

    if ($no_of_papers_to_run > 0 and $no_of_papers_to_run < count($epapers)) $epapers = array_slice($epapers, 0, $no_of_papers_to_run);

    $counter = 0;
    foreach ($epapers as $epapercode => $epaperArray) {

        echo $eol . $epapercode . "=" . ++$counter . $eol;

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

        if ($cityarray != null) {

            if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
        }

        $citylinkcode = cityCodeArray($epapercode);
        $linkArray = cityCodeArray($epapercode);

        $datecode = dateForLinks($epapercode, $filenamedate);

        if ($epapercode == "AU") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                $response = file_get_contents("https://epaper.amarujala.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/01.html?format=img&ed_code=" . $cityarray[$edition], false, stream_context_create($arrContextOptions));
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
                    runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                }

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "DC") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                $code = $citycode[$edition];
                $city = $cityarray[$edition];

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                    $testlink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_1.jpg";
                    if (!file_get_contents($testlink, false, stream_context_create($arrContextOptions)))
                        break;

                    for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                        $imagelink = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_" . $section . ".jpg";

                        if (!file_get_contents($imagelink, false, stream_context_create($arrContextOptions)))
                            break;


                        $getpath = explode("&", makefilepath($epapercode, $city, $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
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

        if ($epapercode == "DJ") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                $response = file_get_contents("https://epaper.jagran.com/epaper/" . $dateForLinks . $linkArray[$edition] . ".html", false, stream_context_create($arrContextOptions));
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

        if ($epapercode == "MC") {
            $firsturl = "https://www.mumbaichoufer.com/view/" . $datecode . "/mc";
            $content =   file_get_contents($firsturl, false, stream_context_create($arrContextOptions));
            if (strlen($content) == 0) {
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=> Looks like the paper is not yet available for this date. Try later" . $eol;
                continue;
            }
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
                runTesseract($epapername, "Mumbai", $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $section . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }

        if ($epapercode == "NB") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                echo $cityarray[$edition] . "\n";

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

                    $nbpageurl = "https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-1/";

                    $testcontent = file_get_contents($nbpageurl, false, stream_context_create($arrContextOptions));

                    $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];

                    $testimageInfo = getimagesize($testimagelink);

                    if (!$testimageInfo)
                        break;

                    for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                        $link =   "https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-" . $section . "/";
                        $content = file_get_contents($link, false, stream_context_create($arrContextOptions));
                        $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo)
                            break;


                        $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
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

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                $code = str_replace("dateForLinks", $filenamedate, $citycode[$edition]);
                $city = $cityarray[$edition];
                $pageURL = "https://epaper.navbharattimes.com/" . $code  . "/page-1.html";
                $content = file_get_contents($pageURL, false, stream_context_create($arrContextOptions));
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
                    runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "ND") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                $code = $citycode[$edition];
                $city = $cityarray[$edition];
                $pageURL = "https://epaper.naidunia.com/epaper/" . $dateForLinks . "-" . $code . "-" . $city . "-edition-" . $city . ".html";
                $response = file_get_contents($pageURL, false, stream_context_create($arrContextOptions));
                $a =  number_format(explode('"', explode('<input type="hidden" name="totalpage" id="totalpage" value="', $response)[1])[0]);
                $array = (explode('<img data-src="',  explode('<div class="slidebox" id="item-zoom1">', $response)[1]));

                if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $a) $a = $no_of_pages_to_run_on_each_edition;

                for ($page = 1; $page <= $a; $page++) {
                    $imagelink = trim(explode('" title=', $array[$page])[0]);

                    $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "NVR") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                echo "Edition: " . $edition . ", " . $cityarray[$edition] . $eol . $eol;

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

                    echo "Page: " . $page . $eol . $eol;

                    echo $testurl = "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-1/";

                    $testcontent = file_get_contents($testurl, false, stream_context_create($arrContextOptions));

                    $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];
                    $imageInfo = getimagesize($testimagelink);

                    if (!$imageInfo)
                        break;

                    for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {

                        echo "Section: " . $section . $eol . $eol;

                        echo $link =   "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-" . $section . "/";
                        $content = file_get_contents($link, false, stream_context_create($arrContextOptions));
                        $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo)
                            break;


                        $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    }
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "RS") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                    $imagelink = str_replace("md-1", "md-" . $page, str_replace("dateForLinks", $dateForLinks, $linkArray[$edition]));

                    if (file_get_contents($imagelink, false, stream_context_create($arrContextOptions))) {


                        $getpath = explode("&", makefilepath($epapercode, $cityarray[$edition], $filenamedate, $page, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                        ob_flush();
                        flush();
                    } else break;
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "YB") {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $testcontent = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $dateForLinks . "_" .  $page . "_1", false, stream_context_create($arrContextOptions));
                $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
                $testimageInfo = getimagesize($testimagelink);

                if (!$testimageInfo)
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $content = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $dateForLinks . "_" .  $page . "_" . $section, false, stream_context_create($arrContextOptions));

                    if ($content) {
                        $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo)
                            break;


                        $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, "Mumbai", $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    }
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
            }
        }

        if ($epapercode == "GSM") {
            for ($edition = 0; $edition < count($cityarray); $edition++) {
                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }
                $response = file_get_contents("https://epaper.gujaratsamachar.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/1", false, stream_context_create($arrContextOptions));
                $linkArray = explode('<imgclass="img-fluidd-inline-block"src="', str_replace(" ", "", str_replace("\n", "", $response)));

                if ($no_of_pages_to_run_on_each_edition < 0 and $no_of_pages_to_run_on_each_edition > count($linkArray)) $linkArray = array_slice($linkArray, 1, $no_of_pages_to_run_on_each_edition + 1);

                for ($page = 1; $page <= count($linkArray); $page++) {
                    if ($linkArray[$page])
                        $imagelink = str_replace("thumbnail/", "", explode('"', $linkArray[$page])[0]);

                    $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page, $lang));
                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
            }
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
        }

        if ($epapercode == "PN") {

            for ($edition = 0; $edition < count($cityarray); $edition++) {
                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                    $testcontent = file_get_contents("http://epunyanagari.com/articlepage.php?articleid=PNAGARI_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf('%02d', $page) . "_1", false, stream_context_create($arrContextOptions));

                    $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];

                    if (!getimagesize($testimagelink))
                        break;

                    for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                        $response = file_get_contents("http://epunyanagari.com/articlepage.php?articleid=PNAGARI_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf('%02d', $page) . "_" . $section, false, stream_context_create($arrContextOptions));

                        $imagelink = explode('"', explode('id="artimg" src="', $response)[1])[0];

                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo)
                            break;


                        $getpath = explode("&", makefilepath($epapercode,  $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    }
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "TOI" or $epapercode == "ET" or $epapercode == "MT" or $epapercode == "Mirror" or $epapercode ==  "ESM") {
            crawltoi($cityarray, $dateForLinks, $epapercode, $citycode, $filenamedate, $eol, $conn, $lang, $cities_of_interest, $epapername);
        }

        if ($epapercode == "DN") {

            $citycode = array();
            $cityarray = array();
            $citycovered = array();

            for ($pagination = 1; $pagination <= 3; $pagination++) {
                $response = file_get_contents("https://epaper.navajyoti.net/archive/date/" . $dateForLinks . "/" . $pagination . "?forcesingle=yes", false, stream_context_create($arrContextOptions));
                $contentarray = explode('<a data-linktype="edition-link" data-cat_ids="', $response);
                for ($i = 1; $i < count($contentarray); $i++) {
                    $codecityarray = explode('"', $contentarray[$i]);

                    array_push($citycode, str_replace("200x200/", "", $codecityarray[4]));
                    array_push($cityarray, end(explode("/", $codecityarray[2])));
                }
            }
            for ($edition = 0; $edition < count($cityarray); $edition++) {
                $city = ucwords(explode("-", $cityarray[$edition])[0]);

                // if (!cityofinterest($city, $cities_of_interest, $eol)) {
                //     continue;
                // }

                $content = file_get_contents("https://epaper.navajyoti.net/view/" . $citycode[$edition] . "/" . $cityarray[$edition] . "", false, stream_context_create($arrContextOptions));

                $link = $citycode[$edition];


                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                    $pageforfile = $page;

                    if ($citycovered[$city]) {
                        $citycovered[$city] += 1;
                        $pageforfile = $citycovered[$city];
                    }
                    $imagelink = str_replace("01.", sprintf("%02d", $page) . ".", $link);

                    if (!getimagesize($imagelink))
                        break;

                    echo $imagelink . $eol;
                    $getpath = explode("&", makefilepath($epapercode,  $city, $filenamedate, $pageforfile, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, $city, $pageforfile, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $city . " Page " . $pageforfile . " Completed" . $eol;
                    ob_flush();
                    flush();

                    $pageforfile++;
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $city . " Completed" . $eol;
                $citycovered[$city] = $pageforfile - 1;
            }
        }

        if ($epapercode == "DST") {
            for ($edition = 0; $edition < count($cityarray); $edition++) {

                echo "Edition: " . $edition . ", " . $cityarray[$edition] . $eol . $eol;

                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

                    echo "Page: " . $page . $eol . $eol;


                    echo $testurl = "https://epaper.dainiksaveratimes.in/articlepage.php?articleid=DSTIME_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1";

                    $testcontent = file_get_contents($testurl, false, stream_context_create($arrContextOptions));

                    $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];
                    $imageInfo = getimagesize($testimagelink);

                    if (!$imageInfo)
                        break;

                    for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {

                        echo "Section: " . $section . $eol . $eol;

                        echo $link =   "https://epaper.dainiksaveratimes.in/articlepage.php?articleid=DSTIME_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section;
                        $content = file_get_contents($link, false, stream_context_create($arrContextOptions));
                        $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];
                        $imageInfo = getimagesize($imagelink);

                        if (!$imageInfo)
                            break;


                        $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page . "00" . $section, $lang));

                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    }
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
            }
        }

        if ($epapercode == "SOM") {
            $content = file_get_contents("https://epaper.starofmysore.com/epaper/edition/" . $datecode . "/star-mysore/page/1", false, stream_context_create($arrContextOptions));
            $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));
            $linkArray =   explode('"><img src="', $content);

            if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) $linkArray = array_slice($linkArray, 0, $no_of_pages_to_run_on_each_edition + 1);

            for ($page = 1; $page <= count($linkArray); $page++) {

                $imagelink = explode('"', explode('"><img src="', $content)[$page])[0];
                if (trim($imagelink) == '')
                    break;

                echo $imagelink . "<br>";

                $getpath = explode("&", makefilepath($epapercode, "Mysore", $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Mysore", $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }

        if ($epapercode == "NHT") {
            $code = end(explode('/', explode('-md-ga', file_get_contents("https://epaper.navhindtimes.in/mainpage.aspx?pdate=" . $filenamedate, false, stream_context_create($arrContextOptions)))[0]));

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $pdfUrl = "https://epaper.navhindtimes.in/PageImages/pdf/" . $dateForLinks . "/" . $code . "-md-ga-" . sprintf("%02d", $page) . ".pdf";
                if (!file_get_contents($pdfUrl, false, stream_context_create($arrContextOptions)))
                    continue;
                $resolution = 300;
                $getpath = explode("&", makefilepath($epapercode, "Goa", $filenamedate, $page, $lang));

                $filepath =  $getpath[0];

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                $command = "convert -density $resolution $pdfUrl -background white -alpha remove -alpha off -quality 100 $filepath";
                exec($command, $output, $returnCode);
                if ($returnCode === 0) {
                    echo "PDF converted to images successfully.";
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, "Goa", $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                } else {
                    echo "Error converting PDF to images.";
                    print_r($output);
                }
            };
        }

        if ($epapercode == "OHO") {


            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

                $testcontent = file_get_contents("http://epaper.heraldgoa.in/articlepage.php?articleid=OHERALDO_GOA_" . $dateForLinks . "_" . $page . "_1", false, stream_context_create($arrContextOptions));

                $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];

                if (!getimagesize($testimagelink) and $page > 20)
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $response = file_get_contents("http://epaper.heraldgoa.in/articlepage.php?articleid=OHERALDO_GOA_" . $dateForLinks . "_" . $page . "_" . $section, false, stream_context_create($arrContextOptions));

                    $imagelink = explode('"', explode('id="artimg" src="', $response)[1])[0];

                    echo $imagelink . "<br>";
                    $imageInfo = getimagesize($imagelink);

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

        if ($epapercode == "AP") {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = "https://epaper.anandabazar.com/epaperimages////" . $dateForLinks . "////" . $dateForLinks . "-md-hr-" . $page . "ll.png";
                if (!getimagesize($imagelink))
                    break;

                $getpath = explode("&", makefilepath($epapercode,  "Kolkata", $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Kolkata", $page, $section, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }

        if ($epapercode == "ASP") {
            $content = file_get_contents("https://epaper.asomiyapratidin.in/edition/" . $datecode . "/%E0%A6%85%E0%A6%B8%E0%A6%AE%E0%A7%80%E0%A7%9F%E0%A6%BE-%E0%A6%AA%E0%A7%8D%E0%A6%B0%E0%A6%A4%E0%A6%BF%E0%A6%A6%E0%A6%BF%E0%A6%A8", false, stream_context_create($arrContextOptions));
            $linkArray = explode('"><img src="', $content);
            $filenamedate = date('Y-m-d', strtotime(trim(explode('|', explode('Asomiya Pratidin ePaper :', $content)[1])[0])));
            if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) $linkArray = array_slice($linkArray, 0, $no_of_pages_to_run_on_each_edition + 1);

            for ($page = 1; $page <= count($linkArray); $page++) {
                if ($linkArray[$page] == null)
                    break;
                $imagelink = str_replace('thumb_150_auto', 'files', explode('"', $linkArray[$page])[0]);
                if (!getimagesize($imagelink))
                    break;
                $getpath = explode("&", makefilepath($epapercode, "Guwahati", $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Guwahati", $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }

        if ($epapercode == "BS") {

            $content = file_get_contents("https://epaper.bombaysamachar.com/view/" . $datecode . "/" . date("d-m-Y", strtotime($filenamedate)) . "/1", false, stream_context_create($arrContextOptions));

            $linkcode = end(explode('/c600x315/', explode('-page-01', $content)[0]));



            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = "https://epaper.bombaysamachar.com/media/" . $linkcode . "-page-" . sprintf("%02d", $page) . ".jpg";


                if (!getimagesize($imagelink))
                    break;
                $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $page, $lang));

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Mumbai", $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }

        if ($epapercode == "DHM") {
            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $testcontent = file_get_contents("http://webmilap.com/articlepage.php?articleid=HINDIMIL_HIN_" . $dateForLinks . "_" . $page . "_1", false, stream_context_create($arrContextOptions));
                $testimagelink = explode('"', explode('d="artimg" src="', $testcontent)[1])[0];
                $testimageInfo = getimagesize($testimagelink);

                if (!$testimageInfo and $page > 20)
                    break;

                for ($section = 1; $section <= $no_of_sections_to_run_on_each_page; $section++) {
                    $content = file_get_contents("http://webmilap.com/articlepage.php?articleid=HINDIMIL_HIN_" . $dateForLinks . "_" .  $page . "_" . $section, false, stream_context_create($arrContextOptions));

                    $imagelink = explode('"', explode('d="artimg" src="', $content)[1])[0];
                    $imageInfo = getimagesize($imagelink);

                    if (!$imageInfo)
                        continue;

                    $getpath = explode("&", makefilepath($epapercode, "Hyderabad", $filenamedate, $page . "00" . $section, $lang));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                    writeImage($imagelink, $getpath[0]);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    runTesseract($epapername, "Mumbai", $page, $section, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
            }
        }

        if ($epapercode == "DNS") {
            $dateTime = DateTime::createFromFormat('dmY', $dateForLinks);
            $filenamedate = $dateTime->format('Y-m-d');

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $imagelink = "https://www.dainiksambad.net/epaperimages//" . $dateForLinks . "//page-" . $page . "ll.png";
                $getpath = explode("&", makefilepath($epapercode, "Agartala", $filenamedate, $page, $lang));
                $islastpage = writeImageWithCurl($imagelink, $getpath[0]);

                if ($islastpage)
                    break;
                // if (alreadyDone($getpath[0], $conn) == "Yes") continue;

                // writeImage($imagelink, $getpath[0]);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Mumbai", $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }
        }

        // if ($epapercode == "ESM") {
        //     crawltoi($cityarray, $dateForLinks, $epapercode, $citycode, $filenamedate, $eol, $conn, $lang, $cities_of_interest, $epapername);
        // }

        if ($epapercode == "HTV") {

            for ($page = 1; $page < $no_of_pages_to_run_on_each_edition; $page++) {

                $url = 'https://www.ehitavada.com/index.php?edition=NCpage&date=' . $filenamedate . '&page=' . $page;

                $getpath = explode("&", makefilepath($epapercode, "Nagpur", $filenamedate, $page, $lang));

                $outputFile = $getpath[0];

                $client = Client::createChromeClient();

                try {
                    $client->start();
                    $client->request('GET', $url);
                    $client->wait(10, 500)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.map.maphilighted'))
                    );
                    $client->wait(10, 500)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('img.map.maphilighted'))
                    );

                    if ($client->getCrawler()->filter('.custom-popup')->count() > 0) {
                        $client->executeScript("document.querySelector('.custom-popup span').click()");
                    }

                    if ($client->getCrawler()->filter('#tour_popup_container')->count() > 0) {
                        $client->executeScript("document.querySelector('#tour_popup_container button').click()");
                    }

                    $client->wait(10, 500)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('zoom_btn'))
                    );

                    $client->executeScript("document.getElementById('zoom_btn').click()");
                    $client->wait(10, 500)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.icon_btn.selected'))
                    );

                    $client->executeScript('window.scrollTo(0, document.body.scrollHeight);');
                    $client->executeScript('window.scrollTo(1000, 0);');
                    $scrollWidth = $client->executeScript('return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);');
                    $scrollHeight = $client->executeScript('return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);');
                    $window = $client->getWebDriver()->manage()->window();
                    $window->setSize(new WebDriverDimension($scrollWidth, $scrollHeight));

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                    $client->takeScreenshot($outputFile);

                    $window->setSize(new WebDriverDimension(1920, 1080));

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    // runTesseract($epapername, "Mumbai", $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                } catch (TimeoutException $e) {
                    echo "last page";
                    $client->quit();
                    exit;
                }
            }
        }

        if ($epapercode == "NGS") {
            $data = getdata("https://epaper.navgujaratsamay.com/");

            $datecode = explode('"', explode('"https://epaper.navgujaratsamay.com/r/', $data)[1])[0];
            $filenamedate = date("Y-m-d", time());
            $link = "https://epaper.navgujaratsamay.com/" . $datecode . "/Ahmedabad/" . $dateForLinks . "#page/1/3";

            $response = getdata($link);
            $totalPages = number_format(end(explode("of ", explode('<i class="fa fa-caret-down"',  $response)[0])));

            if (!($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $totalPages)) $no_of_pages_to_run_on_each_edition = $totalPages;

            $client = Client::createChromeClient();
            $client->start();
            setSize($client, $link);

            for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                $url = "https://epaper.navgujaratsamay.com/" . $datecode . "/Ahmedabad/" . $dateForLinks . "#page/" . $page . "/3";
                $getpath = explode("&", makefilepath($epapercode, "Ahmedabad", $filenamedate, $page, $lang));
                $outputFile = $getpath[0];

                $client->request('GET', $url);

                $client->executeScript('document.getElementById("top-clips-box").style.display = "block";');

                sleep(3);

                if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                $screenshot = $client->takeScreenshot($outputFile);

                $croppedImage = getCroppedImage($client, 'page-div', $screenshot);
                imagepng($croppedImage, $outputFile);

                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                runTesseract($epapername, "Ahmedabad", $page, 0, $conn, $getpath, $lang);
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
                ob_flush();
                flush();
            }

            $client->quit();
        }

        if ($epapercode == "PBK") {
            $data = getdata("https://epaper.prabhatkhabar.com/");
            $contentarray = explode('<a href="https://epaper.prabhatkhabar.com/r/', $data);
            $codearray = array();
            for ($code = 1; $code < count($contentarray); $code++) {
                $getcode = explode('"', $contentarray[$code])[0];
                $getcity = explode('</h3>', explode("<h3>", $contentarray[$code])[1])[0];
                $codearray[$getcity] = $getcode;
            }

            if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);

            for ($edition = 0; $edition < count($cityarray); $edition++) {
                $city = str_replace(' ', '', $cityarray[$edition]);
                $code = $codearray[$cityarray[$edition]];

                $filenamedate = date("Y-m-d", time());
                $link = "https://epaper.prabhatkhabar.com/" . $code . "/" . $city . "/CITY#page/1/3";
                $client = Client::createChromeClient();
                $client->start();
                setSize($client, $link);

                $response = getdata("https://epaper.prabhatkhabar.com/" . $code . "/" . $city . "/CITY#page/1/3");


                $totalPages = number_format(end(explode("of ", explode('<i class="fa fa-caret-down"',  $response)[0])));
                if (!($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < $totalPages)) $no_of_pages_to_run_on_each_edition = $totalPages;
                // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

                //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
                //     continue;
                // }

                for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
                    $url = "https://epaper.prabhatkhabar.com/" . $code . "/" . $city . "/CITY#page/" . $page . "/3";
                    $getpath = explode("&", makefilepath($epapercode, ucwords(explode('-', strtolower($city))[0]), $filenamedate, $page, $lang));
                    $outputFile = $getpath[0];

                    $client->request('GET', $url);

                    $client->executeScript('document.getElementById("top-clips-box").style.display = "block";');

                    sleep(3);

                    if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                    $screenshot = $client->takeScreenshot($outputFile);

                    $croppedImage = getCroppedImage($client, 'page-div', $screenshot);
                    imagepng($croppedImage, $outputFile);

                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                    // runTesseract($epapername, ucwords(explode('-', strtolower($city))[0]), $page, 0, $conn, $getpath, $lang);
                    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
                    ob_flush();
                    flush();
                }
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
                $client->quit();
            }
        }

        //exec("rm -f /nvme/*");
        // exec("rm -f ./nvme/*");

        // mysqli_query($conn, "INSERT INTO Crawl_Record1 (Papername,Papershortname,Paperdate) VALUES ('" . $epapername . "','" . $epapercode . "','" . $filenamedate . "')");
    }
