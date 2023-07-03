
<?php


error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

include "../../includes/connect.php";
$finddateq = "Select * from Crawl_Record WHERE Papershortname='SY' ORDER BY Paperdate DESC LIMIT 1";
$finddaters = mysqli_query($conn, $finddateq);
if (mysqli_num_rows($finddaters)) {
    $finddaterow = mysqli_fetch_array($finddaters);
    $filenamedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
} else $filenamedate = date('Y-m-d', time());
$dateForLinks = date('Ymd', strtotime($filenamedate));

$cityArray  =  array("Mangalore", "Davangere", "Kalaburgi", "Hubli", "Bangalore");
$citycode = array("MANG", "DAVN", "KALB", "HUB",  "BANG",);

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting SY\n";

for ($edition = 0; $edition < count($cityArray); $edition++) {
    for ($page = 1; $page < 50; $page++) {
        $testcontent  = file_get_contents("http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_1");
        $testimagelink = explode('"', explode('id="artimg" src="', $testcontent)[1])[0];

        if (!empty($testimagelink)) $testimageInfo = @getimagesize($testimagelink);


        if (!$testimageInfo)
            break;

        for ($section = 1; $section < 100; $section++) {
            $link =   "http://epaper.samyukthakarnataka.com/articlepage.php?articleid=SMYK_" . $citycode[$edition] . "_" . $dateForLinks . "_" . sprintf("%02d", $page) . "_" . $section;
            $content = file_get_contents($link);


            $imagelink = explode('"', explode('id="artimg" src="', $content)[1])[0];

            if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);


            if (!$imageInfo)
                break;


            $filepath = "/nvme/SY_" .  $cityArray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_kan.jpg";
            $temp_txtfile = str_replace(".jpg", "", $filepath);
            $txtfile = "./imagestext/SY_" .  $cityArray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_kan.txt";


            $image = file_get_contents($imagelink);



            $handle = fopen($filepath, "w");
            fwrite($handle, $image);
            fclose($handle);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

            try {
                $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l kan > /dev/null 2>&1";
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
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $page . " Section " . $section . " Completed\n";
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Page " . $page . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityArray[$edition] . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All Editions Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Samyukta Karnataka','SY','" . $filenamedate . "')");

?>