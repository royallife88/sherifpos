$(document).on("click", "#add_dining_room_btn", function () {
    var form = $("#dining_room_form");
    var data = form.serialize();
    $.ajax({
        url: "/dining-room",
        type: "POST",
        data: data,
        success: function (result) {
            if (result.success === true) {
                toastr.success(result.msg);
                $(".view_modal").modal("hide");
                get_dining_content();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

$(document).on("change", "#dining_room_name", function () {
    let name = $(this).val();

    $.ajax({
        method: "GET",
        url: "/dining-room/check-dining-room-name",
        data: { name },
        success: function (result) {
            if (result.success == false) {
                toastr.error(result.msg);
            }
        },
    });
});

function get_dining_content() {
    $.ajax({
        method: "GET",
        url: "/dining-room/get-dining-room-content",
        data: {},
        success: function (result) {
            $("#dining_content").empty().append(result);
        },
    });
}


$(document).on("click", "#add_dining_table_btn", function () {
    var form = $("#dining_table_form");
    var data = form.serialize();
    $.ajax({
        url: "/dining-table",
        type: "POST",
        data: data,
        success: function (result) {
            if (result.success === true) {
                toastr.success(result.msg);
                $(".view_modal").modal("hide");
                get_dining_content();
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

$(document).on("change", "#dining_table_name", function () {
    let name = $(this).val();

    $.ajax({
        method: "GET",
        url: "/dining-table/check-dining-table-name",
        data: { name },
        success: function (result) {
            if (result.success == false) {
                toastr.error(result.msg);
            }
        },
    });
});
