<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

include "../../includes/connect.php";
$finddateq = "Select * from Crawl_Record WHERE Papershortname='HB' ORDER BY Paperdate DESC LIMIT 1";
$finddaters = mysqli_query($conn, $finddateq);
if (mysqli_num_rows($finddaters)) {
    $finddaterow = mysqli_fetch_array($finddaters);
    $filenamedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
} else
    $filenamedate = date('Y-m-d', time());
$dateForLinks = date('Y/m/d', strtotime($filenamedate));


$array = explode(',', file_get_contents(dirname(__FILE__) . "./dependencies/hb.txt"));
$cityCode = array();
$newCodes = array();
foreach ($array as $val) {
    $codeFromString = explode('=>', $val)[1];
    array_push($cityCode, $codeFromString);
}

$cityArray = ["raipur", "bilaspur", "bhopal"];
$cityLink = ["raipur-raipur-main", "bilaspur-main", "bhopal-main"];

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting HB\n";


for ($edition = 0; $edition < count($cityArray); $edition++) {
    $code = $cityCode[$edition];
    $link = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $cityArray[$edition] . "-full-edition/" . $dateForLinks . "/" . $cityLink[$edition] . "/";

    if ($cityArray[$edition] == "raipur") {
        $link2 = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $cityArray[$edition] . "-full-edition/" . $dateForLinks . "/" . $cityArray[$edition] . "-main/";
        if (file_get_contents($link2 . $code)) {
            $link = $link2;
        }
    }
    if (!file_get_contents($link . $code)) {
        for ($i = 40; $i < 300; $i++) {
            $code = $cityCode[$edition] + $i;
            if ($cityArray[$edition] == "raipur") {
                $link2 = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $cityArray[$edition] . "-full-edition/" . $dateForLinks . "/" . $cityArray[$edition] . "-main/";
                if (file_get_contents($link2 . $code)) {
                    $link = $link2;
                    array_push($newCodes, strval($code));
                    break;
                }
            }
            if (file_get_contents($link . $code)) {
                array_push($newCodes, strval($code));
                break;
            }
        }
    }

    $content = file_get_contents($link . $code);
    $section1 = explode('id="slider-epaper" class="imageGalleryWrapper"><li data-index="0"', $content)[1];
    $section2 = explode('class="page-toolbar"><div id="page-level-nav"', $section1)[0];
    $linkArray = explode('data-big="', trim($section2));

    for ($imglink = 1; $imglink < count($linkArray); $imglink++) {
        $imageLink =  explode('"', $linkArray[$imglink])[0];
        $filepath = "/nvme/HB_" . ucwords($cityArray[$edition]) . "_" . $filenamedate . "_" . $imglink . "_admin_hin.jpg";
        $temp_txtfile = str_replace(".jpg", "", $filepath);
        $txtfile = "./imagestext/HB_" . ucwords($cityArray[$edition]) . "_" . $filenamedate . "_" . $imglink . "_admin_hin.txt";

        $image = file_get_contents($imageLink);

        $handle = fopen($filepath, "w");
        fwrite($handle, $image);
        fclose($handle);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

        try {
            $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l hin > /dev/null 2>&1";
            exec($command);
            $text = file_get_contents($temp_txtfile . ".txt");

            $matches = array();
            preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
            $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
            foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
            $n = count($matches);

            if ($n == 0) {
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No new numbers found\n";
            } else {
                echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved\n";
                rename($temp_txtfile . ".txt", $txtfile);
            }
        } catch (Exception $e) {
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run\n";
        }

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $imglink . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Completed\n";
}
if (count($newCodes) == 3) {
    $file = fopen(dirname(__FILE__, 1) . "./dependencies/hb.txt", 'w');
    $txt =  "raipur=>" . $newCodes[0] . ",bilaspur=>" . $newCodes[1] . ",bhopal=>" . $newCodes[2] . "";
    fwrite($file, $txt);
    fclose($file);
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All Editions Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Hari Bhumi','HB','" . $filenamedate . "')");
