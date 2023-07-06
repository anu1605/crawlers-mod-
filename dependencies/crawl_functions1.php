<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverDimension;


// require  '/var/www/d78236gbe27823/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;

if (php_sapi_name() == "cli") $eol = "\n";
else  $eol = "<br>";

function filenamedate($epapercode, $conn)
{
    // $finddateq = "Select * from Crawl_Record WHERE Papershortname='" . $epapercode . "' ORDER BY Paperdate DESC LIMIT 1";
    // $finddaters = mysqli_query($conn, $finddateq);
    // if (mysqli_num_rows($finddaters)) {
    //     $finddaterow = mysqli_fetch_array($finddaters);
    //     $filedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
    // } else
    $filedate = date('Y-m-d', time());

    return $filedate;
}

function makefilepath($epapercode, $city, $date, $number, $lang)
{
    // $filepath = "/nvme/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    $filepath = "./nvme/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    // $temp_txtfile = "./nvme/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".txt";
    $temp_txtfile = str_replace(".jpg", "", $filepath);
    // $txtfile = "/var/www/d78236gbe27823/marketing/Whatsapp/images/ocrtexts/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".txt";
    $txtfile = "./ocrtexts/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".txt";
    $newspaper_name = $epapercode;
    $newspaper_region = $city;
    $newspaper_date = $date;
    $newspaper_lang = $lang;
    $Image_file_name = $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    $newspaper_operator_name = 'admin';

    return $filepath . "&" . $temp_txtfile . "&" . $txtfile . "&" . $newspaper_name . "&" . $newspaper_region . "&" . $newspaper_date . "&" . $newspaper_lang . "&" . $Image_file_name . "&" . $newspaper_operator_name;
}
function alreadyDone($filepath, $conn)
{
    // $a = explode("/", $filepath)[2];
    // $b = explode("_", $a);
    // $newspaper_name = $b[0];
    // $edition = $b[1];
    // $date = $b[2];
    // if ($b[3] >= 1001) {
    //     $page = explode("00", $b[3])[0];
    //     $section = explode("00", $b[3])[1];
    // } else {
    //     $page = $b[3];
    //     $section = '0';
    // }

    // if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Crawl_Record WHERE Papershortname = '" . $newspaper_name . "' AND Paperdate = '" . $date . "'"))) return "Yes";

    // $q = "select * from Crawled_Pages WHERE Papershortname = '" . $newspaper_name . "' AND Paperdate = '" . $date . "' AND Edition = '" . $edition . "' AND Page = '" . $page . "' AND Section = '" . $section . "'";
    // $rs = mysqli_query($conn, $q);
    // if (mysqli_num_rows($rs) > 0) return "Yes";
    // else return "No";
}
function writeImage($url, $path)
{
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $image = @file_get_contents($url, false, stream_context_create($arrContextOptions));

    // If the file_get_contents function fails, it returns FALSE
    if ($image === false) {
        echo "Failed to get contents from URL: $url";
        return;
    }

    $handle = fopen($path, "w");

    // If the fopen function fails, it returns FALSE
    if ($handle === false) {
        echo "Failed to open file at path: $path";
        return;
    }

    fwrite($handle, $image);
    fclose($handle);
}

function runOpenCV($filepath, $conn)
{

    $b = explode("/", $filepath);
    $filepath = $b[count($b) - 1];

    $a = explode("_", $filepath);
    $papershortname = $a[0];
    $ImagesBaseDir = '/var/www/d78236gbe27823/nvme';
    $smallImageDir = $ImagesBaseDir . "/" . $papershortname;
    $largeImagePath = $ImagesBaseDir . "/" . $filepath;

    $q = "INSERT INTO opencv (Large_Image) VALUES ('" . $filepath . "') ON DUPLICATE KEY UPDATE Large_Image = VALUES(Large_Image)";
    mysqli_query($conn, $q);

    $found = false;

    $iteration = 0;

    $prevaccuracy = 0;

    $smallImages = glob("$smallImageDir/*.{jpeg,jpg,png}", GLOB_BRACE);

    natsort($smallImages);

    foreach ($smallImages as $smallImagePath) {

        $iteration++;

        $outputVars = exec("iii " . $largeImagePath . " " . $smallImagePath);

        $output = explode("~~", $outputVars)[0];
        $accuracy = explode("~~", $outputVars)[1];

        $b = explode("/", $smallImagePath);

        $current_small_image = $b[count($b) - 1];

        echo $current_small_image . " | " . $accuracy . "<br>";

        if ($accuracy > $prevaccuracy) {
            $updq = "UPDATE opencv SET Small_Image = '" . $current_small_image . "', Accuracy = '" . $accuracy . "' WHERE Large_Image = '" . $filepath . "'";
            mysqli_query($conn, $updq);
            $prevaccuracy = $accuracy;
        }

        if (trim($output) == 'Image found.') {
            mysqli_query($conn, "UPDATE opencv SET Small_Image = '" . $current_small_image . "', AI_Decision = 'Approved' WHERE Large_Image = '" . $filepath . "'");
            return true;
        }
    }
    return false;
}

