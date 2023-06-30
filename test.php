<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, "https://epaper-sakal-application.s3.ap-south-1.amazonaws.com/EpaperData/Sakal/Pune/2023/06/30/Main/Sakal_Pune_2023_06_30_Main_DA_001_PR.jpg");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
$data = curl_exec($ch);
curl_close($ch);



file_put_contents(dirname(__FILE__) . "/test.png",   $data);
