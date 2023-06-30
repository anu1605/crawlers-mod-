<?php
if ($epapercode == "SOM") {
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

    $content = file_get_contents("https://epaper.starofmysore.com/epaper/edition/" . $datecode . "/star-mysore/page/1", false, stream_context_create($arrContextOptions));
    $filenamedate = date("Y-m-d", strtotime(explode('"', explode('value="', $content)[1])[0]));
    $linkArray =   explode('"><img src="', $content);

    if ($no_of_pages_to_run_on_each_edition > 0 and $no_of_pages_to_run_on_each_edition < count($linkArray)) $linkArray = array_slice($linkArray, 0, $no_of_pages_to_run_on_each_edition + 1);

    for ($page = 1; $page < count($linkArray); $page++) {

        $imagelink = explode('"', explode('"><img src="', $content)[$page])[0];

        if (trim($imagelink) == '')
            break;

        $getpath = explode("&", makefilepath($epapercode, "Mysore", $filenamedate, $page, $lang));

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

        writeImage($imagelink, $getpath[0]);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
        runTesseract($epapername, "Mysore", $page, 0, $conn, $getpath, $lang);
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>Page " . $page . " Completed" . $eol;
        ob_flush();
        flush();
    }
}
