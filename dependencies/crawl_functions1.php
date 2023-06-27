

<?php

header('Content-Type: text/html; charset=utf-8');
// require  '/var/www/d78236gbe27823/vendor/autoload.php';

// use thiagoalessio\TesseractOCR\TesseractOCR;

if (php_sapi_name() == "cli") $eol = "\n";
else  $eol = "<br>";

function filenamedate($epapercode, $conn)
{
    // $finddateq = "Select * from Crawl_Record1 WHERE Papershortname='" . $epapercode . "' ORDER BY Paperdate DESC LIMIT 1";
    // $finddaters = mysqli_query($conn, $finddateq);
    // if (mysqli_num_rows($finddaters)) {
    //     $finddaterow = mysqli_fetch_array($finddaters);
    //     $filedate = date('Y-m-d', strtotime($finddaterow['Paperdate']) + (24 * 3600));
    // } else
    $filedate = date('Y-m-d', strtotime("2023-06-04"));

    return $filedate;
}

function dateForLinks($epapercode, $filenamedate)
{
    if ($epapercode == "DHM" or $epapercode == "OHO" or $epapercode == "DST" or $epapercode == "PN" or $epapercode == "AU" or $epapercode == "LM" or $epapercode == "SY" or $epapercode == "VV" or $epapercode == "YB") {
        echo "epapercode=" . $epapercode . PHP_EOL;
        return date('Ymd', strtotime($filenamedate));
    } else if ($epapercode == "NHT" or $epapercode == "HB") return date('Y/m/d', strtotime($filenamedate));
    else if ($epapercode == "ESM" or $epapercode == "TOI" or $epapercode == "ET" or $epapercode == "MT" or $epapercode == "Mirror") return  date('d/m/Y', strtotime($filenamedate));
    else if ($epapercode == "GSM") return date("d-m-Y", strtotime($filenamedate));
    else if ($epapercode == "DN" or $epapercode == "DJ" or $epapercode == "NB" or $epapercode == "ND" or $epapercode == "NVR" or $epapercode == "PAP") return date('d-M-Y', strtotime($filenamedate));
    else if ($epapercode == "JPS") return date('dmy', strtotime($filenamedate));
    else if ($epapercode == "AP" or $epapercode == "NYB" or $epapercode == "RS" or $epapercode == "SAM" or $epapercode == "SMJ") {
        return date('dmY', strtotime($filenamedate));
    } else if ($epapercode == "KM") {

        if (date("d", strtotime($filenamedate)) == date("d", time())) {
            $dateForLinks = date('Ymd', strtotime($filenamedate) - (24 * 3600));
        } else
            $dateForLinks = date("Ymd", strtotime($filenamedate));
        return $dateForLinks;
    } else if ($epapercode == "MC") {

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
    } else if ($epapercode == "MM") {

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
    } else if ($epapercode == "POD") {

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
    } else if ($epapercode == "SBP") {

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
    } else if ($epapercode == "SOM") {
        $data = file_get_contents("https://epaper.starofmysore.com/");
        $datecodearray = explode('<a href="/epaper/edition/', $data);
        $originaldatecode = explode('/star-mysore"', $datecodearray[1])[0];
        $datecode = explode('/star-mysore"', $datecodearray[1])[0];
        $reqiredDate = date("Y-m-d", strtotime($filenamedate));
        $content = file_get_contents("https://epaper.starofmysore.com/epaper/edition/" . $datecode . "/star-mysore");
        $date = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));

        if ($date < $reqiredDate) {
            $reqiredDate = $date;
            $filenamedate = $reqiredDate;
        }
        $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
        $content = file_get_contents("https://epaper.starofmysore.com/epaper/edition/" . $datecode . "/star-mysore");
        $date = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));

        if ($date > $reqiredDate)
            $difference = -1;
        else if ($date < $reqiredDate)
            $difference = 1;
        while ($date != $reqiredDate && $datecode >= 0 && $datecode <= $originaldatecode) {
            $datecode += $difference;
            $date =  date("Y-m-d", strtotime($date) + ($difference * 24 * 3600));
            $content = file_get_contents("https://epaper.starofmysore.com/epaper/edition/" . $datecode . "/star-mysore");
            if ($date  == $reqiredDate && !$content) {
                $reqiredDate =  date("Y-m-d", strtotime($reqiredDate) + (24 * 3600));
                $difference = 1;
            }
        }

        return $datecode;
    } else if ($epapercode == "ASP") {
        $data = file_get_contents("https://epaper.asomiyapratidin.in");
        $datecodearray = explode('href="/edition/', $data);
        $originaldatecode = explode('/', $datecodearray[1])[0];
        $datecode = explode('/', $datecodearray[1])[0];
        $reqiredDate = date("Y-m-d", strtotime($filenamedate));
        $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
        $content = file_get_contents("https://epaper.asomiyapratidin.in/edition/" . $datecode . "/%E0%A6%85%E0%A6%B8%E0%A6%AE%E0%A7%80%E0%A7%9F%E0%A6%BE-%E0%A6%AA%E0%A7%8D%E0%A6%B0%E0%A6%A4%E0%A6%BF%E0%A6%A6%E0%A6%BF%E0%A6%A8");
        $date = date("Y-m-d", strtotime(trim(explode('|', explode('Asomiya Pratidin ePaper :', $content)[1])[0])));
        if ($date > $reqiredDate)
            $difference = -1;
        else if ($date < $reqiredDate)
            $difference = 1;
        while ($date != $reqiredDate && $datecode >= 0 && $datecode <= $originaldatecode) {
            $datecode += $difference;
            $date =  date("Y-m-d", strtotime($date) + ($difference * 24 * 3600));
            $content = file_get_contents("https://epaper.asomiyapratidin.in/edition/" . $datecode . "/%E0%A6%85%E0%A6%B8%E0%A6%AE%E0%A7%80%E0%A7%9F%E0%A6%BE-%E0%A6%AA%E0%A7%8D%E0%A6%B0%E0%A6%A4%E0%A6%BF%E0%A6%A6%E0%A6%BF%E0%A6%A8");
            if ($date  == $reqiredDate && !$content) {
                $reqiredDate =  date("Y-m-d", strtotime($reqiredDate) + (24 * 3600));
                $difference = 1;
            }
        }
        return $datecode;
    } else if ($epapercode == "BS") {
        $data = file_get_contents("https://epaper.bombaysamachar.com/");
        $datecodearray = explode('href="/view/', $data);
        $originaldatecode = explode('/', $datecodearray[1])[0];
        $datecode = explode('/', $datecodearray[1])[0];
        $reqiredDate = date("Y-m-d", strtotime($filenamedate));
        $datecode -= intval((time() - strtotime($filenamedate)) / (24 * 3600));
        $date = date("d-m-Y", strtotime($reqiredDate));


        $datecodePlus = $datecode;
        $datecodeMinus = $datecode;

        while (!getdata("https://epaper.bombaysamachar.com/view/" . $datecode . "/" . $date)) {
            $datecodePlus += 1;
            $datecodeMinus -= 1;

            if (getdata("https://epaper.bombaysamachar.com/view/" . $datecodePlus . "/" . $date)) {
                $datecode = $datecodePlus;
                return $datecode;
            }
            if (getdata("https://epaper.bombaysamachar.com/view/" . $datecodeMinus . "/" . $date)) {
                $datecode = $datecodeMinus;
                return $datecode;
            }
        }

        return $datecode;
    } else if ($epapercode == "DNS") {

        if (($filenamedate -  date("Y-m-d", time())) == 0) {
            $dateForLinks = date('dmY', strtotime($filenamedate) - (24 * 3600));
        } else
            $dateForLinks = date("dmY", strtotime($filenamedate));

        return $dateForLinks;
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

        case "MC":
            return array("Mumbai");
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

        case "GSM":
            return array("ahmedabad", "baroda", "bhavnagar", "bhuj", "mumbai", "rajkot", "surat");
            break;

        case "PN":
            return  array("Mumbai", "Pune", "Nashik", "Nagpur", "Kolhapur", "Satara", "Solapur", "Jalgaon", "Dhule", "Nanded", "Thane", "Latur", "Ahmednagar");
            break;

        case "TOI":
            return  array("Ahmedabad", "Bangalore", "Bhopal", "Chandigarh", "Chennai", "Delhi", "Goa", "Hyderabad", "Jaipur", "Kochi", "Kolkata", "Lucknow", "Mumbai");
            break;

        case "ET":
            return  array("Bangalore", "Mumbai", "Delhi", "Kolkata");
            break;

        case "MT":
            return  array("Nagpur", "Mumbai", "Pune", "Sambhaji", "Nashik");
            break;

        case "Mirror":
            return  array("Bangalore", "Mumbai", "Pune");
            break;
        case "DST":
            return array("Delhi", "Chandigarh", "Haryana");
            break;
        case "ESM":
            return array("Kolkata");
            break;
        default:
            return null;
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

        case "PN":
            return array("PM", "PU", "NS", "NAG", "KOL", "STR", "SOL", "JAL", "DHU", "NDD", "PT", "LTR", "AH");
            break;

        case "TOI":
            return array("toiac", "toibgc", "toibhoc", "toicgct", "toich", "cap", "toigo", "toih", "toijc", "toikrko", "toikc", "toilc", "toim");

        case "ET":
            return array("etbg", "etmc", "etdc", "etkc");
            break;

        case "MT":
            return array("mtnag", "mtm", "mtpe", "mtag", "mtnk");
            break;

        case "Mirror":
            return array("vkbgmr", "vkmmir", "pcmir");
            break;

        case "DST":
            return array("DEL", "CHAND", "HAR");
            break;
        case "ESM":
            return array("esamk");
            break;
    }
}

