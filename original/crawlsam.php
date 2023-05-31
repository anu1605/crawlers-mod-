

<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);


$filenamedate = date("Y-m-d", time());
$linkdate = date("dmY", time());


$cityarray = array("Bhubaneswar", "Cuttack", "Rourkela", "Berhampur");
$citycode = array("71", "72", "79", "77");
$imagelinkcitycode = array("hr", "km", "ro", "be");

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting SAM\n";
for ($edition = 0; $edition < count($cityarray); $edition++) {
    $link = "https://sambadepaper.com/epaper/1/" . $citycode[$edition] . "/" . $filenamedate . "/1";
    $content = file_get_contents($link);
    $pagearray = explode("id='imgpage_", $content);

    for ($page = 1; $page < count($pagearray); $page++) {
        $pageno = explode("'", $pagearray[$page])[0];
        $idarray = explode("show_pop('", $pagearray[$page]);
        for ($id = 1; $id < count($idarray); $id++) {
            $imagelinkid = explode(",", $idarray[$id])[1];
            $imagelink = "https://sambadepaper.com/epaperimages/" . $linkdate . "/" . $linkdate . "-md-" . $imagelinkcitycode[$edition] . "-" . $pageno . "/" . str_replace("'", "", $imagelinkid) . ".jpg";


            $filepath = "/nvme/SAM_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $id . "_admin_ori.jpg";
            $temp_txtfile = str_replace(".jpg", "", $filepath);
            $txtfile = "./imagestext/SAM_" . $cityarray[$edition] . "_" . $filenamedate . "_" . $page . "00" . $id . "_admin_ori.txt";


            $image = file_get_contents($imagelink);


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
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $id . " Completed\n";
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All Editions Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
?>