$(document).on("click", "#category-filter", function (e) {
    e.stopPropagation();
    $(".filter-window").show("slide", { direction: "right" }, "fast");
    $(".category").show();
    $(".brand").hide();
    $(".sub_category").hide();
});

$(document).on("click", "#brand-filter", function (e) {
    e.stopPropagation();
    $(".filter-window").show("slide", { direction: "right" }, "fast");
    $(".brand").show();
    $(".category").hide();
    $(".sub_category").hide();
});

$(document).on("click", "#sub-category-filter", function (e) {
    e.stopPropagation();
    $(".filter-window").show("slide", { direction: "right" }, "fast");
    $(".brand").hide();
    $(".category").hide();
    $(".sub_category").show();
});

$(".selling_filter, .price_filter, .expiry_filter, .sorting_filter").change(
    function () {
        let class_name = $(this).attr("class");
        $("." + class_name).prop("checked", false);
        $(this).prop("checked", true);
    }
);

$("body").on("click", function (e) {
    $(".filter-window").hide("slide", { direction: "right" }, "fast");
});

function getFilterCheckboxValue(class_name) {
    let val = null;
    $("." + class_name).each((i, ele) => {
        if ($(ele).prop("checked")) {
            val = $(ele).val();
        }
    });
    return val;
}

$(document).on("click", ".filter-by", function () {
    var id = $(this).data("id");
    var type = $(this).data("type");

    var selling_filter = getFilterCheckboxValue("selling_filter");
    var price_filter = getFilterCheckboxValue("price_filter");
    var expiry_filter = getFilterCheckboxValue("expiry_filter");
    var sale_promo_filter = getFilterCheckboxValue("sale_promo_filter");
    var sorting_filter = getFilterCheckboxValue("sorting_filter");

    if (id && type) {
        $.ajax({
            method: "get",
            url: "/pos/get-product-items-by-filter/" + id + "/" + type,
            data: {
                selling_filter,
                price_filter,
                expiry_filter,
                sale_promo_filter,
                sorting_filter,
            },
            contentType: "html",
            success: function (result) {
                $("#filter-product-table > tbody").empty().append(result);
            },
        });
    }
});

$(document).ready(function () {
    //Add products
    if ($("#search_product").length > 0) {
        $("#search_product")
            .autocomplete({
                source: function (request, response) {
                    $.getJSON(
                        "/pos/get-products",
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
    if (product_id) {
        var store_id = $("#store_id").val();
        var row_count = $("table#product_table tbody tr").length;
        $.ajax({
            method: "GET",
            url: "/pos/add-product-row",
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
        let sell_price = __read_number($(tr).find(".sell_price"));
        let sub_total = sell_price * quantity;
        __write_number($(tr).find(".sub_total"), sub_total);
        $(tr)
            .find(".sub_total_span")
            .text(__currency_trans_from_en(sub_total, false));
        total += sub_total;
    });

    __write_number($("#final_total"), total);
    $(".final_total_span").text(__currency_trans_from_en(total, false));
}

$(document).on("change", ".quantity, .sell_price", function () {
    calculate_sub_totals();
});
$(document).on("click", ".remove_row", function () {
    $(this).closest("tr").remove();
    calculate_sub_totals();
});
$(document).on("click", ".minus", function () {
    let tr = $(this).closest("tr");
    let qty = parseFloat($(tr).find(".quantity").val());

    let new_qty = qty - 1;
    if (new_qty < 0.1) {
        return;
    }

    $(tr).find(".quantity").val(new_qty).change();
});
$(document).on("click", ".plus", function () {
    let tr = $(this).closest("tr");
    let qty = parseFloat($(tr).find(".quantity").val());
    let new_qty = qty + 1;
    if (new_qty < 0.1) {
        return;
    }
    $(tr).find(".quantity").val(new_qty).change();
});

$(document).on("submit", "form#quick_add_customer_form", function (e) {
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
                var customer_id = result.customer_id;
                $.ajax({
                    method: "get",
                    url: "/customer/get-dropdown",
                    data: {},
                    contactType: "html",
                    success: function (data_html) {
                        $("#customer_id").empty().append(data_html);
                        $("#customer_id").selectpicker("refresh");
                        $("#customer_id").selectpicker("val", customer_id);
                    },
                });
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});

$(document).on("click", ".quick_add_purchase_order", function () {
    let tr = $(this).closest("tr");
    let href = $(this).data("href");

    $.ajax({
        method: "get",
        url: href,
        data: {store_id: $('#store_id').val()},
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(tr).find('.quick_add_purchase_order').remove();
            }else{
                swal("Error", result.msg, "error");
            }
            console.log(result);
        },
    });
});
