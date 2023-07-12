<?php
require  '/var/www/d78236gbe27823/vendor/autoload.php';

if ($epapercode == "TT") {

    $dateforlinks = date("dmY", strtotime($filenamedate));

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {

        $imagelink = 'https://epaper.telegraphindia.com/epaperimages////' . $dateforlinks . '////' . $dateforlinks . '-md-hr-' . $page . 'll.png';

        if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

        if (!$imageInfo)
            break;

        $getpath = explode("&", makefilepath($epapercode, "Kolkata", $filenamedate, $page, $lang));

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

        writeImage($imagelink, $getpath[0]);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
        runTesseract($epapername, "Kolkata", $page, 0, $conn, $getpath, $lang);
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
        ob_flush();
        flush();
    }
}
