<?php

error_reporting(E_ERROR);
set_time_limit(1800);

require  "/var/www/d78236gbe27823/vendor/autoload.php";

use thiagoalessio\TesseractOCR\TesseractOCR;

$date = date('Ymd', time());
$filenamedate = date('Y-m-d', time());



$paperArray =
  array("Mumbai", "Pune", "Nashik", "Nagpur", "Kolhapur", "Satara", "Solapur", "Jalgaon", "Dhule", "Nanded", "Thane", "Latur", "Ahmednagar");

$paperCode = array("PM", "PU", "NS", "NAG", "KOL", "STR", "SOL", "JAL", "DHU", "NDD", "PT", "LTR", "AH");

$number = 1;

for ($edition = 0; $edition < count($paperCode); $edition++) {


  for ($page = 1; $page <= $lastPage; $page++) {
    $testcontent = file_get_contents("http://epunyanagari.com/articlepage.php?articleid=PNAGARI_" . $paperCode[$edition] . "_" . $date . "_" . sprintf('%02d', $page) . "_1");
    $testimagelink = explode('"', explode('<link rel="image_src" href="', $testcontent)[1])[0];
    if (!getimagesize($testimagelink))
      break;
    for ($articleno = 1; $articleno <= $lastArtcile; $articleno++) {

      $response = file_get_contents("http://epunyanagari.com/articlepage.php?articleid=PNAGARI_" . $paperCode[$edition] . "_" . $date . "_" . sprintf('%02d', $page) . "_" . $articleno);

      echo $$imagelink = explode('"', explode('<link rel="image_src" href="', $articleResponse)[1])[0];
      echo "<br>";

      ob_flush();
      flush();
    }
  }
}
