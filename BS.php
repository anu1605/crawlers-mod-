<?php
if ($epapercode == "BS") {

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
            break;
        }
        if (getdata("https://epaper.bombaysamachar.com/view/" . $datecodeMinus . "/" . $date)) {
            $datecode = $datecodeMinus;
            break;
        }
    }

    $content = file_get_contents("https://epaper.bombaysamachar.com/view/" . $datecode . "/" . date("d-m-Y", strtotime($filenamedate)) . "/1", false, stream_context_create($arrContextOptions));

    $linkcode = end(explode('/c600x315/', explode('-page-01', $content)[0]));



    for ($page = 1; $page <= $no_of_pages_to_run_on_each_edition; $page++) {
        $imagelink = "https://epaper.bombaysamachar.com/media/" . $linkcode . "-page-" . sprintf("%02d", $page) . ".jpg";

        if (!empty($imagelink)) $imageInfo = @getimagesize($imagelink);

        if (!$imageInfo)
            break;

        $getpath = explode("&", makefilepath($epapercode, "Mumbai", $filenamedate, $page, $lang));

        if (alreadyDone($getpath[0], $conn) == "Yes") continue;

        writeImage($imagelink, $getpath[0]);

        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>File " . $getpath[0] . " Saved" . $eol;
        runTesseract($epapername, "Mumbai", $page, 0, $conn, $getpath, $lang);
        echo date('Y-m-d H:i:s', time() + (5.5 * 3600)) . "=>"  . " Page " . $page . " Completed" . $eol;
        ob_flush();
        flush();
    }
}
