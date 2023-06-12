<?php

error_reporting(E_ERROR);
set_time_limit(1800);

require  "/var/www/d78236gbe27823/vendor/autoload.php";

use thiagoalessio\TesseractOCR\TesseractOCR;

$date = date('Ymd', time());
$filenamedate = date('Y-m-d', time());



$paperArray = array("Mumbai", "Pune", "Nashik", "Nagpur", "Kolhapur", "Satara", "Solapur", "Jalgaon", "Dhule", "Nanded", "Thane", "Latur", "Ahmednagar");

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
      $image = file_get_contents($imageURL);

      $filepath = "/var/www/d78236gbe27823/marketing/Whatsapp/pn_images/PN_" . $paperArray[$edition] . "_" . $filenamedate . "_" . $number . "_admin_mar.jpg";

      $number++;

      $handle = fopen($filepath, "w");
      fwrite($handle, $image);
      fclose($handle);

      echo "Saved to " . str_replace("/var/www/d78236gbe27823/marketing/Whatsapp/pn_images/", "", $filepath) . ".....";

      $text = (new TesseractOCR($filepath))->run();

      $matches = array();
      preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
      $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
      foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
      $n = count($matches);

      if ($n < 2) {
        echo 'Does not seem to be a classifieds page..... deleting<br>';
        unlink($filepath);
      } else {
        rename($filepath, str_replace("pn_images", "images", $filepath));
        echo 'Identified as a classifieds page..... check it out here: <a href = "https://marketing.buzzgully.com/' . str_replace("/var/www/d78236gbe27823/", "", $filepath) . '" target="_blank">' . str_replace("/var/www/d78236gbe27823/marketing/Whatsapp/images/", "", $filepath) . '</a><br>';
      }

      echo "<br>";

      ob_flush();
      flush();
    }
  }
}
