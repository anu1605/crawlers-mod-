
<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);


$date = date('Ymd', time());
$filenamedate = date('Y-m-d', time());


$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, "https://www.mumbaichoufer.com/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
$data = curl_exec($ch);
curl_close($ch);
$datecode = explode("/mc", explode("/view/", $data)[1])[0];



$content =   file_get_contents("https://www.mumbaichoufer.com/view/" . $datecode . "/mc");
$getmcdate = trim(explode('-', explode('<title>Mumbaichoufer -', $content)[1])[0]);
$mcdate = date("Y-m-d", strtotime($getmcdate));
$firstId = explode('"', explode('{"mp_id":"', $content)[1])[0];
file_put_contents(dirname(__FILE__) . "/test.txt", $firstId);

$section1 = explode($firstId, $content)[1];
$idarray = explode('{"mp_id":"', $section1);


$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting MC\n";

for ($id = 1; $id < count($idarray) - 1; $id++) {
    $imageId = explode('"', $idarray[$id])[0];
    $imagelink = "https://www.mumbaichoufer.com/map-image/" . $imageId . ".jpg";


    $filepath = "/nvme/MC_Mumbai" . "_" . $filenamedate . "_" . $id . "_admin_mar.jpg";
    $temp_txtfile = str_replace(".jpg", "", $filepath);
    $txtfile = "./imagestext/MC_Mumbai" . "_" . $filenamedate . "_" . $id . "_admin_mar.txt";


    $image = file_get_contents($imagelink);


    $handle = fopen($filepath, "w");
    fwrite($handle, $image);
    fclose($handle);


    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

    try {
        $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l mar > /dev/null 2>&1";
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


    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $id . " Completed\n";
}
exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
?>