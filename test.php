<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, "https://epaper.inextlive.com/epaper/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
$data = curl_exec($ch);
curl_close($ch);

$sectionarray = explode('<li><a href="https://epaper.inextlive.com/epaper/edition-today-', $data);
$codearray = array();

for ($i = 1; $i < count($sectionarray); $i++) {
    $cityandcode = explode("-", $sectionarray[$i]);
    $city = $cityandcode[0];
    $code = explode(".", $cityandcode[1])[0];
    $codearray[$city] = $code;
}


// file_put_contents(dirname(__FILE__) . "/test.txt",   $data);
