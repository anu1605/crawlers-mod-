<?php

$cityarray = array("Pune", "Aurangabad", "Mumbai", "Nashik", "Kolhapur", "Ratnagiri", "Nagpur");
$dateForLinks1 = date("Y/m/d", strtotime($filenamedate));
$dateForLinks2 = date("Y_m_d", strtotime($filenamedate));


if ($cityarray != null) {

    if ($no_of_editions_to_run > 0 and $no_of_editions_to_run < count($cityarray)) $cityarray = array_slice($cityarray, 0, $no_of_editions_to_run);
}


for ($edition = 0; $edition < count($cityarray); $edition++) {


    // if ($_REQUEST['city']) {
    //     if (strtolower($cityarray[$edition]) != strtolower($_REQUEST['city'])) continue;
    // }

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $imagelink = "https://epaper-sakal-application.s3.ap-south-1.amazonaws.com/EpaperData/Sakal/" . $cityarray[$edition] . "/" . $dateForLinks1 . "/Main/Sakal_" . $cityarray[$edition] . "_" . $dateForLinks2 . "_Main_DA_" . sprintf("%03d", $page) . "_PR.jpg";

        $getpath = explode("&", makefilepath($epapercode, ucwords($cityarray[$edition]), $filenamedate, $page, $lang));

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

        writeImage($imagelink, $getpath[0]);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
        runTesseract($epapername, $cityarray[$edition], $page, 0, $conn, $getpath, $lang);
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . " Page " . $page . " Completed" . $eol;
        ob_flush();
        flush();
    }
    echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>" . $cityarray[$edition] . " Completed" . $eol;
}
