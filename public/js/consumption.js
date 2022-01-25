$(document).on("click", ".remove_row", function () {
    $(this).closest("tr").remove();
});
$(document).on("click", ".add_row", function () {
    let row_id = parseInt($("#row_id").val());
    $("#row_id").val(row_id + 1);

    $.ajax({
        method: "get",
        url: "/consumption/add-row",
        data: { row_id },
        success: function (result) {
            $("table#consumption_table > tbody").append(result);
            $(".selectpicker").selectpicker();
        },
    });
});
$(document).on("change", "select.raw_material_id", function () {
    let tr = $(this).closest("tr");
    let first_td = $(this).closest("td");
    let raw_materials = $(this).val();
    let store_id = $("#store_id").val();
    let this_row_id = tr.find(".this_row_id").val();

    $.ajax({
        method: "get",
        url: "/consumption/get-raw-material-details",
        data: {
            raw_material_id: raw_materials,
            store_id: store_id,
            this_row_id: this_row_id,
        },
        success: function (result) {
            $(first_td).siblings().remove();
            $(tr).append(result);
        },
    });
});
