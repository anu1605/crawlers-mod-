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
    // $filedate = date('Y-m-d', time() - (16 * 24 * 3600));
    $filedate = date("Y-m-d", strtotime("10-05-2023"));

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
            $datecode = explode('/mc"', $datecodearray[1])[0];

            $reqiredDay = date("d", strtotime($filenamedate));
            $datecode -= (date("d", time()) - $reqiredDay);

            $content = file_get_contents("https://www.mumbaichoufer.com/view/" . $datecode . "/mc");
            $day = date("d", strtotime(trim(explode("- Page 1", explode("Mumbaichoufer -", $content)[1])[0])));

            $difference = 0;
            if ($day >= $reqiredDay)
                $difference = $day - $reqiredDay;
            return ($datecode - $difference);
            break;

        case "MM":
            $data = file_get_contents("https://epaper.mysurumithra.com/");
            $datecodearray = explode('class="epost-title"><a href="/epaper/edition/', $data);
            $datecode = explode('/mysuru-mithra"', $datecodearray[1])[0];

            $reqiredDay = date("d", strtotime($filenamedate));
            $datecode -= (date("d", time()) - $reqiredDay);

            $content = file_get_contents("https://epaper.mysurumithra.com/epaper/edition/" . $datecode . "/mysuru-mithra");
            $day = date("d", strtotime(explode('"', explode('value="', $content)[1])[0]));
            // if (date("d", time()) - date("d", strtotime($filenamedate)) > 15) {
            //     $index = count($datecodearray) - 1;
            //     $filenamedate = date('Y-m-d', time() - (15 * 24 * 3600));
            // } else
            //     $index = date("d", time()) - date("d", strtotime($filenamedate)) + 1;


            echo "day = " . $day  . " reqday = " . $reqiredDay;
            $difference = 0;
            if ($day >= $reqiredDay)
                $difference = $day - $reqiredDay;
            return ($datecode - $difference);
            break;

        case "NB":
            return date('d-M-Y', strtotime($filenamedate));
            break;

        case "NBT":
            return;
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
            $datecode = explode('/pratidin-odia-daily', explode('class="first disabled"><a href="/pratidin/epaper/edition/', $data)[1])[0];

            return $datecode - (date("d", time()) - date("d", strtotime($filenamedate)));
            break;

        case "PUD":
            return;
            break;

        case "RS":
            return;
            break;

        case "SAM":
            return;
            break;

        case "SBP":
            return;
            break;

        case "SMJ":
            return;
            break;

        case "SY":
            return;
            break;

        case "VV":
            return;
            break;

        case "YB":
            return;
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

        case "MC":
            return;
            break;

        case "MM":
            return;
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

        case "NYB":
            return;
            break;

        case "PAP":
            return;
            break;

        case "POD":
            return;
            break;

        case "PUD":
            return;
            break;

        case "RS":
            return;
            break;

        case "SAM":
            return;
            break;

        case "SBP":
            return;
            break;

        case "SMJ":
            return;
            break;

        case "SY":
            return;
            break;

        case "VV":
            return;
            break;

        case "YB":
            return;
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

        case "JPS":
            return;
            break;

        case "KM":
            return;
            break;

        case "LM":
            return array("MULK", "ANLK", "AKLK", "AULK", "GALK", "JLLK", "KOLK", "NPLK", "NSLK", "PULK", "SOLK");
            break;

        case "MC":
            return;
            break;

        case "MM":
            return;
            break;

        case "NB":
            return array("mum", "nag", "pun", "akol", "nas", "amr", "cha");
            break;

        case "NBT":
            return array("delhi/d/13", "mumbai/d/16", "lucknow-kanpur/d/9", "noida/d/19", "ghaziabad/d/20", "faridabad/d/24", "gurugram/d/25");
            break;

        case "ND":
            return array("74", "33", "52", "59", "50", "71");
            break;

        case "NVR":
            return;
            break;

        case "NYB":
            return;
            break;

        case "PAP":
            return;
            break;

        case "POD":
            return;
            break;

        case "PUD":
            return;
            break;

        case "RS":
            return;
            break;

        case "SAM":
            return;
            break;

        case "SBP":
            return;
            break;

        case "SMJ":
            return;
            break;

        case "SY":
            return;
            break;

        case "VV":
            return;
            break;

        case "YB":
            return;
            break;
    }
}
