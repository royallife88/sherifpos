$(document).ready(function () {
    //Prevent enter key function except texarea
    $("form").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != "TEXTAREA") {
            e.preventDefault();
            return false;
        }
    });
    //Add products
    if ($("#search_product").length > 0) {
        $("#search_product")
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(
                        "/purchase-order/get-products?is_raw_material=" +
                            $("#is_raw_material").val(),
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
    //Get item addition method
    var sender_store_id = parseInt($("#sender_store_id").val());
    if (isNaN(sender_store_id)) {
        swal("Please select store first.");
        return false;
    }
    var add_via_ajax = true;

    var is_added = false;

    //Search for variation id in each row of pos table
    $("#product_table tbody")
        .find("tr")
        .each(function () {
            var row_v_id = $(this).find(".variation_id").val();

            if (row_v_id == variation_id && !is_added) {
                add_via_ajax = false;
                is_added = true;

                //Increment product quantity
                qty_element = $(this).find(".quantity");
                var qty = __read_number(qty_element);
                __write_number(qty_element, qty + 1);
                qty_element.change;
                calculate_sub_totals();
                $("input#search_product").focus();
            }
        });

    if (add_via_ajax) {
        var row_count = $("table#product_table tbody tr").length;
        $.ajax({
            method: "GET",
            url: "/transfer/add-product-row?sender_store_id=" + sender_store_id,
            dataType: "html",
            data: {
                product_id: product_id,
                row_count: row_count,
                variation_id: variation_id,
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
        let sub_total = purchase_price * quantity;
        __write_number($(tr).find(".sub_total"), sub_total);
        $(tr)
            .find(".sub_total_span")
            .text(__currency_trans_from_en(sub_total, false));
        total += sub_total;
    });

    __write_number($("#final_total"), total);
    $(".final_total_span").text(__currency_trans_from_en(total, false));
}

$(document).on("change", ".quantity", function () {
    let quantity = __read_number($(this));
    let max_quantity = $(this).attr("max");

    if (quantity > max_quantity) {
        swal("Quantity should not exceed the available quantity");
        $(this).val(max_quantity);
    }
});
$(document).on("change", ".quantity, .purchase_price", function () {
    calculate_sub_totals();
});
$(document).on("click", ".remove_row", function () {
    let index = $(this).data("index");

    $(this).closest("tr").remove();
    $(".row_details_" + index).remove();
    calculate_sub_totals();
});