// function runTesseract($epapername, $edition, $page, $section, $conn, $patharray, $lang)
// {
//     global $eol;

//     $opencvPapers = array("SOM", "RS", "MC", "NVR", "OHO", "DST", "DC", "GSM", "LM", "NBT", "DJ", "ND", "NB", "AU", "BS", "DN", "ASP", "PN", "YB");

//     $filepath = $patharray[0];
//     $temp_txtfile = $patharray[1];
//     $txtfile = $patharray[2];
//     $newspaper_name = $patharray[3];
//     $newspaper_region = $patharray[4];
//     $newspaper_date = $patharray[5];
//     $newspaper_lang = $patharray[6];
//     $Image_file_name = $patharray[7];
//     $newspaper_operator_name = $patharray[8];
//     $starttime = date('Y-m-d H:i:s', time());

//     $emergencyStopQ = "SELECT Emergency_STOP FROM Emergency WHERE Instruction_For = 'crawl.php'";
//     $emergencyStopRS = mysqli_query($conn, $emergencyStopQ);
//     $emergencyStopRow = mysqli_fetch_array($emergencyStopRS);

//     if ($emergencyStopRow['Emergency_STOP'] == "STOP") {

//         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $newspaper_region . " Page " . $page . " Section " . $section . " Completed" . $eol;
//         mysqli_query($conn, "UPDATE Emergency SET Emergency_STOP = 'Keep Going' WHERE Instruction_For = 'crawl.php'");
//         die($eol . $eol . date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . "EMERGENCY STOP CALLED" . $eol . $eol);
//     }

//     try {

//         if ($lang != 'eng') $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l " . $lang . "+eng > /dev/null 2>&1";
//         else $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l eng > /dev/null 2>&1";

//         exec($command);

//         $text = file_get_contents($temp_txtfile . ".txt");

//         $matches = array();
//         preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
//         $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));

//         foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");

//         $n = count($matches);

//         if ($n < 5) {

//             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No numbers found" .  $eol;
//         } else {

//             echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " numbers found. File Saved" . $eol;

//             rename($temp_txtfile . ".txt", $txtfile);

//             echo $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Starting to add in the database...";

//             $values = "";
//             $values_non_unique = "";

//             for ($i = 0; $i < $n; $i++) {

//                 $blockcheck = "select * from Blocked_Numbers where Mobile_No = '" . $matches[$i] . "'";
//                 $bcrs = mysqli_query($conn, $blockcheck);
//                 if (!mysqli_num_rows($bcrs)) {
//                     $values .= "('" . $matches[$i] . "','" . $newspaper_name . "','" . $newspaper_region . "','" . $newspaper_date . "','" . $newspaper_lang . "','" . $Image_file_name . "','" . $newspaper_operator_name . "'),";
//                     $values_non_unique .= "('" . $matches[$i] . "','" . $newspaper_name . "','" . $newspaper_region . "','" . $newspaper_date . "','" . $newspaper_lang . "','" . $Image_file_name . "','" . $newspaper_operator_name . "'),";
//                 } else echo "" . $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Skipping " . $matches[$i] . " found in blocked numbers";
//             }

//             if (strlen($values) > 0) {

//                 $values = substr($values, 0, strlen($values) - 1) . " ON DUPLICATE KEY UPDATE Newspaper_Name = VALUES(Newspaper_Name), Newspaper_Region = VALUES(Newspaper_Region), Newspaper_Date = VALUES(Newspaper_Date), Newspaper_Lang = VALUES(Newspaper_Lang);";

