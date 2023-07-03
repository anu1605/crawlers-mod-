<?php
require_once 'vendor/autoload.php';

use Symfony\Component\Panther\Client;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverDimension;

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set("display_errors", "1");
set_time_limit(0);

// $check_if_extract_running = shell_exec("ps aux | grep extractPhoneNumber.php");
// $extract_runtimes = explode("extractPhoneNumber.php", $check_if_extract_running);
// $extract_howmanyruns = count($extract_runtimes);

// $check_if_crawl_running = shell_exec("ps aux | grep crawl.php");
// $crawl_runtimes = explode("crawl.php", $check_if_crawl_running);
// $howmanyruns_of_crawl = count($crawl_runtimes);

// if ($extract_howmanyruns > 3 or $howmanyruns_of_crawl > 5) die("Other related programmes are running");

// if (php_sapi_name() == "cli") $eol = "\n";
// else  $eol = "<br>";

// $arrContextOptions = array(
//     "ssl" => array(
//         "verify_peer" => false,
//         "verify_peer_name" => false,
//     ),
// );

$static_date = ''; // Production Value is ''
$no_of_papers_to_run = 0; // Production Value is 0
$no_of_editions_to_run = 0; // Production Value is 0
$no_of_pages_to_run_on_each_edition = 1; // Production Value is 50
$no_of_sections_to_run_on_each_page = 100; // Production Value is 100

//// code starts below ///

// include "/var/www/d78236gbe27823/includes/connect.php";
// include "/var/www/d78236gbe27823/marketing/Whatsapp/Crawlers/dependencies/crawl_functions.php";
include "dependencies/crawl_functions1.php";

$epapers = array("SKL" => "Sakal,mar");
// $epapers = array("GSM" => "Gujarat Samachar,guj", "NVR" => "Navrasthra,mar", "SOM" => "Star of Mysore,kan", "NHT" => "Nav Hind Times,eng", "OHO" => "O Heral O,eng", "AP" => "Anandabazar Patrika,ben", "ASP" => "Asomiya Pratidin,asm", "BS" => "Bombay Samachar,guj", "AU" => "Amar Ujala,hin", "DC" => "Deccan Chronicle,eng", "HB" => "Hari Bhumi,hin", "DJ" => "Danik Jagran,hin", "LM" => "Lokmat,mar", "MC" => "Mumbai Chaufer,mar", "NB" => "Navbharat,hin", "NBT" => "Navbharat Times,hin", "ND" => "Nai Dunia,hin", "RS" => "Rashtriya Sahara,hin", "YB" => "yashobhumi,hin", "PN" => "Punayanagri,mar", "TOI" => "Times of India,eng", "ET" => "Economic Times,eng", "MT" => "Maharashtra Times,eng", "Mirror" => "Mirror,eng", "ESM" => "EiSamay,ben", "DN" => "Dainik Navjyoti,hin", "DST" => "Dainik Savera times,hin", "DHM" => "Daily Hindi Milap,hin", "HTV" => "Hitavada,eng", "NGS" => "Nav Gujarat Samay,guj", "PBK" => "Prabhat Khabar,hin");

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

    if ($epapercode == "TOI" or $epapercode == "ET" or $epapercode == "MT" or $epapercode == "Mirror") include("TOIGROUP.php");
    else include($epapercode . ".php");

    // mysqli_query($conn, "INSERT IGNORE INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('" . $epapername . "','" . $epapercode . "','" . $filenamedate . "')");
}
