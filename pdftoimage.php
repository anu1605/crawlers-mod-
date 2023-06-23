<?php
$pdfUrl = "https://docs.google.com/viewerng/viewer?url=https://www.gujarattoday.in/wp-content/uploads/2023/06/GUJARATTODAY-E-PAPER-20-JUNE-2023.pdf&hl=en_US";

// Determine if the PDF link is embedded in Google Docs or not
if (isGoogleDocsUrl($pdfUrl)) {
    $pdfUrl = extractPdfUrlFromGoogleDocs($pdfUrl);
}

// Convert PDF to images
$outputDir = dirname(__FILE__, 2) . "/pdftoimages";
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$resolution = 300; // Adjust the resolution as needed

$command = "convert convert -density $resolution $pdfUrl -background white -alpha remove -alpha off -quality 100 $outputDir/image-%d.png";
exec($command, $output, $returnCode);

if ($returnCode === 0) {
    echo "PDF converted to images successfully.";
} else {
    echo "Error converting PDF to images.";
    print_r($output);
}

function isGoogleDocsUrl($url)
{
    return strpos($url, 'https://docs.google.com/viewerng/viewer?url=') === 0;
}

function extractPdfUrlFromGoogleDocs($url)
{
    $urlParts = parse_url($url);
    parse_str($urlParts['query'], $query);

    return urldecode($query['url']);
}
