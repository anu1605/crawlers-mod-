
<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);



$linkDate = date('dmY', time());
$date = date('Ymd', time());
$filenamedate = date('Y-m-d', time());
$content = file_get_contents("https://samajaepaper.in/epaper/1/73/" . $date . "/1");
$pageArray = explode("class='map", $content);


$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting SMJ\n";

for ($page = 1; $page < count($pageArray); $page++) {
    $sections = explode("show_pop('", $pageArray[$page]);
    for ($sec = 1; $sec < count($sections); $sec++) {
        $name = explode("','", $sections[$sec])[1];
        $link = "https://samajaepaper.in/epaperimages/" . $linkDate . "/" . $linkDate . "-md-bh-" . $page . "/" . $name . ".jpg";



        $filepath = "/nvme/SMJ_Bhubaneswar_" . $filenamedate . "_" . $page . "00" . $sec . "_admin_ori.jpg";
        $temp_txtfile = str_replace(".jpg", "", $filepath);
        $txtfile = "./imagestext/SMJ_Bhubaneswar_" . $filenamedate . "_" . $page . "00" . $sec . "_admin_ori.txt";


        $image = file_get_contents($link);


        $handle = fopen($filepath, "w");
        fwrite($handle, $image);
        fclose($handle);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

        try {
            $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l ori > /dev/null 2>&1";
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
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $sec . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
?>