

<?php



error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(3600);


$filenamedate = date('Y-m-d', time());
$dateForLinks = date('d-M-Y', time());


$paperArray = array("Delhi", "Kanpur", "Patna", "Lucknow", "Varanasi", "Prayagraj", "Gorakhpur", "Agra", "Meerut", "Bhagalpur", "Muzaffarpur");

$paperString = array("-4-Delhi-City-edition-Delhi-City", "-64-Kanpur-edition-Kanpur", "-84-Patna-Nagar-edition-Patna-Nagar", "-11-Lucknow-edition-Lucknow", "-45-Varanasi-City-edition-Varanasi-City", "-79-Prayagraj-City-edition-Prayagraj-City", "-56-Gorakhpur-City-edition-Gorakhpur-City", "-193-Agra-edition-Agra", "-29-Meerut-edition-Meerut", "-205-Bhagalpur-City-edition-Bhagalpur-City", "-203-Muzaffarpur-Nagar-edition-Muzaffarpur-Nagar");



$starttime = time();

echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Starting Jagran\n";

for ($edition = 0; $edition < count($paperArray); $edition++) {

    $response = file_get_contents("https://epaper.jagran.com/epaper/" . $dateForLinks . $paperString[$edition] . ".html");

    $a = explode('<ul id="menu-toc" class="menu-toc">', $response);
    $b = explode('</ul>', $a[1]);
    $pagesHTML = $b[0];

    $a = explode('ss.png">', $pagesHTML);

    for ($i = 0; $i < count($a) - 1; $i++) {
        $b = explode('data-src="', $a[$i]);
        $url = $b[1];
        $url_parts = explode('/', $url);
        $last_part = end($url_parts);
        $modified_last_part = 'M-' . $last_part . '.png';
        $url_parts[count($url_parts) - 1] = $modified_last_part;
        $pgImageURL = implode('/', $url_parts);



        $filepath = "/nvme/DJ_" . $paperArray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.jpg";
        $temp_txtfile = str_replace(".jpg", "", $filepath);
        $txtfile = "./imagestext/DJ_" . $paperArray[$edition] . "_" . $filenamedate . "_" . $i . "_admin_hin.txt";



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
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $paperArray[$edition] . " Page " . $i . " Completed\n";
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $paperArray[$edition] . " Completed\n";
}

exec("rm -f /nvme/*");

echo "\n";
echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>All Editions Completed\n";
$endtime = time();
echo "Total Time Taken: " . ($endtime - $starttime) . " seconds\n";
?>