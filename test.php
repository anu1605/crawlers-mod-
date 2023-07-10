<?php
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_URL, "https://epaper.navarashtra.com/article-07-Jul-2023-pune-edition/9-12/");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
// $data = curl_exec($ch);
// curl_close($ch);



// file_put_contents(dirname(__FILE__) . "/test.txt",   $data);

// id='ImageArticle'  src="
// id='ImageArticle'  src="

$array = explode(',',  file_get_contents("dependencies/hb.txt"));

print_r($array);
echo $eol;
die();
