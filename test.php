<?php
$url = 'http://webmilap.com/articlepage.php?articleid=HINDIMIL_HIN_20230625_12_1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    echo "Failed to retrieve the content.";
} else {
    // Process the response
    // ...
}

file_put_contents(dirname(__FILE__) . "/test.txt", $response);
