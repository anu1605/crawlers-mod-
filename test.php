<?php
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_URL, "https://epaper.thehindu.com/ccidist-replica-reader/?epub=https://epaper.thehindu.com/ccidist-ws/th/th_delhi/issues/42353/#/pages/1");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
// $data = curl_exec($ch);
// curl_close($ch);


$im = imagecreatefromstring(file_get_contents('https://epaper.thehindu.com/ccidist-ws/th/th_delhi/issues/42353/OPS/L590482192.png?&cropFromPage=true'));
imagepng($im, dirname(__FILE__) . "/test.png");
// file_put_contents(dirname(__FILE__) . "/test.png",   'https://epaper.thehindu.com/ccidist-ws/th/th_delhi/issues/42353/OPS/L590482192.png?&cropFromPage=true');