//                 $values_non_unique = substr($values_non_unique, 0, strlen($values_non_unique) - 1);

//                 echo $eol . "=================================" . $eol;
//                 echo $q_non_unqiue = "insert into Mobile_Lists_NON_Unique (Mobile_Number,Newspaper_Name,Newspaper_Region,Newspaper_Date,Newspaper_Lang,Image_File_Name,Image_Operator) values " . $values_non_unique;
//                 echo $eol;
//                 echo $q = "insert into Mobile_Lists (Mobile_Number,Newspaper_Name,Newspaper_Region,Newspaper_Date,Newspaper_Lang,Image_File_Name,Image_Operator) values " . $values;
//                 echo $eol . "=================================" . $eol;

//                 if (!mysqli_query($conn, $q)) {
//                     echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Error in insert query... ABORTING!" . $eol . $q . "" . $eol;
//                     die();
//                 }

//                 if (!mysqli_query($conn, $q_non_unqiue)) {
//                     echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Error in insert query... ABORTING!" . $eol . $q_non_unqiue . "" . $eol . $eol . mysqli_error($conn);
//                     die();
//                 }

//                 echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=> Running OpenCV to STOP Sending of messages on Non Classified Decisions FROM AI" .  $eol;

//                 if (in_array($newspaper_name, $opencvPapers)) {

//                     if (!runOpenCV($filepath, $conn)) {
//                         $b = explode("/", $filepath);
//                         $filename = $b[count($b) - 1];
//                         mysqli_query($conn, "UPDATE Mobile_Lists SET Sending_Approved='No' WHERE Image_File_Name = '" . $filename . "'");
//                         mysqli_query($conn, "UPDATE Mobile_Lists_NON_Unique SET Sending_Approved='No' WHERE Image_File_Name = '" . $filename . "'");
//                         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>OpenCV Completed. " . $filepath . " is not a classified page. Stopped Sending on numbers from this image" .  $eol;
//                     }
//                 }
//                 echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Insert query executed successfully......" . $eol;
//             } else echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "No numbers left to insert";
//         }

//         $iq = "INSERT IGNORE INTO Crawled_Pages (Papername,Papershortname,Paperdate,Edition,Page,Section,No_Of_Mobiles_Found,Start_Time) VALUES ('" . $epapername . "','" . $newspaper_name . "','" . $newspaper_date . "','" . $newspaper_region . "','" . $page . "','" . $section . "','" . count($matches) . "','" . $starttime . "')";

//         echo $eol . $iq . "" . $eol;

//         mysqli_query($conn, $iq);
//     } catch (Exception $e) {
//         echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run" . $eol;
//     }
// }
function runTesseract($epapername, $edition, $page, $section, $conn, $patharray, $lang)
{
    global $eol;

    $opencvPapers = array("SOM", "RS", "MC", "NVR", "OHO", "DST", "DC", "GSM", "LM", "NBT", "DJ", "ND", "NB", "AU", "BS", "DN", "ASP", "PN", "YB");

    $filepath = $patharray[0];
    $temp_txtfile = $patharray[1];
    $txtfile = $patharray[2];
    $newspaper_name = $patharray[3];
    $newspaper_region = $patharray[4];
    $newspaper_date = $patharray[5];
    $newspaper_lang = $patharray[6];
    $Image_file_name = $patharray[7];
    $newspaper_operator_name = $patharray[8];
    $starttime = date('Y-m-d H:i:s', time());

    try {

        if ($lang != 'eng') $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l " . $lang . "+eng";
        else $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l eng";

        exec($command);

        $text = file_get_contents($temp_txtfile . ".txt");

        $matches = array();
        preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
        $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));

        foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");

        $n = count($matches);

        if ($n < 5) {

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. No numbers found" .  $eol;
        } else {

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " numbers found. File Saved" . $eol;

            rename($temp_txtfile . ".txt", $txtfile);
        }
    } catch (Exception $e) {
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run" . $eol;
    }
}
function getHBeditionlink($city, $dateforlinks, $citylink, $code)
{
    $link = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $city . "-full-edition/" . $dateforlinks . "/" . $citylink . "/";

    if ($city == "raipur") {
        $link2 = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $city . "-full-edition/" . $dateforlinks . "/" . $city . "-main/";
        if (file_get_contents($link2 . $code)) {
            $link = $link2;
        }
    }
    return $link;
}

