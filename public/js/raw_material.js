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
            $("select.unit_id").val($('select#multiple_units').val());
            $("select.unit_id").selectpicker("refresh");
        },
    });
});

$(document).on("change", "select#multiple_units", function () {
    let selected_unit_text = $(this).find("option:selected").text();
    $(".unit_label").text(selected_unit_text);
    $("select.unit_id").attr('readonly', true);
    $("select.unit_id").val($(this).val());
    $("select.unit_id").selectpicker("refresh");
    $("#alert_quantity_unit_id").val($(this).val());
    $("#alert_quantity_unit_id").selectpicker("refresh");
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
