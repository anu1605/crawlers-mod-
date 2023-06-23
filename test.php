<?php
$url = 'http://s3.ap-south-1.amazonaws.com/erelegos3dec17/News/OHERALDO/GOA/2023/06/11/ArticleImages/1291CD0.jpg';

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
