<?php
if ($epapercode == "NHT") {
    $dateForLinks = date('Y/m/d', strtotime($filenamedate));

    $code = end(explode('/', explode('-md-ga', file_get_contents("https://epaper.navhindtimes.in/mainpage.aspx?pdate=" . $filenamedate, false, stream_context_create($arrContextOptions)))[0]));

    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $pdfUrl = "https://epaper.navhindtimes.in/PageImages/pdf/" . $dateForLinks . "/" . $code . "-md-ga-" . sprintf("%02d", $page) . ".pdf";
        if (!file_get_contents($pdfUrl, false, stream_context_create($arrContextOptions)))
            continue;
        $resolution = 300;
        $getpath = explode("&", makefilepath($epapercode, "Goa", $filenamedate, $page, $lang));

        $filepath =  $getpath[0];

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;
        $command = "convert -density $resolution $pdfUrl -background white -alpha remove -alpha off -quality 100 $filepath";
        exec($command, $output, $returnCode);
        if ($returnCode === 0) {
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
            runTesseract($epapername, "Goa", $page, 0, $conn, $getpath, $lang);
            echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
            ob_flush();
            flush();
        } else {
            echo "Error converting PDF to images.";
            print_r($output);
        }
    };
}
