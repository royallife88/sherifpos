if ($("form#edit_pos_form").length > 0) {
    pos_total_row();
    pos_form_obj = $("form#edit_pos_form");
} else {
    pos_form_obj = $("form#sell_return_form");
}
$(document).ready(function () {});

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
$(document).on("change", "#discount_amount", function () {
    calculate_sub_totals();
});
function calculate_sub_totals() {
    var grand_total = 0; //without any discounts
    var total = 0;
    var item_count = 0;
    var product_discount_total = 0;
    var total_item_tax = 0;
    $("#product_table > tbody  > tr").each((ele, tr) => {
        let quantity = __read_number($(tr).find(".quantity"));
        let sell_price = __read_number($(tr).find(".sell_price"));
        let price_hidden = __read_number($(tr).find(".price_hidden"));
        let item_tax = __read_number($(tr).find(".item_tax"));
        let tax_method = $(tr).find(".tax_method").val();
        let sub_total = 0;

        if (quantity > 0) {
            if (sell_price > price_hidden) {
                let price_discount = (sell_price - price_hidden) * quantity;
                $(tr).find(".product_discount_type").val("surplus");
                __write_number(
                    $(tr).find(".product_discount_value"),
                    price_discount
                );
                __write_number(
                    $(tr).find(".product_discount_amount"),
                    price_discount
                );
                sub_total = sell_price * quantity;
            } else if (sell_price < price_hidden) {
                let price_discount = (price_hidden - sell_price) * quantity;
                $(tr).find(".product_discount_type").val("fixed");
                __write_number(
                    $(tr).find(".product_discount_value"),
                    price_discount
                );
                __write_number(
                    $(tr).find(".product_discount_amount"),
                    price_discount
                );
                sub_total = price_hidden * quantity;
            } else {
                sub_total = price_hidden * quantity;
            }

            __write_number($(tr).find(".sub_total"), sub_total);
            let product_discount = calculate_product_discount(tr);
            product_discount_total += product_discount;
            sub_total -= product_discount;
        }

        grand_total += sub_total;
        $(".grand_total_span").text(
            __currency_trans_from_en(grand_total, false)
        );

        __write_number($(tr).find(".sub_total"), sub_total);
        $(tr)
            .find(".sub_total_span")
            .text(__currency_trans_from_en(sub_total, false));

        total += sub_total;
        if (quantity > 0) {
            item_tax = item_tax / quantity;
            if (tax_method === "exclusive") {
                total_item_tax += item_tax;
            }
        }

        item_count++;
    });
    $("#subtotal").text(__currency_trans_from_en(total, false));
    $("#item").text(item_count);

    let tax_amount = __read_number($("#total_tax"));
    let tax_type = $("#tax_type").val();
    let tax_method = $("#tax_method").val();

    if (tax_method == "inclusive") {
        tax_amount = 0;
    }
    __write_number($("#total_tax"), tax_amount);
    total += tax_amount;

    __write_number($("#grand_total"), grand_total); // total without any discounts

    let discount_amount = __read_number($("#discount_amount"));
    total -= discount_amount;

    let delivery_cost = __read_number($("#delivery_cost"));
    total += delivery_cost;
    total += total_item_tax;
    __write_number($("#final_total"), total);
    $("#final_total").change();

    $(".final_total_span").text(__currency_trans_from_en(total, false));
}
function calculate_product_discount(tr) {
    let discount = 0;

    let value = __read_number($(tr).find(".product_discount_value"));
    let type = $(tr).find(".product_discount_type").val();
    let sub_total = __read_number($(tr).find(".sub_total"));
    if (type == "fixed" || type == "surplus") {
        discount = value;
    }
    if (type == "percentage") {
        discount = __get_percent_value(sub_total, value);
    }
    __write_number($(tr).find(".product_discount_amount"), discount);
    if (type == "surplus") {
        discount = 0;
    }
    return discount;
}
$("#discount_btn").click(function () {
    calculate_sub_totals();
});

$("#tax_btn").click(function () {
    calculate_sub_totals();
});

