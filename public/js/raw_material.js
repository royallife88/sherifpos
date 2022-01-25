$(document).on("click", ".add_product_row", function () {
    let row_id = parseInt($("#row_id").val());
    $("#row_id").val(row_id + 1);
    $.ajax({
        method: "GET",
        url: "/raw-material/add-product-row",
        data: { row_id: row_id },
        success: function (result) {
            $("#consumption_table tbody").append(result);
            $(".selectpicker").selectpicker("refresh");
        },
    });
});

$(document).on("change", "select.unit_id", function () {
    let unit_id = $(this).val();
    let tr = $(this).closest("tr");
    $.ajax({
        method: "GET",
        url: "/unit/get-unit-details/" + unit_id,
        data: {},
        success: function (result) {
            tr.find(".info_text").removeClass("hide");
            tr.find(".info_text").text(result.unit.description);
        },
    });
});
