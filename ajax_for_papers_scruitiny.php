<?php
include_once "../includes/connect.php";
$filename = $_POST['filename'];
$action = $_POST['action'];

if ($action == 'delete') {

    $query1 = "DELETE FROM Mobile_Lists WHERE Image_File_Name = '$filename'";
    $query2 = "DELETE FROM Mobile_Lists_NON_Unique WHERE Image_File_Name = '$filename'";
    mysqli_query($conn, $query1);
    mysqli_query($conn, $query2);
} elseif ($action == 'approve') {

    $query1 = "UPDATE Mobile_Lists SET Sending_Approved='Yes' WHERE Image_File_Name = '" . $filename . "'";
    $query2 = "UPDATE Mobile_Lists_NON_Unique SET Sending_Approved='Yes' WHERE Image_File_Name = '" . $filename . "'";
    mysqli_query($conn, $query1);
    mysqli_query($conn, $query2);
} elseif ($action == 'crop') {

    $croppedImage = file_get_contents($_FILES['croppedImage']['tmp_name']);
    $imageResource = imagecreatefromstring($croppedImage);

    // $croppedImage = $_POST['croppedImage'];
    $base64 = str_replace('data:image/png;base64,', '', $croppedImage);
    $data = base64_decode($base64);
    $parts = explode("_", $filename);
    $targetDirectory = "../nvme/{$parts[0]}/";
    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }
    $files = glob($targetDirectory . "*.{png}", GLOB_BRACE);
    $highestNumber = 0;
    foreach ($files as $file) {
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $fileParts = explode('.', $fileName); // Here, we split on "."
        $number = str_replace($parts[0], '', $fileParts[0]); // Then, remove the prefix from the file name
        if (is_numeric($number) && $number > $highestNumber) {
            $highestNumber = $number;
        }
    }
    $nextNumber = $highestNumber + 1;
    $targetFileName = $targetDirectory . $parts[0] . $nextNumber . '.png';
    // file_put_contents($targetFileName, $data);
    imagepng($imageResource, $targetFileName);
}
