<?php 
include "../includes/connect.php";
if(!isset($_SESSION['Admin_ID']) && $_SESSION['Admin_ID']=='')
{
    ?>
    <script type="text/javascript">
        window.location.href="<?php echo "https://marketing.buzzgully.com/admin/";?>";
    </script>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag and Drop File Upload</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <style>
        .dropzone {
            border: 2px dashed #0087f7;
            background-color: #f5f5f5;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
        }
        .dropzone .dz-message {
            font-weight: bold;
            text-align: center;
            color: #707070;
        }
        .container {
            margin-top: 20px;
        }
        .dropzone .dz-error-mark svg g g {
            fill: #c00 !important;
        }
        .dropzone .dz-success-mark svg g path{
            fill: #198754 !important;
        }
    </style>
</head>
<body>
    <div class="container"><span style="width: 100%;text-align: right;font-size: small;color: brown;font-style: italic;display: block;">Version 2.0</span>
        <h1>Welcome to Buzzgully, You can upload your papers here</h1>
        <p>Upload Papers only for these cities Jaipur,Jodhpur,Udaipur,Ahmedabad,Surat,Vadodara,Bhavnagar,Mumbai,Pune,Thane</p>
        <div id="dropzoneArea" class="dropzone">
            <div class="dz-message" data-dz-message>
                <span>Drop files here or click to upload</span>
            </div>
        </div>
        <div><a href="https://marketing.buzzgully.com/marketing/Whatsapp/display_images.php">Click to View & Edit Uploaded Images</a></div>
    </div>
    <script src="script.js"></script>
</body>
</html>

