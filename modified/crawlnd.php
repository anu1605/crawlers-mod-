

<?php

error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);

include "../../includes/connect.php";
$finddateq = "Select * from Crawl_Record WHERE Papershortname='ND' ORDER BY Paperdate DESC LIMIT 1";
$finddaters = mysqli_query($conn, $finddateq);
if (mysqli_num_rows($finddaters)) {
    $finddaterow = mysqli_fetch_array($finddaters);
    $filenamedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
} else $filenamedate = date('Y-m-d', time());
$dateForLinks = date('d-M-Y', strtotime($filenamedate));

$paperArray = array("indore", "bhopal", "gwalior", "jabalpur", "raipur", "bilaspur");
$paperString = array("74", "33", "52", "59", "50", "71");

$starttime = time();
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting ND\n";

for ($edition = 0; $edition < count($paperArray); $edition++) {
    $code = $paperString[$edition];
    $city = $paperArray[$edition];
    $pageURL = "https://epaper.naidunia.com/epaper/" . $dateForLinks . "-" . $code . "-" . $city . "-edition-" . $city . ".html";
    $response = file_get_contents($pageURL);



    $a =  number_format(explode('"', explode('<input type="hidden" name="totalpage" id="totalpage" value="', $response)[1])[0]);
    $array = (explode('<img data-src="',  explode('<div class="slidebox" id="item-zoom1">', $response)[1]));

    for ($i = 1; $i <= $a; $i++) {

        $pgImageURL = trim(explode('" title=', $array[$i])[0]);

        $filepath = "/nvme/ND_" . $paperArray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.jpg";
        $temp_txtfile = str_replace(".jpg", "", $filepath);
        $txtfile = "./imagestext/ND_" . $paperArray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.txt";


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

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $i . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $paperArray[$edition] . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All pages Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
mysqli_query($conn, "INSERT INTO Crawl_Record (Papername,Papershortname,Paperdate) VALUES ('Nai Dunia','ND','" . $filenamedate . "')");

?>