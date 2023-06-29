<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, "https://epaper.prabhatkhabar.com/3726073/RANCHI-City/CITY#page/1/3");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
$data = curl_exec($ch);
curl_close($ch);

// $data = str_replace(" ", "", $data);
$contentarray = explode('<a href="https://epaper.prabhatkhabar.com/r/', $data);
echo count($contentarray);

$codeArray = array();
file_put_contents(dirname(__FILE__) . "/test.txt",   $data);
// for ($code = 1; $code < count($contentarray); $code++) {
//     $getcode = explode('"', $contentarray[$code])[0];
//     $getcity = explode('</h3>', explode("<h3>", $contentarray[$code])[1])[0];
//     $codeArray[$getcity] = $getcode;
// }

// // file_put_contents(dirname(__FILE__) . "/test.txt",   $codeArray);
// print_r($codeArray);
