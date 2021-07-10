if ($("form#edit_pos_form").length > 0) {
    pos_total_row();
    pos_form_obj = $("form#edit_pos_form");
} else {
    pos_form_obj = $("form#add_pos_form");
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

function calculate_sub_totals() {
    var grand_total = 0; //without any discounts
    var total = 0;
    var item_count = 0;
    $("#product_table > tbody  > tr").each((ele, tr) => {
        let quantity = __read_number($(tr).find(".quantity"));
        let sell_price = __read_number($(tr).find(".sell_price"));
        let sub_total = sell_price * quantity;

        grand_total += sub_total;
        $(".grand_total_span").text(
            __currency_trans_from_en(grand_total, false)
        );

        __write_number($(tr).find(".sub_total"), sub_total);
        $(tr)
            .find(".sub_total_span")
            .text(__currency_trans_from_en(sub_total, false));

        total += sub_total;

        item_count++;
    });
    $("#subtotal").text(__currency_trans_from_en(total, false));
    $("#item").text(item_count);

    let tax_amount = get_tax_amount(total);
    __write_number($("#total_tax"), tax_amount);
    total += tax_amount;

    __write_number($("#grand_total"), grand_total); // total without any discounts

    let discount_amount = get_discount_amount(total);
    total -= discount_amount;

    __write_number($("#final_total"), total);
    $("#final_total").change();

    $(".final_total_span").text(__currency_trans_from_en(total, false));
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
    __write_number($("#amount"), final_total);
    __write_number($("#paying_amount"), final_total);
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
});

pos_form_validator = pos_form_obj.validate({
    submitHandler: function (form) {
        $("#pos-save").attr("disabled", "true");
        var data = $(form).serialize();
        data = data + "&method=" + $("#method").val();
        var url = $(form).attr("action");
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function (result) {
                if (result.success == 1) {
                    $("#add-payment").modal("hide");
                    toastr.success(result.msg);

                    if (
                        $("#print_the_transaction").prop("checked") &&
                        $("#status").val() !== "draft"
                    ) {
                        pos_print(result.html_content);
                    }
                    reset_pos_form();
                    get_recent_transactions();
                } else {
                    toastr.error(result.msg);
                }

                $("div.pos-processing").hide();
                $("#pos-save").removeAttr("disabled");
            },
        });
    },
});

//Finalize without showing payment options
$("button#submit-btn").click(function () {
    //Check if product is present or not.
    if ($("table#product_table tbody").find(".product_row").length <= 0) {
        toastr.warning("No Product Added");
        return false;
    }

    $(this).attr("disabled", true);

    pos_form_obj.submit();
});

function pos_print(receipt) {
    $("#receipt_section").html(receipt);
    __currency_convert_recursively($("#receipt_section"));
    __print_receipt("receipt_section");
}

function reset_pos_form() {
    //If on edit page then redirect to Add POS page
    if ($("form#edit_pos_sell_form").length > 0) {
        setTimeout(function () {
            window.location = $("input#pos_redirect_url").val();
        }, 4000);
        return true;
    }
    if (pos_form_obj[0]) {
        pos_form_obj[0].reset();
    }
    $(
        "span#subtotal, span#item, span#discount, span#tax, span#delivery-cost, span.final_total_span"
    ).text(0);
    $(
        "#amount, #paying_amount, #discount_value, #final_total, #grand_total,  #gift_card_id, #total_tax, #coupon_id, #change, .delivery_address, .delivery_cost"
    ).val("");
    $("#status").val("final");
    $("button#submit-btn").attr("disabled", false);
    set_default_customer();
    $("#tax_id").val("");
    $("#tax_id").selectpicker("refresh");
    $("#deliveryman_id").val("");
    $("#deliveryman_id").selectpicker("refresh");
    $("tr.product_row").remove();
}

function confirmCancel() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    if (confirm("Are you sure want to reset?")) {
        window.location = $('#cancel-btn').data("href");
    }
    return false;
}

$(document).on("click", "#recent-transaction-btn", function () {
    $("#recentTransaction").modal("show");
    get_recent_transactions();
});

$(document).on(
    "change",
    "#rt_start_date, #rt_end_date, #rt_customer_id",
    function () {
        get_recent_transactions();
    }
);

//Get recent transactions
function get_recent_transactions() {
    let href = $("#recent-transaction-btn").data("href");

    $.ajax({
        method: "get",
        url:
            href +
            "?start_date=" +
            $("#rt_start_date").val() +
            "&end_date=" +
            $("#rt_end_date").val() +
            "&customer_id=" +
            $("#rt_customer_id").val(),
        data: {},
        success: function (result) {
            $(".recent_transaction_div").empty().append(result);
        },
    });
}

//Get recent transactions
function get_draft_transactions() {
    let href = $("#view-draft-btn").data("href");

    $.ajax({
        method: "get",
        url:
            href +
            "?start_date=" +
            $("#draft_start_date").val() +
            "&end_date=" +
            $("#draft_end_date").val(),
        data: {},
        success: function (result) {
            $(".draft_transaction_div").empty().append(result);
        },
    });
}

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
