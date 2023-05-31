<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);


$date = date('Ymd', time());
$filenamedate = date('Y-m-d', time());

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting YB\n";

for ($page = 1; $page < 20; $page++) {

    for ($section = 1; $section < 30; $section++) {
        $content = file_get_contents("http://yeshobhumi.com/articlepage.php?articleid=YBHUMI_MAI_" . $date . "_" .  $page . "_" . $section);
        if ($content) {
            $imagelink = explode('"', explode('id="artimg"  src="', $content)[1])[0];
            $imageInfo = getimagesize($imagelink);
            if (!$imageInfo)
                break;


            $filepath = "/nvme/YB_Mumbai" . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_hin.jpg";
            $temp_txtfile = str_replace(".jpg", "", $filepath);
            $txtfile = "./imagestext/YB_Mumbai" . "_" . $filenamedate . "_" . $page . "00" . $section . "_admin_hin.txt";



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
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Section " . $section . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed\n";
}
exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
