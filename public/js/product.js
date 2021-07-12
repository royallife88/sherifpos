$(document).ready(function () {
    tinymce.init({
        selector: "#product_details",
        height: 130,
        plugins: [
            "advlist autolink lists link charmap print preview anchor textcolor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste code wordcount",
        ],
        toolbar:
            "insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat",
        branding: false,
    });
});
$(".different_prices_for_stores_div").slideUp();
$("#different_prices_for_stores").change(function () {
    if ($(this).prop("checked")) {
        $(".different_prices_for_stores_div").slideDown();
    } else {
        $(".different_prices_for_stores_div").slideUp();
    }
});
$(".this_product_have_variant_div").slideUp();
$("#this_product_have_variant").change(function () {
    if ($(this).prop("checked")) {
        $(".this_product_have_variant_div").slideDown();
    } else {
        $(".this_product_have_variant_div").slideUp();
    }
});
$(".show_to_customer_type_div").slideUp();
$("#show_to_customer").change(function () {
    if ($(this).prop("checked")) {
        $(".show_to_customer_type_div").slideUp();
    } else {
        $(".show_to_customer_type_div").slideDown();
    }
});
$(document).on("click", ".remove_row", function () {
    row_id = $(this).closest("tr").data('row_id');
    $(this).closest("tr").remove();
    $('.variant_store_checkbox_'+row_id).remove();
    $('.variant_store_prices_'+row_id).remove();

});

$(document).on("click", ".add_row", function () {
    var row_id = parseInt($("#row_id").val());
    $.ajax({
        method: "get",
        url: "/product/get-variation-row?row_id=" + row_id,
        data: {},
        contentType: "html",
        success: function (result) {
            $("#variation_table tbody").append(result);
            $(".row_" + row_id)
                .find(".selectpicker")
                .selectpicker("refresh");
            $(".variant_store_prices_" + row_id).slideUp();

            $("#row_id").val(row_id + 1);
        },
    });
});

$(document).on("click", ".variant_different_prices_for_stores", function () {
    let row_id = $(this).data("row_id");

    if ($(this).prop("checked")) {
        $(".variant_store_prices_" + row_id).slideDown();
    } else {
        $(".variant_store_prices_" + row_id).slideUp();
    }
});

Dropzone.autoDiscover = false;
myDropzone = new Dropzone("div#my-dropzone", {
    addRemoveLinks: true,
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 100,
    maxFilesize: 12,
    paramName: "images",
    clickable: true,
    method: "POST",
    url: "/product",
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    renameFile: function (file) {
        var dt = new Date();
        var time = dt.getTime();
        return time + file.name;
    },
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    init: function () {
        var myDropzone = this;
        $("#submit-btn").on("click", function (e) {
            e.preventDefault();
            if ($("#product-form").valid()) {
                tinyMCE.triggerSave();
                if (myDropzone.getAcceptedFiles().length) {
                    myDropzone.processQueue();
                } else {
                    $.ajax({
                        type: "POST",
                        url: "/product",
                        data: $("#product-form").serialize(),
                        success: function (response) {
                            if (response.success) {
                                swal("Success", response.msg, "success");
                            }
                        },
                        error: function (response) {
                            if (!response.success) {
                                swal("Error", response.msg, "error");
                            }
                        },
                    });
                }
            }
        });

        this.on("sending", function (file, xhr, formData) {
            var data = $("#product-form").serializeArray();
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            });
        });
        this.on("complete", function (file) {
            this.removeAllFiles(true);
        });
    },
    error: function (file, response) {
        console.log(response);
    },
    successmultiple: function (file, response) {
        if (response.success) {
            swal("Success", response.msg, "success");
        }
        if (!response.success) {
            swal("Error", response.msg, "error");
        }
    },
    completemultiple: function (file, response) {},
    reset: function () {
        this.removeAllFiles(true);
    },
});

