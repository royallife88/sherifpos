$(document).ready(function () {
    //Add products
    if ($("#search_product").length > 0) {
        $("#search_product")
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(
                        "/purchase-order/get-products",
                        { store_id: $("#store_id").val(), term: request.term },
                        response
                    );
                },
                minLength: 2,
                response: function (event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                            .data("ui-autocomplete")
                            ._trigger("select", "autocompleteselect", ui);
                        $(this).autocomplete("close");
                    } else if (ui.content.length == 0) {
                        swal("Product not found");
                    }
                },
                select: function (event, ui) {
                    $(this).val(null);
                    get_label_product_row(
                        ui.item.product_id,
                        ui.item.variation_id
                    );
                },
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append("<div>" + item.text + "</div>")
                .appendTo(ul);
        };
    }
});

function get_label_product_row(product_id, variation_id) {
    var store_id = $("#store_id").val();
    console.log(store_id);
    if (product_id) {
        var row_count = $("table#product_table tbody tr").length;
        $.ajax({
            method: "GET",
            url: "/purchase-return/add-product-row",
            dataType: "html",
            data: {
                product_id: product_id,
                row_count: row_count,
                variation_id: variation_id,
                store_id: store_id,
            },
            success: function (result) {
                $("table#product_table tbody").append(result);
                calculate_sub_totals();
            },
        });
    }
}
function calculate_sub_totals() {
    var total = 0;
    $("#product_table > tbody  > tr").each((ele, tr) => {
        let quantity = __read_number($(tr).find(".quantity"));
        let purchase_price = __read_number($(tr).find(".purchase_price"));
        let current_stock = __read_number($(tr).find(".current_stock"));
        let sub_total = purchase_price * quantity;
        __write_number($(tr).find(".sub_total"), sub_total);
        $(tr)
            .find(".sub_total_span")
            .text(__currency_trans_from_en(sub_total, false));
        total += sub_total;

        total_sotck = current_stock - quantity;
        $(tr)
            .find(".current_stock_span")
            .text(__currency_trans_from_en(total_sotck, false));
    });

    __write_number($("#grand_total"), total);
    __write_number($("#final_total"), total);
    __write_number($("#amount"), total);
    $(".grand_total_span").text(__currency_trans_from_en(total, false));
}

$(document).on("change", ".quantity, .purchase_price", function () {
    calculate_sub_totals();
});
$(document).on("click", ".remove_row", function () {
    $(this).closest("tr").remove();
});
//payment related script

$("#payment_status").change(function () {
    var payment_status = $(this).val();

    if (payment_status === "paid" || payment_status === "partial") {
        $(".not_cash_fields").addClass("hide");
        $("#method").change();
        $(".payment_fields").removeClass("hide");
    } else {
        $(".payment_fields").addClass("hide");
    }
    if (payment_status === "pending" || payment_status === "partial") {
        $(".due_fields").removeClass("hide");
    } else {
        $(".due_fields").addClass("hide");
    }
    if (payment_status === "pending") {
        $(".not_cash_fields").addClass("hide");
        $("#method").attr("required", false);
        $(".not_cash").attr("required", false);
    }
    if (payment_status === "paid") {
        $(".due_fields").addClass("hide");
    }
});
$("#method").change(function () {
    var method = $(this).val();

    if (method === "cash") {
        $(".not_cash_fields").addClass("hide");
        $(".not_cash").attr("required", false);
    } else {
        $(".not_cash_fields").removeClass("hide");
        $(".not_cash").attr("required", true);
    }
});

$("#purchase_return_form").submit(function () {
    $(this).validate();
});
