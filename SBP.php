<?php

if ($epapercode == "SBP") {
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
    $datecode -= ((time() - strtotime($filenamedate)) / (24 * 3600));
    $datecode = floor($datecode);
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

    $data = file_get_contents("https://epaper.sangbadpratidin.in/epaper/edition/" . $datecode . "/sangbad-pratidin", false, stream_context_create($arrContextOptions));
    $contentArray = explode('<div class="item">', $data);
    $filenamedate = date("Y-m-d", strtotime(trim(explode('<', explode('p">', $data)[1])[0])));

    if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($contentArray)) $contentArray = array_slice($contentArray, 1, $no_of_pages_to_run_on_each_edition + 1);

    for ($page = 1; $page < count($contentArray); $page++) {
        $imagelink = str_replace('&', '&amp;', explode('"', explode('src="', $contentArray[$page])[1])[0]);

        if (trim($imagelink) == '')
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
