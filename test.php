<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, "https://www.divyabhaskar.co.in/epaper/detail-page/ahmedabad-botad/65/2023-06-27");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
$data = curl_exec($ch);
curl_close($ch);


file_put_contents(dirname(__FILE__) . "/test.txt", $data);