$(document).on("submit", "form#quick_add_product_class_form", function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var class_id = result.id;
                $.ajax({
                    method: "get",
                    url: "/product-class/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#product_class_id").empty().append(data_html);
                        $("#product_class_id").selectpicker("refresh");
                        $("#product_class_id").val(class_id).change();
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});
var sub_category_id = null;
$(document).on("submit", "form#quick_add_category_form", function (e) {
    e.preventDefault();
    var data = new FormData(this);

    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var category_id = result.category_id;
                sub_category_id = result.sub_category_id;
                $.ajax({
                    method: "get",
                    url: "/category/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#category_id").empty().append(data_html);
                        $("#category_id").selectpicker("refresh");
                        if (category_id) {
                            $("#category_id").val(category_id).change();
                        }
                        if (sub_category_id) {
                            $("#sub_category_id").val(sub_category_id);
                        }
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});

$(document).on("change", "#category_id", function () {
    $.ajax({
        method: "get",
        url:
            "/category/get-sub-category-dropdown?category_id=" +
            $("#category_id").val(),
        data: {},
        contentType: "html",
        success: function (result) {
            $("#sub_category_id").empty().append(result).change();
            $("#sub_category_id").selectpicker("refresh");

            if (sub_category_id) {
                $("#sub_category_id").selectpicker("val", sub_category_id);
            }
        },
    });
});

$(document).on("submit", "form#quick_add_brand_form", function (e) {
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var brand_id = result.brand_id;
                $.ajax({
                    method: "get",
                    url: "/brand/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#brand_id").empty().append(data_html);
                        $("#brand_id").selectpicker("refresh");
                        $("#brand_id").selectpicker("val", brand_id);
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});
$(document).on("submit", "form#quick_add_tax_form", function (e) {
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var tax_id = result.tax_id;
                $.ajax({
                    method: "get",
                    url: "/tax/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#tax_id").empty().append(data_html);
                        $("#tax_id").selectpicker("refresh");
                        $("#tax_id").selectpicker("val", tax_id);
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});
var multiple_units_array = [];
$("#multiple_units").change(function () {
    multiple_units_array.push($(this).val());
});
$(document).on("submit", "form#quick_add_unit_form", function (e) {
    $("form#quick_add_unit_form").validate();
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var unit_id = result.unit_id;
                multiple_units_array.push(unit_id);
                $.ajax({
                    method: "get",
                    url: "/unit/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#multiple_units").empty().append(data_html);
                        $("#multiple_units").selectpicker("refresh");
                        $("#multiple_units").selectpicker(
                            "val",
                            multiple_units_array
                        );
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});
var multiple_colors_array = [];
$("#multiple_colors").change(function () {
    multiple_colors_array.push($(this).val());
});
$(document).on("submit", "form#quick_add_color_form", function (e) {
    $("form#quick_add_color_form").validate();
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var color_id = result.color_id;
                multiple_colors_array.push(color_id);
                $.ajax({
                    method: "get",
                    url: "/color/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#multiple_colors").empty().append(data_html);
                        $("#multiple_colors").selectpicker("refresh");
                        $("#multiple_colors").selectpicker(
                            "val",
                            multiple_colors_array
                        );
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});
var multiple_sizes_array = [];
$("#multiple_sizes").change(function () {
    multiple_sizes_array.push($(this).val());
});
$(document).on("submit", "form#quick_add_size_form", function (e) {
    $("form#quick_add_size_form").validate();
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var size_id = result.size_id;
                multiple_sizes_array.push(size_id);
                $.ajax({
                    method: "get",
                    url: "/size/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#multiple_sizes").empty().append(data_html);
                        $("#multiple_sizes").selectpicker("refresh");
                        $("#multiple_sizes").selectpicker(
                            "val",
                            multiple_sizes_array
                        );
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});
var multiple_grades_array = [];
$("#multiple_grades").change(function () {
    multiple_grades_array.push($(this).val());
});
$(document).on("submit", "form#quick_add_grade_form", function (e) {
    $("form#quick_add_grade_form").validate();
    e.preventDefault();
    var data = new FormData(this);
    $.ajax({
        method: "post",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(".view_modal").modal("hide");
                var grade_id = result.grade_id;
                multiple_grades_array.push(grade_id);
                $.ajax({
                    method: "get",
                    url: "/grade/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#multiple_grades").empty().append(data_html);
                        $("#multiple_grades").selectpicker("refresh");
                        $("#multiple_grades").selectpicker(
                            "val",
                            multiple_grades_array
                        );
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});

$("#expiry_date").change(function () {
    if (
        $(this).val() != undefined &&
        $(this).val() != "" &&
        $(this).val() != null
    ) {
        $(".warning").removeClass("hide");
        $(".convert_status_expire").removeClass("hide");
    } else {
        $(".warning").addClass("hide");
        $(".convert_status_expire").addClass("hide");
    }
});

$(document).on("change", "#sell_price", function () {
    $(".store_prices").val($(this).val());
});