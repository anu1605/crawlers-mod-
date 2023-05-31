
<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

$epapers = array("AU", "DC", "HB", "DJ", "JPS", "KM", "LM", "MC", "MM", "NB", "NBT", "ND", "NVR", "NYB", "PAP", "POD", "PUD", "RS", "SAM", "SBP", "SMJ", "SY", "VV", "YB");

include dirname(__FILE__) . "/crawl_functions.php";
foreach ($epapers as $epapercode) {

    $filenamedate = filenamedate($epapercode);

    // if ($epapercode == "AU") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);
    //     $cityarray = cityArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         $response = file_get_contents("https://epaper.amarujala.com/" . $cityarray[$edition] . "/" . $dateForLinks . "/01.html?format=img&ed_code=" . $cityarray[$edition]);

    //         $a = explode('/hdimage.jpg"', $response);
    //         $b = explode('<link rel="preload" href="', $a[0]);
    //         $imageLinkPage1 = $b[1] . "/hdimage.jpg";

    //         for ($i = 1; $i <= 50; $i++) {

    //             $pgImageURL = str_replace("/01/hdimage.jpg", "/" . sprintf('%02d', $i) . "/hdimage.jpg", $imageLinkPage1);

    //             if (!getimagesize($pgImageURL)) {
    //                 break;
    //             }

    //             $filepath = "/nvme/AU_" . ucwords(explode("-", $cityarray[$edition])[0]) . "_" . $filenamedate . "_" . $i . "_admin_hin.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/AU_" . ucwords(explode("-", $cityarray[$edition])[0]) . "_" . $filenamedate . "_" . $i . "_admin_hin.txt";

    //             $image = file_get_contents($pgImageURL);
    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");
    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $i . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Amar Ujala','AU','" . $filenamedate . "')");
    // }
    // if ($epapercode == "DC") {
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
    //                 $link = "http://103.241.136.50/epaper/DC/" . $code . "/510X798/" . $filenamedate . "/b_images/" . $code . "_" . $filenamedate . "_maip" . $page . "_" . $section . ".jpg";

    //                 if (!file_get_contents($link))
    //                     break;

    //                 $filepath = "/nvme/DC_" . $city . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_eng.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/DC_" . $city . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_eng.txt";

    //                 $image = file_get_contents($link);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l eng > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Deccan Chronicle','DC','" . $filenamedate . "')");
    // }
    // if ($epapercode == "HB") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     $cityarray = cityArray($epapercode);
    //     $citylink = cityCodeArray($epapercode);

    //     $array = explode(',', file_get_contents(dirname(__FILE__) . "./dependencies/hb.txt"));
    //     $citycode = array();
    //     $newCodes = array();

    //     foreach ($array as $val) {
    //         $codeFromString = explode('=>', $val)[1];
    //         array_push($citycode, $codeFromString);
    //     }


    //     for ($edition = 0; $edition < count($cityArray); $edition++) {
    //         $code = $citycode[$edition];
    //         $link = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $cityarray[$edition] . "-full-edition/" . $dateForLinks . "/" . $citylink[$edition] . "/";

    //         if ($cityArray[$edition] == "raipur") {
    //             $link2 = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $cityarray[$edition] . "-full-edition/" . $dateForLinks . "/" . $cityarray[$edition] . "-main/";
    //             if (file_get_contents($link2 . $code)) {
    //                 $link = $link2;
    //             }
    //         }
    //         if (!file_get_contents($link . $code)) {
    //             for ($i = 40; $i < 300; $i++) {
    //                 $code = $citycode[$edition] + $i;
    //                 if ($cityArray[$edition] == "raipur") {
    //                     $link2 = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $cityarray[$edition] . "-full-edition/" . $dateForLinks . "/" . $cityarray[$edition] . "-main/";
    //                     if (file_get_contents($link2 . $code)) {
    //                         $link = $link2;
    //                         array_push($newCodes, strval($code));
    //                         break;
    //                     }
    //                 }
    //                 if (file_get_contents($link . $code)) {
    //                     array_push($newCodes, strval($code));
    //                     break;
    //                 }
    //             }
    //         }

    //         $content = file_get_contents($link . $code);
    //         $section1 = explode('id="slider-epaper" class="imageGalleryWrapper"><li data-index="0"', $content)[1];
    //         $section2 = explode('class="page-toolbar"><div id="page-level-nav"', $section1)[0];
    //         $linkArray = explode('data-big="', trim($section2));

    //         for ($imglink = 1; $imglink < count($linkArray); $imglink++) {
    //             $imageLink =  explode('"', $linkArray[$imglink])[0];
    //             $filepath = "/nvme/HB_" . ucwords($cityArray[$edition]) . "_" . $filenamedate . "_" . $imglink . "_admin_hin.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/HB_" . ucwords($cityArray[$edition]) . "_" . $filenamedate . "_" . $imglink . "_admin_hin.txt";

    //             $image = file_get_contents($imageLink);

    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");

    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $imglink . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }
    //     if (count($newCodes) == 3) {
    //         $file = fopen(dirname(__FILE__, 1) . "./dependencies/hb.txt", 'w');
    //         $txt =  "raipur=>" . $newCodes[0] . ",bilaspur=>" . $newCodes[1] . ",bhopal=>" . $newCodes[2] . "";
    //         fwrite($file, $txt);
    //         fclose($file);
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Hari Bhumi','HB','" . $filenamedate . "')");
    // }

    // if ($epapercode == "DJ") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);
    //     $cityarray = cityArray($epapercode);
    //     $linkarray = cityCodeArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         $response = file_get_contents("https://epaper.jagran.com/epaper/" . $dateForLinks . $linkarray[$edition] . ".html");

    //         $a = explode('<ul id="menu-toc" class="menu-toc">', $response);
    //         $b = explode('</ul>', $a[1]);
    //         $pagesHTML = $b[0];

    //         $a = explode('ss.png">', $pagesHTML);

    //         for ($i = 0; $i < count($a) - 1; $i++) {
    //             $b = explode('data-src="', $a[$i]);
    //             $url = $b[1];
    //             $url_parts = explode('/', $url);
    //             $last_part = end($url_parts);
    //             $modified_last_part = 'M-' . $last_part . '.png';
    //             $url_parts[count($url_parts) - 1] = $modified_last_part;
    //             $pgImageURL = implode('/', $url_parts);

    //             $filepath = "/nvme/DJ_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/DJ_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.txt";

    //             $image = file_get_contents($pgImageURL);

    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");

    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $i . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Danik Jagran','DJ','" . $filenamedate . "')");
    // }

    // if ($epapercode == "JPS") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     for ($page = 1; $page <= 50; $page++) {

    //         $pgImageURL = "https://www.janpathsamachar.com/epaper/janpath/" . $dateForLinks . "/page" . $page . ".jpg";
    //         if (file_get_contents($pgImageURL)) {
    //             $filepath = "/nvme/JPS_Siliguri" . "_" . $filenamedate . "_" . $page . "_admin_hin.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/JPS_Siliguri" . "_" . $filenamedate . "_" . $page . "_admin_hin.txt";

    //             $image = file_get_contents($pgImageURL);

    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");

    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }
    //         } else break;
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Janpath Samachar','JPS','" . $filenamedate . "')");
    // }

    // if ($epapercode == "KM") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     for ($page = 1; $page < 50; $page++) {
    //         $testlink = "http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_1";
    //         $testcontent = file_get_contents($testlink);
    //         $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
    //         $testimageInfo = getimagesize($testimagelink);
    //         if (!$testimageInfo)
    //             break;

    //         for ($section = 1; $section < 100; $section++) {
    //             $content = file_get_contents("http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $dateForLinks . "_" .  $page . "_" . $section);
    //             if ($content) {
    //                 $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
    //                 $imageInfo = getimagesize($imagelink);
    //                 if (!$imageInfo)
    //                     break;

    //                 $filepath = "/nvme/KM_Karnataka" . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_kan.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/KM_Karnataka" . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_kan.txt";

    //                 $image = file_get_contents($imagelink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l kan > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Karnataka Malla','KM','" . $filenamedate . "')");
    // }

    // if ($epapercode == "LM") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     $cityarray  = cityArray($epapercode);
    //     $citycode = cityCodeArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         for ($page = 1; $page < 50; $page++) {
    //             $testcontent = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1");

    //             if (!strpos($testcontent, "ArticleImages"))
    //                 break;

    //             for ($section = 1; $section < 100; $section++) {
    //                 $content = file_get_contents("http://epaper.lokmat.com/articlepage.php?articleid=LOK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section);
    //                 if (!strpos($content, "ArticleImages"))
    //                     break;

    //                 $imagelink = explode("'", explode("src='", $content)[1])[0];

    //                 $filepath = "/nvme/LM_" .  $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_mar.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/LM_" .  $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_mar.txt";

    //                 if (empty($imagelink))
    //                     break;

    //                 $image = file_get_contents($imagelink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l mar > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }
    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Lokmat','LM','" . $filenamedate . "')");
    // }

    // if ($epapercode == "MC") {
    //     $datecode = dateForLinks($epapercode, $filenamedate);


    //     $content =   file_get_contents("https://www.mumbaichoufer.com/view/" . $datecode . "/mc");
    //     $getmcdate = trim(explode('-', explode('<title>Mumbaichoufer -', $content)[1])[0]);
    //     $mcdate = date("Y-m-d", strtotime($getmcdate));
    //     $firstId = explode('"', explode('{"mp_id":"', $content)[1])[0];

    //     $section1 = explode($firstId, $content)[1];
    //     $idarray = explode('{"mp_id":"', $section1);

    //     for ($id = 1; $id < count($idarray) - 1; $id++) {
    //         $imageId = explode('"', $idarray[$id])[0];
    //         $imagelink = "https://www.mumbaichoufer.com/map-image/" . $imageId . ".jpg";
    //         $filepath = "/nvme/MC_Mumbai" . "_" . $filenamedate . "_" . $id . "_admin_mar.jpg";
    //         $temp_txtfile = str_replace(".jpg", "", $filepath);
    //         $txtfile = "./imagestext/MC_Mumbai" . "_" . $filenamedate . "_" . $id . "_admin_mar.txt";

    //         $image = file_get_contents($imagelink);
    //         $handle = fopen($filepath, "w");
    //         fwrite($handle, $image);
    //         fclose($handle);

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";
    //         try {
    //             $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l mar > /dev/null 2>&1";
    //             exec($command);
    //             $text = file_get_contents($temp_txtfile . ".txt");

    //             $matches = array();
    //             preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //             $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //             foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //             $n = count($matches);

    //             if ($n == 0) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //             } else {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                 rename($temp_txtfile . ".txt", $txtfile);
    //             }
    //         } catch (Exception $e) {
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $id . " Completed\n";
    //     }
    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Mumbai Chaufer','MC','" . $filenamedate . "')");
    // }

    // if ($epapercode == "MM") {
    //     $datecode = dateForLinks($epapercode, $filenamedate);
    //     $content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra/page/1");
    //     $imagelinks =   explode('"><img src="', $content);

    //     for ($link = 1; $link < count($imagelinks); $link++) {
    //         $imagelink = explode('"', explode('"><img src="', $content)[$link])[0];
    //         $filepath = "/nvme/MM_Mysore" . "_" . $filenamedate . "_" . $link . "_admin_kan.jpg";
    //         $temp_txtfile = str_replace(".jpg", "", $filepath);
    //         $txtfile = "./imagestext/MM_Mysore" . "_" . $filenamedate . "_" . $link . "_admin_kan.txt";

    //         $image = file_get_contents($imagelink);
    //         $handle = fopen($filepath, "w");
    //         fwrite($handle, $image);
    //         fclose($handle);

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //         try {
    //             $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l kan > /dev/null 2>&1";
    //             exec($command);
    //             $text = file_get_contents($temp_txtfile . ".txt");

    //             $matches = array();
    //             preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //             $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //             foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //             $n = count($matches);

    //             if ($n == 0) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //             } else {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                 rename($temp_txtfile . ".txt", $txtfile);
    //             }
    //         } catch (Exception $e) {
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $link . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Mysore Mithra','MM','" . $filenamedate . "')");
    // }

    // if ($epapercode == "NB") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     $cityarray  =  cityArray($epapercode);
    //     $citycode = cityCodeArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {

    //         for ($page = 1; $page < 50; $page++) {

    //             $testcontent = file_get_contents("https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-1/");

    //             $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];

    //             $testimageInfo = getimagesize($testimagelink);

    //             if (!$testimageInfo)
    //                 break;

    //             for ($section = 1; $section < 100; $section++) {

    //                 $link =   "https://epaper.enavabharat.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition-navabharat-" . $citycode[$edition] . "/" . $page . "-" . $section . "/";

    //                 $content = file_get_contents($link);
    //                 $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
    //                 $imageInfo = getimagesize($imagelink);

    //                 if (!$imageInfo)
    //                     break;

    //                 $filepath = "/nvme/NB_" .  ucwords($cityarray[$edition]) . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_hin.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/NB_" .  ucwords($cityarray[$edition]) . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_mar.txt";

    //                 $image = file_get_contents($imagelink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {

    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {

    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Navbharat','NB','" . $filenamedate . "')");
    // }

    // if ($epapercode == "NBT") {
    //     $cityarray = cityArray($epapercode);
    //     $citycode = cityCodeArray($epapercode);

    //     for ($edition = 0; $edition < count($citycode); $edition++) {
    //         $code = str_replace("d", $filenamedate, $citycode[$edition]);
    //         $city = $cityarray[$edition];
    //         $pageURL = "https://epaper.navbharattimes.com/" . $code  . "/page-1.html";
    //         $content = file_get_contents($pageURL);

    //         $section1 = explode("class='orgthumbpgnumber'>1</div>", $content)[1];
    //         $section2 = explode('<div id="rsch"', $section1)[0];
    //         $linkArray = explode("<div class='imgd'><img src='", $section2);
    //         $number = 1;
    //         for ($link = 1; $link < count($linkArray); $link++) {
    //             $imageLink =  str_replace('ss', '', trim(explode("' class='pagethumb'", $linkArray[$link])[0]));

    //             $filepath = "/nvme/NBT_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $link . "_admin_hin.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/NBT_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $link . "_admin_hin.txt";

    //             $image = file_get_contents($imageLink);
    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");

    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $link . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }
    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Navbharat Times','NBT','" . $filenamedate . "')");
    // }

    // if ($epapercode == "ND") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     $cityarray = cityArray($epapercode);
    //     $citycode = cityArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         $code = $citycode[$edition];
    //         $city = $cityarray[$edition];
    //         $pageURL = "https://epaper.naidunia.com/epaper/" . $dateForLinks . "-" . $code . "-" . $city . "-edition-" . $city . ".html";
    //         $response = file_get_contents($pageURL);



    //         $a =  number_format(explode('"', explode('<input type="hidden" name="totalpage" id="totalpage" value="', $response)[1])[0]);
    //         $array = (explode('<img data-src="',  explode('<div class="slidebox" id="item-zoom1">', $response)[1]));

    //         for ($i = 1; $i <= $a; $i++) {

    //             $pgImageURL = trim(explode('" title=', $array[$i])[0]);

    //             $filepath = "/nvme/ND_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/ND_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.txt";


    //             $image = file_get_contents($pgImageURL);


    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");


    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $i . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Nai Dunia','ND','" . $filenamedate . "')");
    // }

    // if ($epapercode == "NVR") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     $cityarray  =  cityArray($epapercode);

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {

    //         for ($page = 1; $page < 50; $page++) {

    //             $testcontent = file_get_contents("https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-1/");
    //             $testimagelink = explode('"', explode("id='ImageArticle'  src=", $testcontent)[1])[1];

    //             $imageInfo = getimagesize($testimagelink);

    //             if (!$imageInfo)
    //                 break;

    //             for ($section = 1; $section < 100; $section++) {

    //                 $link =   "https://epaper.navarashtra.com/article-" . $dateForLinks . "-" . $cityarray[$edition] . "-edition/" . $page . "-" . $section . "/";
    //                 $content = file_get_contents($link);
    //                 $imagelink = explode('"', explode("id='ImageArticle'  src=", $content)[1])[1];
    //                 $imageInfo = getimagesize($imagelink);

    //                 if (!$imageInfo)
    //                     break;

    //                 $filepath = "/nvme/NVR_" . ucwords($cityarray[$edition]) . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_mar.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);

    //                 $txtfile = "./imagestext/NVR_" .  ucwords($cityarray[$edition]) . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_mar.txt";

    //                 $image = file_get_contents($imagelink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {

    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l mar > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {

    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Navrasthra','NVR','" . $filenamedate . "')");
    // }

    // if ($epapercode == "NYB") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     for ($pageNumber = 1; $pageNumber <= 50; $pageNumber++) {
    //         if ($pageNumber == 1)
    //             $page = "index.php";
    //         else $page = "page" . $pageNumber . ".php";

    //         $content = file_get_contents("https://niyomiyabarta.com/epaper/" . $dateForLinks . "/" . $page);

    //         if ($content) {
    //             $section1 = explode('<map name="Map2"', $content)[1];
    //             $section2 = explode('</map>', $section1)[0];
    //             $linkArray = explode("redirectme('", $section2);

    //             for ($page = 1; $page < count($linkArray); $page++) {
    //                 $pageName = explode("',", $linkArray[$page])[0];
    //                 $imageLink =  "https://niyomiyabarta.com/epaper/" . $dateForLinks . "/images/p" . $pageNumber . "/" . $pageName;
    //                 $filepath = "/nvme/NYB_Guwahati_" . $filenamedate . "_" . $pageNumber . "00" . $page . "_admin_asm.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/NYB_Guwahati_" . $filenamedate . "_" . $pageNumber . "00" . $page . "_admin_asm.txt";
    //                 $image = file_get_contents($imageLink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l asm > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");
    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $pageNumber . " Section " . $page . " Completed\n";
    //             }
    //         } else break;
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $pageNumber . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Niyomiya Barta','NYB','" . $filenamedate . "')");
    // }

    // if ($epapercode == "PAP") {
    //     $dateForLinks = dateForLinks($epapercode, $filenamedate);

    //     $data = file_get_contents("https://www.glpublications.in/PurvanchalPrahari/Guwahati/" . $dateForLinks . "/Page-2");
    //     $contentarray = explode('<div class="clip-container"', $data);



    //     for ($content = 1; $content < count($contentarray); $content++) {
    //         $link = explode("'", explode("<img src='", $contentarray[$content])[1])[0];


    //         $filepath = "/nvme/PAP_Bhubaneswar_" . $filenamedate . "_" . $content . "_admin_ori.jpg";
    //         $temp_txtfile = str_replace(".jpg", "", $filepath);
    //         $txtfile = "./imagestext/PAP_Bhubaneswar_" . $filenamedate . "_" . $content . "_admin_ori.txt";


    //         $image = file_get_contents($link);
    //         $handle = fopen($filepath, "w");
    //         fwrite($handle, $image);
    //         fclose($handle);

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";
    //         try {
    //             $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l ori > /dev/null 2>&1";
    //             exec($command);
    //             $text = file_get_contents($temp_txtfile . ".txt");


    //             $matches = array();
    //             preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //             $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //             foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //             $n = count($matches);

    //             if ($n == 0) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //             } else {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                 rename($temp_txtfile . ".txt", $txtfile);
    //             }
    //         } catch (Exception $e) {
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $content . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Purvanchal Prahari','PAP','" . $filenamedate . "')");
    // }

    // if ($epapercode == "POD") {
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_URL, "https://e2india.com/pratidin/");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
    //     $data = curl_exec($ch);
    //     curl_close($ch);



    //     $contentArray = explode('</div><img class="" src="', $data);




    //     for ($i = 1; $i < count($contentArray); $i++) {
    //         $imagelink =  str_replace("&", "&amp;", explode('"',  $contentArray[$i])[0]);

    //         $filepath = "/nvme/POD_Bhubaneswar" . "_" . $filenamedate . "_" . $i . "_ori.jpg";
    //         $temp_txtfile = str_replace(".jpg", "", $filepath);
    //         $txtfile = "./imagestext/POD_Bhubaneswar" . "_" . $filenamedate . "_" . $i . "_ori.txt";
    //         $image = file_get_contents($imagelink);


    //         $handle = fopen($filepath, "w");
    //         fwrite($handle, $image);
    //         fclose($handle);

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";


    //         try {
    //             $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l ori > /dev/null 2>&1";
    //             exec($command);
    //             $text = file_get_contents($temp_txtfile . ".txt");


    //             $matches = array();
    //             preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //             $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //             foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //             $n = count($matches);

    //             if ($n == 0) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //             } else {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                 rename($temp_txtfile . ".txt", $txtfile);
    //             }
    //         } catch (Exception $e) {
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //         }

    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $i . " Completed\n";
    //     }
    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Pratidin Odia Daily','POD','" . $filenamedate . "')");
    // }

    // if ($epapercode == "RS") {
    //     $dateForLinks = date('dmY', strtotime($filenamedate));


    //     $cityarray = array("Delhi", "Lucknow", "Patna", "Dehradun", "Kanpur", "Gorakhpur", "Varanasi");
    //     $linkarray = array("http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-hr-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-lu-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-pt-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-dd-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-kn-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-gp-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//29052023-vn-md-1ll.png");


    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         for ($page = 1; $page < 50; $page++) {
    //             $imagelink = str_replace("md-1", "md-" . $page, $linkarray[$edition]);
    //             if (file_get_contents($imagelink)) {
    //                 $filepath = "/nvme/RS_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "_admin_hin.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/RS_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "_admin_hin.txt";

    //                 $image = file_get_contents($imagelink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //             } else break;
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Rashtriya Sahara','RS','" . $filenamedate . "')");
    // }

    // if ($epapercode == "SAM") {
    //     $dateForLinks = date("dmY", strtotime($filenamedate));

    //     $cityarray = array("Bhubaneswar", "Cuttack", "Rourkela", "Berhampur");
    //     $citycode = array("71", "72", "79", "77");
    //     $imagelinkcitycode = array("hr", "km", "ro", "be");

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         $link = "https://sambadepaper.com/epaper/1/" . $citycode[$edition] . "/" . $filenamedate . "/1";
    //         $content = file_get_contents($link);
    //         $pagearray = explode("id='imgpage_", $content);

    //         for ($page = 1; $page < count($pagearray); $page++) {
    //             $pageno = explode("'", $pagearray[$page])[0];
    //             $idarray = explode("show_pop('", $pagearray[$page]);
    //             for ($id = 1; $id < count($idarray); $id++) {
    //                 $imagelinkid = explode(",", $idarray[$id])[1];
    //                 $imagelink = "https://sambadepaper.com/epaperimages/" . $dateForLinks . "/" . $dateForLinks . "-md-" . $imagelinkcitycode[$edition] . "-" . $pageno . "/" . str_replace("'", "", $imagelinkid) . ".jpg";

    //                 $filepath = "/nvme/SAM_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $id . "_admin_ori.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/SAM_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $id . "_admin_ori.txt";


    //                 $image = file_get_contents($imagelink);


    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l ori > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");


    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $id . " Completed\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Sambad','SAM','" . $filenamedate . "')");
    // }

    // if ($epapercode == "SBP") {
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_URL, "https://epaper.sangbadpratidin.in/");
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
    //     $data = curl_exec($ch);
    //     curl_close($ch);
    //     $contentArray = explode('<div class="item">', $data);




    //     for ($i = 1; $i < count($contentArray); $i++) {

    //         $imagelink = str_replace('&', '&amp;', explode('"', explode('src="', $contentArray[$i])[1])[0]);

    //         $filepath = "/nvme/SBP_Kolkata" . "_" . $filenamedate . "_" . $i . "_admin_ben.jpg";
    //         $temp_txtfile = str_replace(".jpg", "", $filepath);
    //         $txtfile = "./imagestext/SBP_Kolkata" . "_" . $filenamedate . "_" . $i . "_admin_ben.txt";


    //         $image = file_get_contents($imagelink);

    //         $handle = fopen($filepath, "w");
    //         fwrite($handle, $image);
    //         fclose($handle);


    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //         try {
    //             $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l ben > /dev/null 2>&1";
    //             exec($command);
    //             $text = file_get_contents($temp_txtfile . ".txt");


    //             $matches = array();
    //             preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //             $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //             foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //             $n = count($matches);

    //             if ($n == 0) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //             } else {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                 rename($temp_txtfile . ".txt", $txtfile);
    //             }
    //         } catch (Exception $e) {
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $i . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Sangbad Pratidin','SBP','" . $filenamedate . "')");
    // }

    // if ($epapercode == "SMJ") {
    //     $dateForLinks = date('dmY', strtotime($filenamedate));

    //     $content = file_get_contents("https://samajaepaper.in/epaper/1/73/" . $filenamedate . "/1");
    //     $pageArray = explode("class='map", $content);



    //     for ($page = 1; $page < count($pageArray); $page++) {
    //         $sections = explode("show_pop('", $pageArray[$page]);
    //         for ($sec = 1; $sec < count($sections); $sec++) {
    //             $name = explode("','", $sections[$sec])[1];
    //             $link = "https://samajaepaper.in/epaperimages/" . $dateForLinks . "/" . $dateForLinks . "-md-bh-" . $page . "/" . $name . ".jpg";



    //             $filepath = "/nvme/SMJ_Bhubaneswar_" . $filenamedate . "_" . $page . "00" . $sec . "_admin_ori.jpg";
    //             $temp_txtfile = str_replace(".jpg", "", $filepath);
    //             $txtfile = "./imagestext/SMJ_Bhubaneswar_" . $filenamedate . "_" . $page . "00" . $sec . "_admin_ori.txt";


    //             $image = file_get_contents($link);


    //             $handle = fopen($filepath, "w");
    //             fwrite($handle, $image);
    //             fclose($handle);

    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //             try {
    //                 $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l ori > /dev/null 2>&1";
    //                 exec($command);
    //                 $text = file_get_contents($temp_txtfile . ".txt");


    //                 $matches = array();
    //                 preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                 $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                 foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                 $n = count($matches);

    //                 if ($n == 0) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                 } else {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                     rename($temp_txtfile . ".txt", $txtfile);
    //                 }
    //             } catch (Exception $e) {
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $sec . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");

    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Samaja','SMJ','" . $filenamedate . "')");
    // }

    // if ($epapercode == "SY") {
    //     $dateForLinks = date('Ymd', strtotime($filenamedate));

    //     $cityArray  =  array("Mangalore", "Davangere", "Kalaburgi", "Hubli", "Bangalore");
    //     $citycode = array("MANG", "DAVN", "KALB", "HUB",  "BANG",);


    //     for ($edition = 0; $edition < count($cityArray); $edition++) {
    //         for ($page = 1; $page < 50; $page++) {
    //             $testcontent  = file_get_contents("http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_1");
    //             $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];

    //             $testimageInfo = getimagesize($testimagelink);


    //             if (!$testimageInfo)
    //                 break;

    //             for ($section = 1; $section < 100; $section++) {
    //                 $link =   "http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_" . $section;
    //                 $content = file_get_contents($link);


    //                 $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];

    //                 $imageInfo = getimagesize($imagelink);


    //                 if (!$imageInfo)
    //                     break;


    //                 $filepath = "/nvme/SY_" .  $cityArray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_kan.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/SY_" .  $cityArray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_kan.txt";


    //                 $image = file_get_contents($imagelink);



    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l kan > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");



    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Completed\n";
    //     }

    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Samyukta Karnataka','SY','" . $filenamedate . "')");
    // }

    // if ($epapercode == "VV") {
    //     $dateForLinks = date('Ymd', strtotime($filenamedate));

    //     $cityarray  =  array("Bengaluru", "Hubli");
    //     $citycode = array("BEN", "HUB");

    //     for ($edition = 0; $edition < count($cityarray); $edition++) {
    //         for ($page = 1; $page < 50; $page++) {

    //             if ($page > 1) {
    //                 $testcontent = file_get_contents("https://epapervijayavani.in/ArticlePage/APpage.php?edn=" . $cityarray[$edition] . "&articleid=VVAANINEW_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_1");
    //                 $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];
    //                 $testimageInfo = getimagesize($testimagelink);

    //                 if (!$testimageInfo)
    //                     break;
    //             }

    //             for ($section = 1; $section < 100; $section++) {
    //                 $content = file_get_contents("https://epapervijayavani.in/ArticlePage/APpage.php?edn=" . $cityarray[$edition] . "&articleid=VVAANINEW_" . $citycode[$edition] . "_" . $dateForLinks . "_" . $page . "_" . $section);
    //                 if ($content) {
    //                     $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];
    //                     $imageInfo = getimagesize($imagelink);



    //                     if (!$imageInfo && $section > 3)
    //                         break;


    //                     $filepath = "/nvme/VV_" . str_replace($cityarray[0], "Bangalore", $cityarray[$edition]) . "_" . $filenamedate . "_" . $page  . "00" . $section . "_admin_kan.jpg";
    //                     $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                     $txtfile = "./imagestext/VV_" . str_replace($cityarray[0], "Bangalore", $cityarray[$edition]) . "_" . $filenamedate . "_" . $page  . "00" . $section . "_admin_kan.txt";


    //                     $image = file_get_contents($imagelink);


    //                     $handle = fopen($filepath, "w");
    //                     fwrite($handle, $image);
    //                     fclose($handle);

    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                     try {
    //                         $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l kan > /dev/null 2>&1";
    //                         exec($command);
    //                         $text = file_get_contents($temp_txtfile . ".txt");

    //                         $matches = array();
    //                         preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                         $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                         foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                         $n = count($matches);

    //                         if ($n == 01) {
    //                             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                         } else {
    //                             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                             rename($temp_txtfile . ".txt", $txtfile);
    //                         }
    //                     } catch (Exception $e) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                     }
    //                 }
    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
    //     }
    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Vijayavani','VV','" . $filenamedate . "')");
    // }

    // if ($epapercode == "YB") {
    //     $dateForLinks = date('Ymd', strtotime($filenamedate));


    //     for ($page = 1; $page < 50; $page++) {
    //         for ($section = 1; $section < 100; $section++) {
    //             $content = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $dateForLinks . "_" .  $page . "_" . $section);
    //             if ($content) {
    //                 $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
    //                 $imageInfo = getimagesize($imagelink);
    //                 if (!$imageInfo)
    //                     break;

    //                 $filepath = "/nvme/YB_Mumbai" . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_hin.jpg";
    //                 $temp_txtfile = str_replace(".jpg", "", $filepath);
    //                 $txtfile = "./imagestext/YB_Mumbai" . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_hin.txt";

    //                 $image = file_get_contents($imagelink);

    //                 $handle = fopen($filepath, "w");
    //                 fwrite($handle, $image);
    //                 fclose($handle);

    //                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    //                 try {
    //                     $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
    //                     exec($command);
    //                     $text = file_get_contents($temp_txtfile . ".txt");

    //                     $matches = array();
    //                     preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
    //                     $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
    //                     foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
    //                     $n = count($matches);

    //                     if ($n == 0) {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
    //                     } else {
    //                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
    //                         rename($temp_txtfile . ".txt", $txtfile);
    //                     }
    //                 } catch (Exception $e) {
    //                     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
    //                 }
    //             }
    //             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
    //         }
    //         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
    //     }
    //     exec("rm -f /nvme/*");
    //     mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('yashobhumi','YB','" . $filenamedate . "')");
    // }
}

?>