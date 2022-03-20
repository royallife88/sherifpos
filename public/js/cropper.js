$(document).ready(function () {
    var $modal = $("#cropper_modal");

    var image = document.getElementById("sample_image");

    var cropper;

    $(document).on("change", "#upload_image", function (event) {
        var files = event.target.files;

        var done = function (url) {
            image.src = url;
            $modal.modal("show");
        };

        if (files && files.length > 0) {
            reader = new FileReader();
            reader.onload = function (event) {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    $modal
        .on("shown.bs.modal", function () {
            cropper = new Cropper(image, {
                initialAspectRatio: 1 / 1,
                cropBoxResizable: false,
                aspectRatio: 1 / 1,
                restore: false,
                guides: false,
                center: false,
                viewMode: 2,
                preview: ".preview_div",
            });
        })
        .on("hidden.bs.modal", function () {
            cropper.destroy();
            cropper = null;
        });

    $(document).on("click", "#crop", function () {
        canvas = cropper.getCroppedCanvas({
            aspectRatio: 1 / 1,
            dragMode: "move",
        });

        canvas.toBlob(function (blob) {
            url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function () {
                var base64data = reader.result;

                $.ajax({
                    url: "/general/upload-image-temp",
                    method: "POST",
                    data: { image: base64data },
                    success: function (data) {
                        if (data.success) {
                            $("#uploaded_image").attr("src", data.url);
                            $("#uploaded_image_name").val(data.filename);
                            $modal.modal("hide");
                        }
                    },
                });
            };
        });
    });
});
