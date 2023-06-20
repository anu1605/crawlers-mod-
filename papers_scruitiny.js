$(document).ready(function () {
    $(".imageModalTrigger").click(function () {
        var imageUrl = $(this).attr('src');
        $("#modalImage").attr('src', imageUrl);
        $('#imageModal').modal('show');
    });

    $(".deleteBtn").click(function () {
        if (confirm("Are you sure you want to delete this record?")) {
            var card = $(this).closest('.card');
            var filename = card.data('filename');

            $.ajax({
                type: "POST",
                url: "ajax_for_papers_scruitiny.php",
                data: { filename: filename, action: 'delete' },
                success: function () {
                    card.css("display", "none");
                },
                error: function () {
                    alert("An error occurred while deleting the record.");
                }
            });
        }
    });

    $(".approveBtn").click(function () {
        var card = $(this).closest('.card');
        var filename = card.data('filename');

        $.ajax({
            type: "POST",
            url: "ajax_for_papers_scruitiny.php",
            data: { filename: filename, action: 'approve' },
            success: function () {
                card.css("display", "none");
            },
            error: function () {
                alert("An error occurred while approving the record.");
            }
        });
    });

    let cropper;

    $("#crop_and_save").click(function () {
        var card = $(".newclass").closest('.card');
        var filename = card.data('filename');
        if (cropper) {
            cropper.getCroppedCanvas().toBlob((blob) => {
                const formData = new FormData();

                formData.append('croppedImage', blob, filename);
                formData.append('filename', filename);
                formData.append('action', 'crop');

                // formData.append('croppedImage', blob);
                // formData.append('action', 'crop');

                $.ajax('ajax_for_papers_scruitiny.php', {
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success() {
                        console.log('Upload success');
                        $('#imageModal').modal('hide');
                    },
                    error() {
                        console.log('Upload error');
                    },
                });
            });
        } else {
            console.log("Cropper instance is not available.");
        }
    });

    $('#imageModal').on('shown.bs.modal', function () {
        let image = document.getElementById('modalImage');
        cropper = new Cropper(image, {
            crop(event) {
                console.log(event.detail.x);
                console.log(event.detail.y);
                console.log(event.detail.width);
                console.log(event.detail.height);
                console.log(event.detail.rotate);
                console.log(event.detail.scaleX);
                console.log(event.detail.scaleY);
            },
        });
    });

    $('#imageModal').on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });
});

function addclass(img) {
    img.classList.add("newclass");
    $('#imageModal').on('hidden.bs.modal', function () {
        img.classList.remove("newclass");
    });
}
