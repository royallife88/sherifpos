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

function get_dining_content(dining_table_id = null) {
    if (dining_table_id == null) {
        dining_table_id = $("#dining_table_id").val();
    }

    $.ajax({
        method: "GET",
        url: "/dining-room/get-dining-room-content",
        data: {
            dining_table_id: dining_table_id,
        },
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
                get_dining_content(result.dining_table_id);
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

$(document).on("click", ".table_action", function () {
    let table_id = $(this).data("table_id");
    $("#dining_table_id").val(table_id);
    $.ajax({
        method: "GET",
        url: "/dining-table/get-dining-table-action/" + table_id,
        data: {},
        success: function (result) {
            $("#dining_table_action_modal").empty().append(result);
            let enable_the_table_reservation = $('#enable_the_table_reservation').val();
            if(enable_the_table_reservation == '1'){
                $("#dining_table_action_modal").modal("show");
            }else{
                $('#table_action_btn').click();
            }
        },
    });
});
$(document).on("change", "#table_status", function () {
    let table_status = $(this).val();
    if (table_status == "reserve") {
        $(".reserve_div").removeClass("hide");
    } else {
        $(".reserve_div").addClass("hide");
    }
});
$(document).on("click", "#table_action_btn", function () {
    let table_status = $("#table_status").val();
    let table_id = $("#dining_table_id").val();
    if (table_status == "reserve" || table_status == "cancel_reservation") {
        if (table_status == "reserve") {
            if (
                !$("#table_customer_name").val() ||
                !$("#table_customer_mobile_number").val() ||
                !$("#table_date_and_time").val()
            ) {
                toastr.error(LANG.please_fill_all_fields);
                return false;
            }
        }
        $.ajax({
            method: "post",
            url: "/dining-table/update-dining-table-data/" + table_id,
            data: {
                customer_name: $("#table_customer_name").val(),
                customer_mobile_number: $(
                    "#table_customer_mobile_number"
                ).val(),
                date_and_time: $("#table_date_and_time").val(),
                status: $("#table_status").val(),
            },
            success: function (result) {
                if (result.success == "1") {
                    swal("Success", result.msg, "success");
                    $("#dining_table_action_modal").modal("hide");
                    get_dining_content();
                }
            },
        });
    } else if (table_status == "order") {
        $("#dining_table_id").val(table_id);
        $(".reserve_div").addClass("hide");
        $(".table_room_hide").addClass("hide");
        $(".table_room_show").removeClass("hide");
        $(".transaction-list").css("height", "55vh");
        $.ajax({
            method: "get",
            url: "/dining-table/get-table-details/" + table_id,
            data: {},
            success: function (result) {
                $("span.room_name").text(result.dining_room.name);
                $("span.table_name").text(result.dining_table.name);
                $("#dining_table_action_modal").modal("hide");
                $("#dining_model").modal("hide");
            },
        });
    } else {
        $(".reserve_div").addClass("hide");
        $(".table_room_show").addClass("hide");
        $(".table_room_hide").removeClass("hide");
        $(".transaction-list").css("height", "45vh");
    }
});

$(document).on("click", ".order_table", function () {
    $("#dining_model").modal("hide");
});
function reset_dinging_table_action_modal() {
    $("#table_customer_name").val("");
    $("#table_customer_mobile_number").val("");
    $("#table_date_and_time").val("");
    $("#table_status").val("");
    $("#table_status").selectpicker("refresh");
}
