<?php
// connection include
include_once "../includes/connect.php";

// Image URL prefix
$imgurl = "https://marketing.buzzgully.com/nvme/";

// query

if (!$_REQUEST['date']) $_REQUEST['date'] = Date('Y-m-d', time() + (5.5 * 3600));
if (!$_REQUEST['limit']) $_REQUEST['limit'] = 20;

$query = "SELECT Image_File_Name, count(1) as No_of_Mobiles FROM `Mobile_Lists_NON_Unique` WHERE DATE(Timestamp)='" . $_REQUEST['date'] . "' AND Sending_Approved = 'No' GROUP By Image_File_Name ORDER BY MIN(Timestamp) LIMIT " . $_REQUEST['limit'];

// execution
$result = mysqli_query($conn, $query);

$smallImageBaseDir = '/var/www/d78236gbe27823/nvme';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Paper Scrutiny</title>
    <link rel="stylesheet" type="text/css" href="papers_scruitiny.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
</head>

<body>
    <div class="container">
        <div class="row">

            <?php
            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    echo '<div class="col-sm-6 col-md-4 col-lg-3">';
                    echo '<div class="card" data-filename="' . $row['Image_File_Name'] . '">'; //add data attribute for filename
                    echo '<img onclick=addclass(this) src="' . $imgurl . $row['Image_File_Name'] . '" class="card-img-top imageModalTrigger" alt="Mobile Image">';
                    echo '<div class="card-title text-center mt-2">' . $row['Image_File_Name'] . '</div>';

                    $a = explode("_", $row['Image_File_Name']);
                    $papershortname = $a[0];
                    $smallImageDir = "$smallImageBaseDir/$papershortname";
                    $largeImagePath = "/var/www/d78236gbe27823/nvme/" . $row['Image_File_Name'];

                    $found = false;

                    foreach (glob("$smallImageDir/*.{jpeg,jpg,png}", GLOB_BRACE) as $smallImagePath) {

                        $output = exec("iii " . $largeImagePath . " " . $smallImagePath);

                        if (trim($output) == 'Image found.') {
                            $found = true;
                            break;
                        }
                    }

                    echo '<div class="card-body">';
                    if ($found) {
                        echo '<button class="btn btn-danger deleteBtn">Delete ' . $row['No_of_Mobiles'] . ' Numbers</button>&nbsp;&nbsp;'; //add class for delete button
                        echo '<button class="btn btn-success approveBtn" style="height: 62px !important; border: 5px solid #1b1d59;">Approve</button>';
                    } else {
                        echo '<button class="btn btn-danger deleteBtn" style="border: 5px solid #1b1d59;">Delete ' . $row['No_of_Mobiles'] . ' Numbers</button>&nbsp;&nbsp;'; //add class for delete button
                        echo '<button class="btn btn-success approveBtn" style="height: 62px !important;">Approve</button>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12">No data found</div>';
            }
            ?>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="papers_scruitiny.js"></script>

        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <!-- The image will be inserted here -->
                    <img id="modalImage" src="" class="img-fluid" alt="Mobile Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="crop_and_save">Save Crop</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="papers_scruitiny.js"></script>
</body>

</html>