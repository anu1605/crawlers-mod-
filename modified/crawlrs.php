

<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

include "../../includes/connect.php";
$finddateq = "Select * from Crawl_Record WHERE Papershortname='RS' ORDER BY Paperdate DESC LIMIT 1";
$finddaters = mysqli_query($conn, $finddateq);
if (mysqli_num_rows($finddaters)) {
    $finddaterow = mysqli_fetch_array($finddaters);
    $filenamedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
} else $filenamedate = date('Y-m-d', time());
$dateForLinks = date('dmY', strtotime($filenamedate));


$cityarray = array("Delhi", "Lucknow", "Patna", "Dehradun", "Kanpur", "Gorakhpur", "Varanasi");
$linkarray = array("http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-hr-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-lu-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-pt-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-dd-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-kn-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//" . $dateForLinks . "-gp-md-1ll.png", "http://sahara.4cplus.net/epaperimages//" . $dateForLinks . "//29052023-vn-md-1ll.png");

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting RS\n";

for ($edition = 0; $edition < count($cityarray); $edition++) {
    for ($page = 1; $page < 50; $page++) {
        $imagelink = str_replace("md-1", "md-" . $page, $linkarray[$edition]);
        if (file_get_contents($imagelink)) {
            $filepath = "/nvme/RS_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "_admin_hin.jpg";
            $temp_txtfile = str_replace(".jpg", "", $filepath);
            $txtfile = "./imagestext/RS_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "_admin_hin.txt";

            $image = file_get_contents($imagelink);

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
        } else break;
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
}

exec("rm -f /nvme/*");
echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All Editions Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Rashtriya Sahara','RS','" . $filenamedate . "')");

?>