function makefilepath($epapercode, $city, $date, $number, $lang)
{
    // $filepath = "/nvme/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    $filepath = "./nvme/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    $temp_txtfile = str_replace(".jpg", "", $filepath);
    // $txtfile = "/var/www/d78236gbe27823/marketing/Whatsapp/images/ocrtexts/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".txt";
    $txtfile = "./ocrtexts/" . $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".txt";
    $newspaper_name = $epapercode;
    $newspaper_region = $city;
    $newspaper_date = $date;
    $newspaper_lang = $lang;
    $Image_file_name = $epapercode . "_" . $city . "_" . $date . "_" . $number . "_admin_" . $lang . ".jpg";
    $newspaper_operator_name = 'admin';

    return $filepath . "&" . $temp_txtfile . "&" . $txtfile . "&" . $newspaper_name . "&" . $newspaper_region . "&" . $newspaper_date . "&" . $newspaper_lang . "&" . $Image_file_name . "&" . $newspaper_operator_name;
}
function alreadyDone($filepath, $conn)
{
    // $a = explode("/", $filepath)[2];
    // $b = explode("_", $a);
    // $newspaper_name = $b[0];
    // $edition = $b[1];
    // $date = $b[2];
    // if ($b[3] >= 1001) {
    //     $page = explode("00", $b[3])[0];
    //     $section = explode("00", $b[3])[1];
    // } else {
    //     $page = $b[3];
    //     $section = '0';
    // }
    // $q = "select * from Crawled_Pages1 WHERE Papershortname = '" . $newspaper_name . "' AND Paperdate = '" . $date . "' AND Edition = '" . $edition . "' AND Page = '" . $page . "' AND Section = '" . $section . "'";
    // $rs = mysqli_query($conn, $q);
    // if (mysqli_num_rows($rs) > 0) return "Yes";
    // else return "No";
}
function writeImage($url, $path)
{
    // $arrContextOptions = array(
    //     "ssl" => array(
    //         "verify_peer" => false,
    //         "verify_peer_name" => false,
    //     ),
    // );
    $image = getdata($url);
    // $image = file_get_contents($url, false, stream_context_create($arrContextOptions));
    $handle = fopen($path, "w");
    fwrite($handle, $image);
    fclose($handle);
}

