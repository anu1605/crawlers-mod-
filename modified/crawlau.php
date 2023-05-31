<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

include "../../includes/connect.php";
$finddateq = "Select * from Crawl_Record WHERE Papershortname='AU' ORDER BY Paperdate DESC LIMIT 1";
$finddaters = mysqli_query($conn, $finddateq);
if (mysqli_num_rows($finddaters)) {
    $finddaterow = mysqli_fetch_array($finddaters);
    $filenamedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
} else $filenamedate = date('Y-m-d', time());
$dateForLinks = date('Ymd', strtotime($filenamedate));

function findLastPage($url)
{
    $min = 1;
    $max = 100;
    while ($min <= $max) {
        $mid = floor(($min + $max) / 2);
        $testUrl = str_replace("/01/hdimage.jpg", "/" . sprintf('%02d', $mid) . "/hdimage.jpg", $url);
        $response = @file_get_contents($testUrl);
        if ($response === false) {
            $max = $mid - 1;
        } else {
            $min = $mid + 1;
        }
    }
    return $max;
}


$paperString = array("agra-city", "ambala", "bhopal", "bilaspur", "chandigarh-city", "dehradun-city", "delhi-city", "faridabad", "ghaziabad", "gurgaon", "haridwar", "jalandhar", "jammu-city", "jhansi-city", "kanpur-city", "kurukshetra", "lucknow-city", "meerut-city", "mohali", "moradabad-city", "noida", "allahabad-city", "shimla", "varanasi-city");

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting AU\n";

for ($edition = 0; $edition < count($paperString); $edition++) {
    $response = file_get_contents("https://epaper.amarujala.com/" . $paperString[$edition] . "/" . $dateForLinks . "/01.html?format=img&ed_code=" . $paperString[$edition]);

    $a = explode('/hdimage.jpg"', $response);
    $b = explode('<link rel="preload" href="', $a[0]);
    $imageLinkPage1 = $b[1] . "/hdimage.jpg";
    $lastPage = findLastPage($imageLinkPage1);

    for ($i = 1; $i <= $lastPage; $i++) {

        $pgImageURL = str_replace("/01/hdimage.jpg", "/" . sprintf('%02d', $i) . "/hdimage.jpg", $imageLinkPage1);

        $filepath = "/nvme/AU_" . ucwords(explode("-", $paperString[$edition])[0]) . "_" . $filenamedate . "_" . $i . "_admin_hin.jpg";
        $temp_txtfile = str_replace(".jpg", "", $filepath);
        $txtfile = "./imagestext/AU_" . ucwords(explode("-", $paperString[$edition])[0]) . "_" . $filenamedate . "_" . $i . "_admin_hin.txt";

        $image = file_get_contents($pgImageURL);

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
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $paperString[$edition] . " Page " . $i . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $paperString[$edition] . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All Editions Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Amar Ujala','AU','" . $filenamedate . "')");
