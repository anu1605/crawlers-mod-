

<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);


$date = date('Y-m-d', time());
$linkdate = date('dmY', time());


$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting NYB\n";


for ($pageNumber = 1; $pageNumber <= 100; $pageNumber++) {
    if ($pageNumber == 1)
        $page = "index.php";
    else $page = "page" . $pageNumber . ".php";


    $content = file_get_contents("https://niyomiyabarta.com/epaper/" . $linkdate . "/" . $page);


    if ($content) {
        $section1 = explode('<map name="Map2"', $content)[1];
        $section2 = explode('</map>', $section1)[0];
        $linkArray = explode("redirectme('", $section2);

        for ($page = 1; $page < count($linkArray); $page++) {
            $pageName = explode("',", $linkArray[$page])[0];
            $imageLink =  "https://niyomiyabarta.com/epaper/" . $linkdate . "/images/p" . $pageNumber . "/" . $pageName;
            $filepath = "/nvme/NYB_Guwahati_" . $date . "_" . $pageNumber . "00" . $page . "_admin_asm.jpg";
            $temp_txtfile = str_replace(".jpg", "", $filepath);
            $txtfile = "./imagestext/NYB_Guwahati_" . $date . "_" . $pageNumber . "00" . $page . "_admin_asm.txt";


            $image = file_get_contents($imageLink);


            $handle = fopen($filepath, "w");
            fwrite($handle, $image);
            fclose($handle);

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $filepath . " Saved\n";

            try {
                $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l asm > /dev/null 2>&1";
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
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $pageNumber . " Section " . $page . " Completed\n";
        }
    } else break;
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $pageNumber . " Completed\n";
}


exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
?>