<?php
	// Delete an image file
	function delete_image($filename) {
		if (file_exists($filename)) {
			unlink($filename);
			return true;
		}
		return false;
	}
	// Check if the delete form was submitted
	if (isset($_POST['filename'])) {
		$filename = $_POST['filename'];
		if (delete_image($filename)) {
			$page = "display_images.php?a=b";
			if($_POST['imageFolder']) $page .= "&imageFolder=".$_POST['imageFolder'];
			if(!empty($_POST['formfilter'])) $page .= "&filter=".$_POST['formfilter'];
			header("Location: ".$page);
			exit;
		} else {
			echo "Error deleting image";
		}
	}

	if(!isset($_REQUEST['imageFolder'])) $imagesfolder = "images";
	else $imagesfolder = $_REQUEST['imageFolder'];

	$images = glob('./'.$imagesfolder.'/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
	$num_images = count($images);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Image Gallery</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-R5XEKj5P3q5uOITrB07uxwRQ2zHvNnL7tMNf+yozjmmiX3qyx5KMVPb/8+pxpsuMGGVdWXm1MzPNVn0EAni/Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="display_images.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.12/css/jquery.Jcrop.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-jcrop/0.9.12/js/jquery.Jcrop.min.js"></script>
	<style>
	.previews {
	     width: 550px;
	     .source {
	       float: left;
	       overflow: hidden;
	     }
	     .crop {
	       float: right;
	       overflow: hidden;
	       img {
	         border: 1px solid black;
	       }
	    }
	}
	.jcrop-holder img{
         object-fit: contain !important;
         width: 100%;
         max-width: 500px;
         height: 400px;
       }
   .jcrop-holder,.crop{
       width: 500px;
       position: relative;
       height: 400px;
       aspect-ratio: 1/1;
   }
   .crop img{
       object-fit: contain !important;
       width: 500px;
       height: 350px;
       aspect-ratio: 1/1;
   }
    .previews {
      width: 100%;
       height: 500px;
       overflow-y: auto;
   }
   .jcrop-holder>div{
       height: initial;
   }
	#loader {
	  position: fixed;
	  left: 0;
	  top: 0;
	  width: 100%;
	  height: 100%;
	  z-index: 9999;
	  background: #fff url('new_loader.gif') no-repeat center center;
	}

	/* Hide the loader element by default */
	#loader.hidden {
	  display: none;
	}
	</style>
