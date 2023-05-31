

<?php
echo dirname(__FILE__);

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);




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

$filenamedate = date('Y-m-d', time());
$dateForLinks = date('Ymd', time());


//$paperStringFull = Array("agra-city","aligarh-city","almora","ambala","ambedkar-nagar","amethi","jpnagar","auraiya","faizabad","azamgarh","badaun","baghpat","bahraich","balia","balrampur","banda","barabanki","bareilly-city","basti","bhadohi","bhiwani","bhopal","bijnor","bilaspur","bulandsahar","chamba","chandauli","chandigarh-city","charkhi-dadri","dehradun-city","delhi-city","deoria","etah","etawa","faridabad","farrukhabad","fatehabad","fatehpur","firozabad","garhwal","ghaziabad","trans-hindon-area","ghazipur","gonda-balrampur","gorakhpur-city","greater-noida","gurgaon","hamirpur-dharamshala","hamirpur","hapur","hardoi","haridwar","hathras","hisar","jalandhar","jalaun","jammu-city","jounpur","jhajjar","jhansi-city","jind","kaithal","kangra","kannauj","kanpur-ghatampur","kanpur-city","karnal","kasganj","kathua","kaushambi","kotdwar","kullu","kurukshetra","kushinagar","khiri","lalitpur","lucknow-city","mharajgunj","mainpuri","mandi","mathura","mau","meerut-city","mirzapur","mohali","moradabad-city","muzaffarnagar","haldwani","narnaul","noida","panchkula","panipat","pilibhit","pithoragarh","pratapgarh","allahabad-city","gangapar","prayagraj-naini","raebareli","rajasthan","rampur-dharamshala","rampur","rewari","rishikesh","rohtak-city","roorkee","saharanpur-city","sambhal","santkabirnagar","shahjahanpur","shimla","siddharthnagar","sirmaur","sirsa","sitapur","solan","sonbhadra","sonipat","sultanpur","udhampur","udhamsingh-nagar","una","unnao","varanasi-city","vikas-nagar","yamuna-nagar");

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
?>