<?php
// include "../../includes/connect.php";
function filenamedate($epapercode)
{
    // $finddateq = "Select * from Crawl_Record WHERE Papershortname='" . $epapercode . "' ORDER BY Paperdate DESC LIMIT 1";
    // $finddaters = mysqli_query($conn, $finddateq);
    // if (mysqli_num_rows($finddaters)) {
    //     $finddaterow = mysqli_fetch_array($finddaters);
    //     $filedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
    // } else
    $filedate = date('Y-m-d', time());

    return $filedate;
}

function dateForLinks($epapercode, $filenamedate)
{
    switch ($epapercode) {
        case "AU":
            return date('Ymd', strtotime($filenamedate));
            break;

        case "HB":
            return date('Y/m/d', strtotime($filenamedate));
            break;

        case "DJ":
            return date('d-M-Y', strtotime($filenamedate));
            break;

        case "JPS":
            return date('dmy', strtotime($filenamedate));
            break;

        case "KM":
            if (date("d", strtotime($filenamedate)) == date("d", time())) {
                $dateForLinks = date('Ymd', strtotime($filenamedate) - (24 * 3600));
            } else
                $dateForLinks = date("Ymd", strtotime($filenamedate));
            return $dateForLinks;
            break;

        case "LM":
            return date('Ymd', strtotime($filenamedate));
            break;

        case "MC":
            $data = file_get_contents("https://www.mumbaichoufer.com/");
            $datecodearray =  explode('data-cat_ids="" href="/view/', $data);
            $originaldatecode = explode('/mc"', $datecodearray[1])[0];
            $datecode = explode('/mc"', $datecodearray[1])[0];
            $reqiredDate = date("Y-m-d", strtotime($filenamedate));
            $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
            $content = file_get_contents("https://www.mumbaichoufer.com/view/" . $datecode . "/mc");
            $date = date("Y-m-d", strtotime(trim(explode("- Page 1", explode("Mumbaichoufer -", $content)[1])[0])));
            if ($date > $reqiredDate)
                $difference = -1;
            else if ($date < $reqiredDate)
                $difference = 1;
            while ($date != $reqiredDate && $datecode >= 0 && $datecode <= $originaldatecode) {
                $datecode += $difference;
                $date = date("Y-m-d", strtotime($date) + ($difference * 24 * 3600));
                $content = file_get_contents("https://www.mumbaichoufer.com/view/" . $datecode . "/mc");
                if ($date  == $reqiredDate && !$content) {
                    $reqiredDate =  date("Y-m-d", strtotime($reqiredDate) + (24 * 3600));
                    $difference = 1;
                }
            }
            return $datecode;
            break;

        case "MM":
            $data = file_get_contents("https://epaper.mysurumithra.com/");
            $datecodearray = explode('class="epost-title"><a href="/epaper/edition/', $data);
            $originaldatecode = explode('/mysuru-mithra"', $datecodearray[1])[0];
            $datecode = explode('/mysuru-mithra"', $datecodearray[1])[0];
            $reqiredDate = date("Y-m-d", strtotime($filenamedate));
            $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
            $content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra");
            $date = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));
            if ($date > $reqiredDate)
                $difference = -1;
            else if ($date < $reqiredDate)
                $difference = 1;
            while ($date != $reqiredDate && $datecode >= 0 && $datecode <= $originaldatecode) {
                $datecode += $difference;
                $date =  date("Y-m-d", strtotime($date) + ($difference * 24 * 3600));
                $content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra");
                if ($date  == $reqiredDate && !$content) {
                    $reqiredDate =  date("Y-m-d", strtotime($reqiredDate) + (24 * 3600));
                    $difference = 1;
                }
            }
            return $datecode;
            break;

        case "NB":
            return date('d-M-Y', strtotime($filenamedate));
            break;

        case "ND":
            return date('d-M-Y', strtotime($filenamedate));
            break;

        case "NVR":
            return date('d-M-Y', strtotime($filenamedate));
            break;

        case "NYB":
            return date('dmY', strtotime($filenamedate));
            break;

        case "PAP":
            return date('d-M-Y', strtotime($filenamedate));
            break;

        case "POD":
            $data = file_get_contents("https://e2india.com/pratidin/");
            $originaldatecode = explode('/', explode('href="/pratidin/epaper/edition/', $data)[1])[0];
            $datecode = $originaldatecode;
            $reqiredDate = date("Y-m-d", strtotime($filenamedate));
            $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
            $content = file_get_contents("https://e2india.com/pratidin/epaper/edition/" . $datecode . "/pratidin-odia-daily");
            $date = date("Y-m-d", strtotime(explode('"', explode('currentText: "', $content)[1])[0]));
            if ($date > $reqiredDate)
                $difference = -1;
            else if ($date < $reqiredDate)
                $difference = 1;
            while ($date != $reqiredDate && $datecode >= 0 && $datecode <= $originaldatecode) {
                $datecode += $difference;
                $date = date("Y-m-d", strtotime($date) + ($difference * 24 * 3600));
                $content = file_get_contents("https://e2india.com/pratidin/epaper/edition/" . $datecode . "/pratidin-odia-daily");
                if ($date  == $reqiredDate && !$content) {
                    $reqiredDate =  date("Y-m-d", strtotime($reqiredDate) + (24 * 3600));
                    $difference = 1;
                }
            }
            return $datecode;
            break;

        case "RS":
            return date('dmY', strtotime($filenamedate));
            break;

        case "SAM":
            return date("dmY", strtotime($filenamedate));
            break;

        case "SBP":
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, "https://epaper.sangbadpratidin.in/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
            $data = curl_exec($ch);
            curl_close($ch);
            $originaldatecode = explode('/', explode('href="/epaper/edition/', $data)[1])[0];
            $datecode = $originaldatecode;
            $reqiredDate = date("Y-m-d", strtotime($filenamedate));
            $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
            $content = file_get_contents("https://epaper.sangbadpratidin.in/epaper/edition/" . $datecode . "/sangbad-pratidin");
            $date = date("Y-m-d", strtotime(trim(explode('<', explode('p">', $content)[1])[0])));
            if ($date > $reqiredDate)
                $difference = -1;
            else if ($date < $reqiredDate)
                $difference = 1;
            while ($date != $reqiredDate && $datecode >= 0 && $datecode <= $originaldatecode) {
                $datecode += $difference;
                $date =  date("Y-m-d", strtotime($date) + ($difference * 24 * 3600));
                $content = file_get_contents("https://epaper.sangbadpratidin.in/epaper/edition/" . $datecode . "/sangbad-pratidin");
                if ($date  == $reqiredDate && !$content) {
                    $reqiredDate =  date("Y-m-d", strtotime($reqiredDate) + (24 * 3600));
                    $difference = 1;
                }
            }
            return $datecode;
            break;

        case "SMJ":
            return date('dmY', strtotime($filenamedate));
            break;

        case "SY":
            return date('Ymd', strtotime($filenamedate));
            break;

        case "VV":
            return date('Ymd', strtotime($filenamedate));
            break;

        case "YB":
            return date('Ymd', strtotime($filenamedate));
            break;
    }
}
function cityArray($epapercode)
{
    switch ($epapercode) {
        case "AU":
            return array("agra-city", "ambala", "bhopal", "bilaspur", "chandigarh-city", "dehradun-city", "delhi-city", "faridabad", "ghaziabad", "gurgaon", "haridwar", "jalandhar", "jammu-city", "jhansi-city", "kanpur-city", "kurukshetra", "lucknow-city", "meerut-city", "mohali", "moradabad-city", "noida", "allahabad-city", "shimla", "varanasi-city");
            break;

        case "DC":
            return array("Hyderabad", "Vijayawada", "Karimnagar", "Nellore", "Ananthapur");
            break;

        case "HB":
            return ["raipur", "bilaspur", "bhopal"];
            break;

        case "DJ":
            return array("Delhi", "Kanpur", "Patna", "Lucknow", "Varanasi", "Prayagraj", "Gorakhpur", "Agra", "Meerut", "Bhagalpur", "Muzaffarpur");
            break;

        case "LM":
            return array("Mumbai", "Ahmednagar", "Akola", "Aurangabad", "Goa", "Jalgaon", "Kolhapur", "Nagpur", "Nashik", "Pune", "solapur");
            break;

        case "NB":
            return array("mumbai", "nagpur",  "pune", "akola", "nashik", "amravati", "chandrapur");
            break;

        case "NBT":
            return array("delhi", "mumbai", "lucknow", "noida", "ghaziabad", "faridabad", "gurugram");
            break;

        case "ND":
            return array("indore", "bhopal", "gwalior", "jabalpur", "raipur", "bilaspur");
            break;

        case "NVR":
            return array("mumbai", "nagpur", "nashik", "pune");
            break;

        case "RS":
            return array("Delhi", "Lucknow", "Patna", "Dehradun", "Kanpur", "Gorakhpur", "Varanasi");
            break;

        case "SAM":
            return array("Bhubaneswar", "Cuttack", "Rourkela", "Berhampur");
            break;

        case "SY":
            return array("Mangalore", "Davangere", "Kalaburgi", "Hubli", "Bangalore");
            break;

        case "VV":
            return array("Bengaluru", "Hubli");
            break;
    }
}
function cityCodeArray($epapercode)
{
    switch ($epapercode) {
        case "DC":
            return ["HYD", "VIJ", "KRM", "NEL", "ATP"];
            break;

        case "HB":
            return ["raipur-raipur-main", "bilaspur-main", "bhopal-main"];
            break;

        case "DJ":
            return array("-4-Delhi-City-edition-Delhi-City", "-64-Kanpur-edition-Kanpur", "-84-Patna-Nagar-edition-Patna-Nagar", "-11-Lucknow-edition-Lucknow", "-45-Varanasi-City-edition-Varanasi-City", "-79-Prayagraj-City-edition-Prayagraj-City", "-56-Gorakhpur-City-edition-Gorakhpur-City", "-193-Agra-edition-Agra", "-29-Meerut-edition-Meerut", "-205-Bhagalpur-City-edition-Bhagalpur-City", "-203-Muzaffarpur-Nagar-edition-Muzaffarpur-Nagar");
            break;

        case "LM":
            return array("MULK", "ANLK", "AKLK", "AULK", "GALK", "JLLK", "KOLK", "NPLK", "NSLK", "PULK", "SOLK");
            break;

        case "NB":
            return array("mum", "nag", "pun", "akol", "nas", "amr", "cha");
            break;

        case "NBT":
            return array("delhi/dateForLinks/13", "mumbai/dateForLinks/16", "lucknow-kanpur/dateForLinks/9", "noida/dateForLinks/19", "ghaziabad/dateForLinks/20", "faridabad/dateForLinks/24", "gurugram/dateForLinks/25");
            break;

        case "ND":
            return array("74", "33", "52", "59", "50", "71");
            break;

        case "RS":
            return array("http://sahara.4cplus.net/epaperimages//dateForLinks//dateForLinks-hr-md-1ll.png", "http://sahara.4cplus.net/epaperimages//dateForLinks//dateForLinks-lu-md-1ll.png", "http://sahara.4cplus.net/epaperimages//dateForLinks//dateForLinks-pt-md-1ll.png", "http://sahara.4cplus.net/epaperimages//dateForLinks//dateForLinks-dd-md-1ll.png", "http://sahara.4cplus.net/epaperimages//dateForLinks//dateForLinks-kn-md-1ll.png", "http://sahara.4cplus.net/epaperimages//dateForLinks//dateForLinks-gp-md-1ll.png", "http://sahara.4cplus.net/epaperimages//dateForLinks//29052023-vn-md-1ll.png");
            break;

        case "SAM1":
            return array("71", "72", "79", "77");
            break;

        case "SAM2":
            return array("hr", "km", "ro", "be");
            break;

        case "SY":
            return array("MANG", "DAVN", "KALB", "HUB",  "BANG",);
            break;

        case "VV":
            return array("BEN", "HUB");
            break;
    }
}

function makefilepath($epapercode, $city, $date, $number, $lang)
{
    $filepath = "./nvme/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    $temp_txtfile = str_replace(".jpg", "", $filepath);
    $txtfile = "./imagestext/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".txt";

    return $filepath . "&" . $temp_txtfile . "&" . $txtfile;
}
function writeImage($url, $path)
{
    echo $path . PHP_EOL;
    $image = file_get_contents($url);
    $handle = fopen($path, "w");
    fwrite($handle, $image);
    fclose($handle);
}

function runTesseract($patharray, $lang)
{
    $filepath = $patharray[0];
    $temp_txtfile = $patharray[1];
    $txtfile = $patharray[2];
    try {
        $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l " . $lang . " > /dev/null 2>&1";
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

function getHBeditionlink($city, $dateforlinks, $citylink, $code)
{
    $link = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $city . "-full-edition/" . $dateforlinks . "/" . $citylink . "/";
    if ($city == "raipur") {
        $link2 = "https://www.haribhoomi.com/full-page-pdf/epaper/pdf/" . $city . "-full-edition/" . $dateforlinks . "/" . $city . "-main/";
        if (file_get_contents($link2 . $code)) {
            $link = $link2;
        }
    }
    return $link;
}
