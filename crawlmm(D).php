<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

$filenamedate = date('Y-m-d', time());

$data = file_get_contents("https://epaper.mysurumithra.com/");

$datecode = explode('/', explode('href="/epaper/edition/', $data)[1])[0];

$content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra/page/1");
$imagelinks =   explode('"><img src="', $content);

$starttime = time();

echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting MM\n";

for ($link = 1; $link < count($imagelinks); $link++) {

    $imagelink = explode('"', explode('"><img src="', $content)[$link])[0];

    $filepath = "/nvme/MM_Mysore" . "_" . $filenamedate . "_" . $link . "_admin_kan.jpg";
    $temp_txtfile = str_replace(".jpg", "", $filepath);
    $txtfile = "./imagestext/MM_Mysore" . "_" . $filenamedate . "_" . $link . "_admin_kan.txt";

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
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $link . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