function get_tax_amount(total) {
    let tax_rate = parseFloat($("#tax_id").find(":selected").data("rate"));
    let tax_amount = 0;
    if (!isNaN(tax_rate)) {
        tax_amount = __get_percent_value(total, tax_rate);
    }

    $("#tax").text(__currency_trans_from_en(tax_amount, false));
    __write_number($("#total_tax"), tax_amount);

    return tax_amount;
}
function get_discount_amount(total) {
    let discount_type = $("#discount_type").val();
    let discount_value = __read_number($("#discount_value"));

    let discount_amount = 0;
    if (discount_value) {
        if (discount_type === "fixed") {
            discount_amount = discount_value;
        }
        if (discount_type === "percentage") {
            discount_amount = __get_percent_value(total, discount_value);
        }
    }

    $("#discount").text(__currency_trans_from_en(discount_amount, false));
    __write_number($("#discount_amount"), discount_amount);
    return discount_amount;
}

$(document).on(
    "change",
    "#discount_value, #discount_type, #tax_id",
    function () {
        calculate_sub_totals();
    }
);

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

$(document).on("change", "#final_total", function (e) {
    let final_total = __read_number($("#final_total"));
    let gift_card_id = parseInt($("#gift_card_id").val());

    if (gift_card_id > 0 && final_total > 0) {
        let gift_card_amount = __read_number($("#gift_card_amount"));
        final_total = final_total - gift_card_amount;
    }
    __write_number($("#amount"), final_total);
    $("#amount").change();
    __write_number($("#paying_amount"), final_total);
});
$(document).on("change", "#amount", function (e) {
    let amount = __read_number($("#amount"));
    let total_amount_paid = __read_number($("#total_amount_paid"));

    if (amount > total_amount_paid) {
        swal(
            "warning",
            LANG.amount_exceeds_total_paid + " :" + total_amount_paid,
            "warning"
        );
        __write_number($("#amount"), total_amount_paid);
    }
});

$(document).on("click", ".payment-btn", function (e) {
    var audio = $("#mysoundclip2")[0];
    audio.play();

    let method = $(this).data("method");

    $("#method").val(method);
    $("#method").selectpicker("refresh");
    $("#method").change();

    if (method === "cheque") {
        $(".cheque_field").removeClass("hide");
    } else {
        $(".cheque_field").addClass("hide");
    }
    if (method === "card") {
        $(".card_field").removeClass("hide");
    } else {
        $(".card_field").addClass("hide");
    }
    if (method === "gift_card") {
        $(".gift_card_field").removeClass("hide");
    } else {
        $(".gift_card_field").addClass("hide");
    }
    if (method === "cash") {
        $(".qc").removeClass("hide");
    } else {
        $(".qc").addClass("hide");
    }
    $("#status").val("final");
});

$(document).on("click", ".qc-btn", function (e) {
    if ($(this).data("amount")) {
        if ($(".qc").data("initial")) {
            $('input[name="amount"]').val($(this).data("amount").toFixed(2));
            $(".qc").data("initial", 0);
        } else {
            $('input[name="amount"]').val(
                (
                    parseFloat($('input[name="amount"]').val()) +
                    $(this).data("amount")
                ).toFixed(2)
            );
        }
    } else {
        $('input[name="amount"]').val("0.00");
    }
    $('input[name="amount"]').change();
    $('input[name="paying_amount"]').change();
});

$(document).on("change", "#amount", function () {
    let amount = __read_number($("#amount"));
    let paying_amount = __read_number($("#paying_amount"));

    let change = paying_amount - amount;
    $("#change").text(__currency_trans_from_en(change, false));

    if (amount > 0) {
        $("#paid_on").attr("required", true);
    }
});

$(document).on("click", ".save-btn", function () {
    $("#sell_return_form").validate();
    if ($("#sell_return_form").valid()) {
        $("#sell_return_form").submit();
    }
});

$(document).on("change", "#customer_id", function () {
    let customer_id = $(this).val();
    $.ajax({
        method: "get",
        url:
            "/customer/get-details-by-transaction-type/" +
            customer_id +
            "/sell",
        data: {},
        success: function (result) {
            $(".customer_name").text(result.name);
            $(".customer_address").text(result.address);
            $(".delivery_address").text(result.address);
            $(".customer_due").text(
                __currency_trans_from_en(result.due, false)
            );
        },
    });
});
$("#customer_id").change();

$(document).on("change", "#tax_id", function () {
    $("#tax_id_hidden").val($(this).val());
});
$(document).on("change", "#deliveryman_id", function () {
    $("#deliveryman_id_hidden").val($(this).val());
});