function crawltoi($cityarray, $dateForLinks, $epapercode, $citycode, $filenamedate, $eol, $conn, $lang, $cities_of_interest, $epapername)
{
    for ($edition = 0; $edition < count($cityarray); $edition++) {

        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }

        $failedPageCount = 0;
        $date_formatted = date("Y/d/m", strtotime($dateForLinks));

        if ($epapercode == "Mirror" and $cityarray[$edition] == "Mumbai") {

            $date_array = explode("/", $dateForLinks);
            $processdate = $date_formatted = $date_array[2] . "-" . $date_array[1] . "-" . $date_array[0];
            $dayofweek = date('l', strtotime($processdate));
            if ($dayofweek <> 'Sunday') {
                echo "There is no Mirror published from Mumbai on " . $dayofweek . ", " . $dateForLinks . ". It publishes only on Sundays";
                continue;
            }
        }

        for ($page = 1; $page <= 40; $page++) {
            $imageFound = "No";

            if ($failedPageCount > 3) {
                echo "Seems Pages over. Skipping... " . $page . "\n";
                continue;
            }
            for ($section = 1; $section <= 50; $section++) {
                $imagelink = "https://asset.harnscloud.com/PublicationData/" . $epapercode . "/" . $citycode[$edition] . "/" . $date_formatted . "/Advertisement/" . str_pad($page, 3, "0", STR_PAD_LEFT) . "/" . str_replace("/", "_", $dateForLinks) . "_" . str_pad($page, 3, "0", STR_PAD_LEFT) . "_" . str_pad($section, 3, "0", STR_PAD_LEFT) . "_" . $citycode[$edition] . ".jpg";

                // if (strlen(file_get_contents($url) <= 0)) continue;

                $section_size_array = getimagesize($imagelink);
                $width = $section_size_array[0];


                if ($width > 0) {
                    $imageFound = "Yes";
                    $failedPageCount = 0;

                    if ($width > 200 and $width < 250) {
                        $getpath = explode("&", makefilepath($epapercode,  $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));
                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    } else continue;
                } else {
                    continue;
                }
            }

            if ($imageFound == "No") $failedPageCount++;

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
    }
}

function cityofinterest($city, $cities_of_interest, $eol)
{
    if (!in_array(ucfirst(explode("-", $city)[0]), $cities_of_interest)) {

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $city . " Edition. Doesn't fall in cities of interest" . $eol;
        return false;
    }
    return true;
}
function getdata($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows;U;Windows NT 5.1;en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function writeImageWithCurl($url, $path)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows;U;Windows NT 5.1;en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
    $data = curl_exec($ch);
    if (strpos($data, "404 - File or directory not found."))
        return true;

    $handle = fopen($path, "w");
    fwrite($handle, $data);
    fclose($handle);
    file_put_contents(dirname(__FILE__, 2) . "/test.txt", $data);
    curl_close($ch);
}

function setsizefunction($client, $link)
{
    $window = $client->getWebDriver()->manage()->window();
    $window->maximize(); // Maximize the window to ensure full page capture
    $client->request('GET', $link);

    $client->executeScript('window.scrollTo(0, document.body.scrollHeight);');
    $client->executeScript('window.scrollTo(1000, 0);');
    $window = $client->getWebDriver()->manage()->window();
    $scrollWidth = $client->executeScript('return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);');
    $scrollHeight = $client->executeScript('return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);');
    $window->setSize(new WebDriverDimension($scrollWidth, $scrollHeight));
}

function getCroppedImage($client, $element, $screenshot)
{
    // Find required element
    $dePageContainer = $client->findElement(WebDriverBy::id($element));

    // Get the location and size of the de-page-container element
    $location = $dePageContainer->getLocation();
    $size = $dePageContainer->getSize();

    // Calculate the coordinates for the screenshot
    $x = $location->getX();
    $y = $location->getY();
    $width = $size->getWidth();
    $height = $size->getHeight();

    // Take a screenshot of the specified section

    // $imageData = file_get_contents($screenshot);
    $image = imagecreatefromstring($screenshot);
    $croppedImage = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
    return $croppedImage;
}