</head>
<body>
	<div class="filter-label">
		<label for="filter">Filter:</label>
		<span id="filter-count"><?php echo "(" . $num_images . " of " . $num_images . ")"; ?></span>
	</div>
	<div>
		 <input type="text" id="filter" name="filter" class="filter-input" value="<?php if(!isset($_REQUEST['filter'])) echo "";else echo $_REQUEST['filter']; ?>">
	</div>
	<div style="margin-bottom: 20px;"></div>

	<div class="gallery">
		<?php
			// Loop through the images and display them as thumbnails
			foreach ($images as $image) {
				echo '<div class="image" id="'.basename($image).'"><img class="thumbnail" src="' . $image . '?r='.rand().'" alt="' . basename($image) . '" onclick = cropped_images("'.$image.'","'.basename($image).'")><div class="filename">' . basename($image) . '</div><div class="delete-icon"> X </div></div>';
			}
		?>
	</div>

	<form method="POST" id="delete-form" style="display:none;">
		<input type="hidden" id="delete-filename" name="filename">
		<input type="hidden" id="imageFolder" name="imageFolder">
		<input type="hidden" id="formfilter" name="formfilter">
	</form>
	<form method="POST" action="display_images.php" id="submit_form">
		<input type="hidden" name="image_url" id="image_url">
		<input type="hidden" name="file_name" id="file_name">
		<input type="hidden" name="after_edit_cropped_imge" id="after_edit_cropped_imge">
		<input type="hidden" name="action" id="action">
		<button type="submit" style="display:none;" ></button>
	</form>
	<div id="loader"></div>
	<div class="modal fade" id="edit_image_ad_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="width:900px;">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Image Edit</h5>
					<button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="edit_image">
					<div class="previews">
						<div class="source">
							<h3>Source image</h3>
							<div id="image_input"></div>
							<div id="image_input_original" style="display:none;"></div>
						</div>
						<div class="crop">
							<h3>Crop preview</h3>
							<img id="image_output" />
							<img id="image_output_original_size" style="display:none;"/>
							<input type="hidden" id="edit_file_name" name = "edit_file_name">
						</div>
					</div>
				</div>  
				<div class="modal-footer">
					<a href="javascript:void(0)" class="btn btn-secondary" onclick="update_image()">Save</a>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	<script>
		$(document).ready(function() {
		  $("#loader").fadeOut();
		});
		function deleteImage(filename) {
			if (confirm("Are you sure you want to delete this image?")) {
				document.getElementById("delete-filename").value = "./<?php echo $imagesfolder; ?>/"+filename;
				document.getElementById("imageFolder").value = "<?php echo $imagesfolder; ?>";
				document.getElementById("formfilter").value = document.getElementById("filter").value;
				document.getElementById("delete-form").submit();
			}
		}

		// Filter images by filename

		function filterImages() {
			var filter = document.getElementById("filter").value.toLowerCase();
			var images = document.querySelectorAll(".gallery .image");
			var num_matches = 0;

			for (var i = 0; i < images.length; i++) {
				var filename = images[i].querySelector(".filename").textContent.toLowerCase();
				if (filename.includes(filter)) {
					images[i].style.display = "block";
					num_matches++;
				} else {
					images[i].style.display = "none";
				}
			}

			// Update the filter count
			document.getElementById("filter-count").textContent = "(" + num_matches + " of " + <?php echo $num_images; ?> + ")";
		}

		document.getElementById("filter").addEventListener("blur", filterImages);

		// Call the function on page load
		window.addEventListener("load", filterImages);

		// Show the delete confirmation popup when the delete icon is clicked
		var deleteIcons = document.querySelectorAll(".delete-icon");
		for (var i = 0; i < deleteIcons.length; i++) {
			deleteIcons[i].addEventListener("click", function() {
				var filename = this.parentNode.querySelector(".filename").textContent;
				deleteImage(filename);
			});
		}

		// update cropped image
		function update_image()
		{
			$("#after_edit_cropped_imge").val($("#image_output_original_size").attr('src'));
			$("#action").val('update_cropped_image');
			$("#submit_form").submit();
			$("#loader").fadeIn();
		}

		var multiplyFactor;
		// Crop Images
		function cropped_images(image_name,filename)
		{
			$("#image_url").val(image_name);
			$("#file_name").val(filename);
			$("#edit_image_ad_modal").modal('show');
			$("#edit_file_name").val(filename);

			var imageUrl = image_name;
			var maxWidth = 500;

			var image = new Image();

			image.onload = function() {

				var canvasinput = document.createElement('canvas');
				var canvasOriginalSize = document.createElement('canvas');

				var width = image.width;
				var height = image.height;

				multiplyFactor = 1;

				if (width > maxWidth) {
					height *= maxWidth / width;
					multiplyFactor = maxWidth / width;
					width = maxWidth;
				}

				canvasinput.width = width;
				canvasinput.height = height;
				canvasOriginalSize.width = image.width;
				canvasOriginalSize.height = image.height;

				var ctx = canvasinput.getContext('2d');
				var ctxOriginal = canvasOriginalSize.getContext('2d');

				ctx.drawImage(image, 0, 0, image.width, image.height, 0, 0, width, height);
				ctxOriginal.drawImage(image,0,0,image.width, image.height, 0, 0, image.width, image.height);


				$('#image_input').html(['<img src="', canvasinput.toDataURL(), '"/>'].join(''));
				$('#image_input_original').html(['<img src="', canvasOriginalSize.toDataURL(), '"/>'].join(''));

				var img = $('#image_input img')[0];
				var imgOriginal = $('#image_input_original img')[0];

				var canvasoutput = document.createElement('canvas');
				var canvasOriginalForOutput = document.createElement('canvas');

				var ctx = canvasoutput.getContext('2d');
				var ctxOriginalForOutput = canvasOriginalForOutput.getContext('2d');

				$('#image_input img').Jcrop({
					bgColor: 'black',
					bgOpacity: .6,
					onSelect: imgSelect
				});

				$('#image_input_original img').Jcrop({
					bgColor: 'black',
					bgOpacity: .6,
					onSelect: imgSelect
				});

				function imgSelect(selection) {

					myx = selection.x / multiplyFactor;
					myy = selection.y / multiplyFactor;
					myw = selection.w / multiplyFactor;
					myh = selection.h / multiplyFactor;

					canvasoutput.width = selection.w;
					canvasoutput.height = selection.h;

					canvasOriginalForOutput.width = myw;
					canvasOriginalForOutput.height = myh;

					ctx.drawImage(img, selection.x, selection.y, selection.w, selection.h, 0, 0, canvasoutput.width, canvasoutput.height);
					ctxOriginalForOutput.drawImage(imgOriginal, myx, myy, myw, myh, 0, 0, canvasOriginalForOutput.width, canvasOriginalForOutput.height);

					$('#image_output').attr('src', canvasoutput.toDataURL());
					$('#image_output_original_size').attr('src', canvasOriginalForOutput.toDataURL());
				}
			};
			image.src = imageUrl;
		}
	</script>
<?php
if($_POST['action']=='update_cropped_image'){
		$image_data = $_POST['after_edit_cropped_imge'];
		$type="png";
		list($type, $image_data) = explode(';', $image_data);
		list(, $image_data)      = explode(',', $image_data);
		$image_data = base64_decode($image_data);
		$image_path = './images/'.$_POST['file_name'];
		$destination ='./images/';
		file_put_contents($image_path,$image_data);
		if(file_exists($image_path)==1)
		{
			?>
			<script type="text/javascript">
				alert("Cropped Successfully");
				var file_name = '<?php echo $_POST['file_name'];?>';
				setTimeout(document.getElementById(file_name).scrollIntoView({
				    behavior: 'smooth'
				 }),2000);
				
			</script>
			<?php
		}else{
			?>
			<script type="text/javascript">
				alert("Something went wrong while uploading cropped image.");
			</script>
			<?php
		}
	}
?>
</body>
</html>
