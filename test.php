<?php
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_URL, "https://sandesh.com/epaper/ahmedabad");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
// $data = curl_exec($ch);
// curl_close($ch);



// file_put_contents(dirname(__FILE__) . "/test.txt",   $data);

$url = "https://epaper.patrika.com/";  // Replace with the URL of the website you want to download
$savePath = dirname(__FILE__) . "/test.txt";  // Replace with the desired save path and file name

// Initialize cURL
$curl = curl_init();

curl_setopt($curl, CURLOPT_TIMEOUT, 60);  // Set timeout to 60 seconds
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.9999.99 Safari/537.36');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);


// Set the URL to fetch
curl_setopt($curl, CURLOPT_URL, $url);

// Set the option to return the response instead of outputting it directly
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($curl);

// Check if the request was successful
if ($response === false) {
    echo 'Error: ' . curl_error($curl);
    exit;
}

// Save the HTML content to a file
// file_put_contents($savePath, $response);
echo $response;

// Close the cURL session
curl_close($curl);

echo 'HTML file downloaded and saved successfully.';
