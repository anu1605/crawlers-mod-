

<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

$content = file_get_contents("http://karnatakamalla.com/");
$getdate = explode('&pn', explode('KARMAL_MAI&date=', $content)[1])[0];
$time = strtotime($getdate);
$kmdate = date('Ymd', $time);
$kmfilenamedate = date('Y-m-d', $time);

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting KM\n";


for ($page = 1; $page < 100; $page++) {
    $testlink = "http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $kmdate . "_" .  $page . "_1";
    $testcontent = file_get_contents($testlink);
    $testimagelink = explode('"', explode('id="artimg"  src="', $testcontent)[1])[0];
    $testimageInfo = getimagesize($testimagelink);
    if (!$testimageInfo)
        break;


    for ($section = 1; $section < 100; $section++) {
        $content = file_get_contents("http://karnatakamalla.com/articlepage.php?articleid=KARMAL_MAI_" . $kmdate . "_" .  $page . "_" . $section);
        if ($content) {
            $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
            $imageInfo = getimagesize($imagelink);
            if (!$imageInfo)
                break;


            $filepath = "/nvme/KM_Karnataka" . "_" . $kmfilenamedate . "_" . $page . "00" . $section . "_admin_kan.jpg";
            $temp_txtfile = str_replace(".jpg", "", $filepath);
            $txtfile = "./imagestext/KM_Karnataka" . "_" . $kmfilenamedate . "_" . $page . "00" . $section . "_admin_kan.txt";


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
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
?>