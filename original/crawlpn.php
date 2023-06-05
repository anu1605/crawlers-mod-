<?php

error_reporting(E_ERROR);
set_time_limit(1800);

require  "/var/www/d78236gbe27823/vendor/autoload.php";
use thiagoalessio\TesseractOCR\TesseractOCR;

$date = date('Ymd',time());
$filenamedate = date('Y-m-d',time());

function findLastPage($url) {
  $min = 1;
  $max = 100;
  while ($min <= $max) {
    $mid = floor(($min + $max) / 2);
    $testUrl = str_replace("_01_","_".sprintf('%02d', $mid)."_",$url);
    $response = @file_get_contents($testUrl);
    if (!strpos($response,'/ArticleImages/')){
      $max = $mid - 1;
    } else {
      $min = $mid + 1;
    }
  }
  return $max;
}

function findLastArticle($url) {
  $min = 1;
  $max = 100;
  while ($min <= $max) {
    $mid = floor(($min + $max) / 2);
    $testUrl = $url."_".$mid;
    $response = @file_get_contents($testUrl);
    if (!strpos($response,'/ArticleImages/')){
      $max = $mid - 1;
    } else {
      $min = $mid + 1;
    }
  }
  return $max;
}

$paperArray = Array("Mumbai","Pune","Nashik","Nagpur","Kolhapur","Satara","Solapur","Jalgaon","Dhule","Nanded","Thane","Latur","Ahmednagar");

$paperCode = Array("PM","PU","NS","NAG","KOL","STR","SOL","JAL","DHU","NDD","PT","LTR","AH");

$number = 1;

for($edition = 0; $edition < count($paperCode); $edition++){

    $imageLinkPage1 = "http://epunyanagari.com/articlepage.php?articleid=PNAGARI_".$paperCode[$edition]."_".$date."_01_1";
    $lastPage = findLastPage($imageLinkPage1);

    echo "Last Page in ".$paperArray[$edition]." is ".$lastPage."<br><br>";

    for($currentpage = 1; $currentpage <= $lastPage; $currentpage++){

        $articleURL = "http://epunyanagari.com/articlepage.php?articleid=PNAGARI_".$paperCode[$edition]."_".$date."_".sprintf('%02d', $currentpage);
        $lastArtcile = findLastArticle($articleURL);

        echo "Last Article in ".$paperArray[$edition]." Page ".$currentpage." is ".$lastArtcile."<br><br>";

        for($articleno = 1; $articleno <= $lastArtcile; $articleno++){

            echo "Now crawling through Article No. ".$articleno."......";

            $thisArticleURL = "http://epunyanagari.com/articlepage.php?articleid=PNAGARI_".$paperCode[$edition]."_".$date."_".sprintf('%02d', $currentpage)."_".$articleno;

            $articleResponse = file_get_contents($thisArticleURL);

            echo $imageURL = explode('"',explode('<link rel="image_src" href="',$articleResponse)[1])[0]; echo "<br>";
            $image = file_get_contents($imageURL);

            $filepath = "/var/www/d78236gbe27823/marketing/Whatsapp/pn_images/PN_".$paperArray[$edition]."_".$filenamedate."_".$number."_admin_mar.jpg";

            $number++;

            $handle = fopen($filepath,"w");
            fwrite($handle,$image);
            fclose($handle);

            echo "Saved to ".str_replace("/var/www/d78236gbe27823/marketing/Whatsapp/pn_images/","",$filepath).".....";

            $text = (new TesseractOCR($filepath))->run();

            $matches = array();
            preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
            $matches = str_replace("+91","",str_replace("\n","",str_replace("-","",str_replace(" ","",$matches[0]))));
            foreach ($matches as $match => $val) $matches[$match] = ltrim($val,"0");
            $n = count($matches);

            if($n < 2){
                echo 'Does not seem to be a classifieds page..... deleting<br>';                
                unlink($filepath);
            }
	    else{
		rename($filepath,str_replace("pn_images","images",$filepath));
                echo 'Identified as a classifieds page..... check it out here: <a href = "https://marketing.buzzgully.com/'.str_replace("/var/www/d78236gbe27823/","",$filepath).'" target="_blank">'.str_replace("/var/www/d78236gbe27823/marketing/Whatsapp/images/","",$filepath).'</a><br>';
            }

            echo "<br>";

            ob_flush();
            flush();

        }

    }

}

?>
