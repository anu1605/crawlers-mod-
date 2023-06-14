<?php
$data = file_get_contents("https://epaper.navajyoti.net/archive/date/14-Jun-2023/2?forcesingle=yes");
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_URL, "https://epaper.navajyoti.net/");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows;U;Windows NT 5.1;en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
// $data = curl_exec($ch);
// curl_close($ch);
file_put_contents(dirname(__FILE__) . "/test.txt",  $data);