function runTesseract($epapername, $edition, $page, $section, $conn, $patharray, $lang)
{
    global $eol;
    $filepath = $patharray[0];
    $temp_txtfile = $patharray[1];
    $txtfile = $patharray[2];
    $newspaper_name = $patharray[3];
    $newspaper_region = $patharray[4];
    $newspaper_date = $patharray[5];
    $newspaper_lang = $patharray[6];
    $Image_file_name = $patharray[7];
    $newspaper_operator_name = $patharray[8];
    $starttime = date('Y-m-d H:i:s', time());

    try {

        if ($lang != 'eng') $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l " . $lang . "+eng > /dev/null 2>&1";
        else $command = "tesseract " . $filepath . " " . $temp_txtfile . " -l eng > /dev/null 2>&1";

        //$output = shell_exec('python3 /var/www/d78236gbe27823/marketing/Whatsapp/python.py' . ' ' . $filepath);
        //$string = explode(',', trim($output));
        //if ($string[0] != "classified")
        //    return;

        exec($command);
        $text = file_get_contents($temp_txtfile . ".txt");
        //$text = $string[1];


        //$text = (new TesseractOCR($filepath))->lang($lang,'eng')->run();

        $matches = array();
        preg_match_all('/\+91[0-9]{10}|[0]?[6-9][0-9]{4}[\s]?[-]?[0-9]{5}/', $text, $matches);
        $matches = str_replace("+91", "", str_replace("\n", "", str_replace("-", "", str_replace(" ", "", $matches[0]))));
        foreach ($matches as $match => $val) $matches[$match] = ltrim($val, "0");
        $n = count($matches);

        if ($n < 5) {
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found" .  $eol;
        } else {

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Completed. " . $n . " new numbers found. File Saved" . $eol;

            // $handle = fopen($txtfile,"w");
            // fwrite($handle,$text);
            // fclose($handle);

            rename($temp_txtfile . ".txt", $txtfile);
            //$file = fopen($temp_txtfile, "w+");
            //fwrite($file, $text);
            //fclose($file);

            //     echo $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Starting to add in the database...";

            //     $values = "";
            //     $values_non_unique = "";

            //     for ($i = 0; $i < $n; $i++) {

            //         $blockcheck = "select * from Blocked_Numbers where Mobile_No = '" . $matches[$i] . "'";
            //         $bcrs = mysqli_query($conn, $blockcheck);
            //         if (!mysqli_num_rows($bcrs)) {
            //             $values .= "('" . $matches[$i] . "','" . $newspaper_name . "','" . $newspaper_region . "','" . $newspaper_date . "','" . $newspaper_lang . "','" . $Image_file_name . "','" . $newspaper_operator_name . "'),";
            //             $values_non_unique .= "('" . $matches[$i] . "','" . $newspaper_name . "','" . $newspaper_region . "','" . $newspaper_date . "','" . $newspaper_lang . "','" . $Image_file_name . "','" . $newspaper_operator_name . "'),";
            //         } else echo "" . $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Skipping " . $matches[$i] . " found in blocked numbers";
            //     }

            //     if (strlen($values) > 0) {

            //         $values = substr($values, 0, strlen($values) - 1) . " ON DUPLICATE KEY UPDATE Newspaper_Name = VALUES(Newspaper_Name), Newspaper_Region = VALUES(Newspaper_Region), Newspaper_Date = VALUES(Newspaper_Date), Newspaper_Lang = VALUES(Newspaper_Lang);";

            //         $values_non_unique = substr($values_non_unique, 0, strlen($values_non_unique) - 1);

            //         echo $eol . "=================================" . $eol;
            //         echo $q_non_unqiue = "insert into Mobile_Lists_NON_Unique (Mobile_Number,Newspaper_Name,Newspaper_Region,Newspaper_Date,Newspaper_Lang,Image_File_Name,Image_Operator) values " . $values_non_unique;
            //         echo $eol;
            //         echo $q = "insert into Mobile_Lists1 (Mobile_Number,Newspaper_Name,Newspaper_Region,Newspaper_Date,Newspaper_Lang,Image_File_Name,Image_Operator) values " . $values;
            //         echo $eol . "=================================" . $eol;

            //         if (!mysqli_query($conn, $q)) {
            //             echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Error in insert query... ABORTING!" . $eol . $q . "" . $eol;
            //             die();
            //         }

            //         if (!mysqli_query($conn, $q_non_unqiue)) {
            //             echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Error in insert query... ABORTING!" . $eol . $q_non_unqiue . "" . $eol . $eol . mysqli_error($conn);
            //             die();
            //         }
            //         echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "Insert query executed successfully......" . $eol;
            //     } else echo  $eol . date('Y-m-d H:i:s', (time() + (5.5 * 3600))) . "==> " . "No numbers left to insert";
        }

        // $iq = "INSERT INTO Crawled_Pages1 (Papername,Papershortname,Paperdate,Edition,Page,Section,No_Of_Mobiles_Found,Start_Time) VALUES ('" . $epapername . "','" . $newspaper_name . "','" . $newspaper_date . "','" . $newspaper_region . "','" . $page . "','" . $section . "','" . count($matches) . "','" . $starttime . "')";

        // echo $eol . $iq . "" . $eol;

        // mysqli_query($conn, $iq);
    } catch (Exception $e) {
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Tesseract Falied to run" . $eol;
    }

    // $emergencyStopQ = "SELECT Emergency_STOP FROM Emergency WHERE Instruction_For = 'crawl.php'";
    // $emergencyStopRS = mysqli_query($conn, $emergencyStopQ);
    // $emergencyStopRow = mysqli_fetch_array($emergencyStopRS);

    // if ($emergencyStopRow['Emergency_STOP'] == "STOP") {

    //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $newspaper_region . " Page " . $page . " Section " . $section . " Completed" . $eol;
    //     mysqli_query($conn, "UPDATE Emergency SET Emergency_STOP = 'Keep Going' WHERE Instruction_For = 'crawl.php'");
    //     die($eol . $eol . date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . "EMERGENCY STOP CALLED" . $eol . $eol);
    // }
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

function crawltoi($cityarray, $dateForLinks, $epapercode, $citycode, $filenamedate, $eol, $conn, $lang, $cities_of_interest, $epapername)
{


    for ($edition = 0; $edition < count($cityarray); $edition++) {
        // if (!in_array(ucfirst(explode("-", $cityarray[$edition])[0]), $cities_of_interest)) {

        //     echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $cityarray[$edition] . " Edition. Doesn't fall in cities of interest" . $eol;
        //     continue;
        // }
        $failedPageCount = 0;
        $date_formatted = date("Y/d/m", strtotime($dateForLinks));

        if ($epapercode == "ESM") {
            $dateTime = DateTime::createFromFormat('d/m/Y', $dateForLinks);
            $date_formatted = $dateTime->format('Y/m/d');
        }

        if ($epapercode == "Mirror" and $cityarray[$edition] == "Mumbai") {

            $date_array = explode("/", $dateForLinks);
            $processdate = $date_formatted = $date_array[2] . "-" . $date_array[1] . "-" . $date_array[0];
            $dayofweek = date('l', strtotime($processdate));
            if ($dayofweek <> 'Sunday') {
                echo "There is no Mirror published from Mumbai on " . $dayofweek . ", " . $dateForLinks . ". It publishes only on Sundays";
                continue;
            }
        }

        for ($page = 1; $page <= 40; $page++) {
            $imageFound = "No";

            if ($failedPageCount > 3) {
                echo "Seems Pages over. Skipping... " . $page . "\n";
                continue;
            }
            for ($section = 1; $section <= 50; $section++) {
                $imagelink = "https://asset.harnscloud.com/PublicationData/" . str_replace("ESM", "EiSamay", $epapercode) . "/" . $citycode[$edition] . "/" . $date_formatted . "/Advertisement/" . str_pad($page, 3, "0", STR_PAD_LEFT) . "/" . str_replace("/", "_", $dateForLinks) . "_" . str_pad($page, 3, "0", STR_PAD_LEFT) . "_" . str_pad($section, 3, "0", STR_PAD_LEFT) . "_" . $citycode[$edition] . ".jpg";

                // if (strlen(file_get_contents($url) <= 0)) continue;

                $section_size_array = getimagesize($imagelink);
                $width = $section_size_array[0];


                if ($width > 0) {
                    $imageFound = "Yes";
                    $failedPageCount = 0;

                    if ($width > 200 and $width < 250) {
                        $getpath = explode("&", makefilepath($epapercode,  $cityarray[$edition], $filenamedate, $page . "00" . $section, $lang));
                        if (alreadyDone($getpath[0], $conn) == "Yes") continue;
                        writeImage($imagelink, $getpath[0]);

                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
                        runTesseract($epapername, $cityarray[$edition], $page, $section, $conn, $getpath, $lang);
                        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Section " . $section . " Completed" . $eol;
                        ob_flush();
                        flush();
                    } else continue;
                } else {
                    continue;
                }
            }

            if ($imageFound == "No") $failedPageCount++;

            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Page " . $page . " Completed" . $eol;
        }
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
    }
}

function cityofinterest($city, $cities_of_interest, $eol)
{
    if (!in_array(ucfirst(explode("-", $city)[0]), $cities_of_interest)) {

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Skipping " . $city . " Edition. Doesn't fall in cities of interest" . $eol;
        return false;
    }
    return true;
}

function getdata($link)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows;U;Windows NT 5.1;en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function writeImageWithCurl($url, $path)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows;U;Windows NT 5.1;en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
    $data = curl_exec($ch);
    if (strpos($data, "404 - File or directory not found."))
        return true;

    $handle = fopen($path, "w");
    fwrite($handle, $data);
    fclose($handle);
    file_put_contents(dirname(__FILE__, 2) . "/test.txt", $data);
    curl_close($ch);
}
