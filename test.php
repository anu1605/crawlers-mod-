<?php
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_URL, "https://epaper.thehindu.com/ccidist-replica-reader/?epub=https://epaper.thehindu.com/ccidist-ws/th/th_delhi/issues/42353/#/pages/1");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
// $data = curl_exec($ch);
// curl_close($ch);


function getdata($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Disable redirection
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.5', 'Accept-Encoding: gzip, deflate', 'Connection: keep-alive', 'Upgrade-Insecure-Requests: 1', 'TE: Trailers'));
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$url = 'https://epaper.thehindu.com/ccidist-ws/th/th_bangalore/issues/42323/OPS/G7HBEMLLT.1%2BG7HBEMLLT.1_background-2048.png';
$data = getdata($url);
echo $data;
