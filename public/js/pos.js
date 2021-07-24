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
        getFilterProductRightSide();
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

getFilterProductRightSide();
function getFilterProductRightSide() {
    var selling_filter = getFilterCheckboxValue("selling_filter");
    var price_filter = getFilterCheckboxValue("price_filter");
    var expiry_filter = getFilterCheckboxValue("expiry_filter");
    var sale_promo_filter = getFilterCheckboxValue("sale_promo_filter");
    var sorting_filter = getFilterCheckboxValue("sorting_filter");

    $.ajax({
        method: "get",
        url: "/pos/get-product-items-by-filter",
        data: {
            selling_filter,
            price_filter,
            expiry_filter,
            sale_promo_filter,
            sorting_filter,
        },
        contentType: "html",
        success: function (result) {
            $("#filter-product-table > tbody").hide()
            $("#filter-product-table > tbody").empty().append(result);
            $("#filter-product-table > tbody").show(500)
        },
    });
}

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
                focus: function (event, ui) {
                    if (ui.item.qty_available <= 0) {
                        return false;
                    }
                },
                select: function (event, ui) {
                    if (ui.item.qty_available > 0) {
                        $(this).val(null);
                        get_label_product_row(
                            ui.item.product_id,
                            ui.item.variation_id
                        );
                    } else {
                        alert("Out of Stock");
                    }
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
                $("input#search_product").focus().select();
            }
        });

    if (add_via_ajax) {
        var store_id = $("#store_id").val();
        var customer_id = $("#customer_id").val();
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
                customer_id: customer_id,
            },
            success: function (result) {
                $("table#product_table tbody").append(result);
                calculate_sub_totals();
            },
        });
    }
}
function calculate_sub_totals() {
    var grand_total = 0; //without any discounts
    var total = 0;
    var item_count = 0;
    $("#product_table > tbody  > tr").each((ele, tr) => {
        let quantity = __read_number($(tr).find(".quantity"));
        let sell_price = __read_number($(tr).find(".sell_price"));
        let sub_total = sell_price * quantity;

        __write_number($(tr).find(".sub_total"), sub_total);

        let product_discount = calculate_product_discount(tr);
        console.log(product_discount, "product_discount");
        sub_total -= product_discount;

        grand_total += sub_total;
        $(".grand_total_span").text(
            __currency_trans_from_en(grand_total, false)
        );

        let coupon_discount = calculate_coupon_discount(tr);
        sub_total -= coupon_discount;

        __write_number($(tr).find(".sub_total"), sub_total);
        $(tr)
            .find(".sub_total_span")
            .text(__currency_trans_from_en(sub_total, false));

        total += sub_total;

        item_count++;

        calculate_promotion_discount(tr);
    });
    $("#subtotal").text(__currency_trans_from_en(total, false));
    $("#item").text(item_count);

    let tax_amount = get_tax_amount(total);
    __write_number($("#total_tax"), tax_amount);
    total += tax_amount;

    __write_number($("#grand_total"), grand_total); // total without any discounts

    let discount_amount = get_discount_amount(total);
    total -= discount_amount;

    let points_redeemed_value = 0;
    if (parseInt($("#is_redeem_points").val())) {
        let customer_total_redeemable = __read_number(
            $("#customer_total_redeemable")
        );
        if (total >= customer_total_redeemable) {
            total -= customer_total_redeemable;
            points_redeemed_value = customer_total_redeemable;
        } else if (total < customer_total_redeemable) {
            total = 0;
            points_redeemed_value = total;
        }
        if (points_redeemed_value > 0) {
            let customer_points = __read_number($(".customer_points"));
            let customer_points_value = __read_number(
                $("#customer_points_value")
            );

            let one_point_value = customer_points / customer_points_value;
            let total_rp_redeemed = points_redeemed_value * one_point_value;
            $("#rp_redeemed").val(total_rp_redeemed);
            $("#rp_redeemed_value").val(points_redeemed_value);
        }
    }

    __write_number($("#final_total"), total);
    let promo_discount = apply_promotion_discounts();
    if (promo_discount > 0) {
        __write_number($("#discount_amount"), promo_discount);
    }
    total -= promo_discount;
    __write_number($("#final_total"), total);
    $("#final_total").change();

    $(".final_total_span").text(__currency_trans_from_en(total, false));
}

function calculate_product_discount(tr) {
    let discount = 0;

    let value = __read_number($(tr).find(".product_discount_value"));
    let type = $(tr).find(".product_discount_type").val();
    let sub_total = __read_number($(tr).find(".sub_total"));
    if (type == "fixed") {
        discount = value;
    }
    if (type == "percentage") {
        discount = __get_percent_value(sub_total, value);
    }

    $(tr).find(".product_discount_amount").val(discount);
    return discount;
}
function calculate_promotion_discount(tr) {
    let discount = 0;

    let value = __read_number($(tr).find(".promotion_discount_value"));
    let type = $(tr).find(".promotion_discount_type").val();
    let sub_total = __read_number($(tr).find(".sub_total"));
    if (type == "fixed") {
        discount = value;
    }
    if (type == "percentage") {
        discount = __get_percent_value(sub_total, value);
    }

    $(tr).find(".promotion_discount_amount").val(discount);
}

function apply_promotion_discounts() {
    let promo_discount = 0;
    let final_total = __read_number($("#final_total"));
    $("#product_table > tbody  > tr").each((ele, tr) => {
        let promotion_discount_amount = __read_number(
            $(tr).find(".promotion_discount_amount")
        );
        let promotion_purchase_condition = __read_number(
            $(tr).find(".promotion_purchase_condition")
        );
        let promotion_purchase_condition_amount = __read_number(
            $(tr).find(".promotion_purchase_condition_amount")
        );

        if (promotion_purchase_condition) {
            if (final_total > promotion_purchase_condition_amount) {
                promo_discount += promotion_discount_amount;
            }
        } else {
            promo_discount += promotion_discount_amount;
        }
    });
    return promo_discount;
}
function calculate_coupon_discount(tr) {
    let discount = 0;

    let value = __read_number($(tr).find(".coupon_discount_value"));
    let type = $(tr).find(".coupon_discount_type").val();
    let sub_total = __read_number($(tr).find(".sub_total"));
    if (type == "fixed") {
        discount = value;
    }
    if (type == "percentage") {
        discount = __get_percent_value(sub_total, value);
    }

    $(tr).find(".coupon_discount_amount").val(discount);

    return discount;
}
$(document).on("change", "#final_total", function (e) {
    let final_total = __read_number($("#final_total"));

    __write_number($("#final_total"), final_total);
    $(".final_total_span").text(__currency_trans_from_en(final_total, false));

    __write_number($("#amount"), final_total);
    __write_number($("#paying_amount"), final_total);
});

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
        data: { store_id: $("#store_id").val() },
        success: function (result) {
            if (result.success) {
                swal("Success", result.msg, "success");
                $(tr).find(".quick_add_purchase_order").remove();
            } else {
                swal("Error", result.msg, "error");
            }
        },
    });
});

$(document).on("click", "#draft-btn", function (e) {
    $("#status").val("draft");

    //Check if product is present or not.
    if ($("table#product_table tbody").find(".product_row").length <= 0) {
        toastr.warning("No Product Added");
        return false;
    }

    pos_form_obj.submit();
});
$(document).on("click", "#pay-later-btn", function (e) {
    //Check if product is present or not.
    if ($("table#product_table tbody").find(".product_row").length <= 0) {
        toastr.warning("No Product Added");
        return false;
    }
    $("#amount").val(0);
    pos_form_obj.submit();
});
$(document).on("click", ".payment-btn", function (e) {
    var audio = $("#mysoundclip2")[0];
    audio.play();

    let method = $(this).data("method");
    console.log(method);
    $("#method").val(method);
    $("#method").selectpicker("refresh");
    $("#method").change();

    if (method === "cheque") {
        $(".cheque_field").removeClass("hide");
    } else {
        $(".cheque_field").addClass("hide");
    }
    if (method === "bank_transfer") {
        $(".bank_field").removeClass("hide");
    } else {
        $(".bank_field").addClass("hide");
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

$(document).on("change", "#amount_to_be_used", function () {
    let amount_to_be_used = __read_number($("#amount_to_be_used"));
    let gift_card_current_balance = __read_number(
        $("#gift_card_current_balance")
    );

    let remaining_balance = gift_card_current_balance - amount_to_be_used;
    __write_number($("#remaining_balance"), remaining_balance);

    let final_total = __read_number($("#final_total"));

    let new_total = final_total - amount_to_be_used;
    __write_number($("#gift_card_final_total"), new_total);
    __write_number($("#final_total"), new_total);
});
$(document).on("change", "#gift_card_number", function () {
    let gift_card_number = $(this).val();

    $.ajax({
        method: "get",
        url: "/gift-card/get-details/" + gift_card_number,
        data: {},
        success: function (result) {
            if (!result.success) {
                $(".gift_card_error").text(result.msg);
            } else {
                let data = result.data;
                $("#gift_card_id").val(data.id);
                $(".gift_card_error").text("");
                $(".gift_card_current_balance").text(
                    __currency_trans_from_en(data.balance, false)
                );
                __write_number($("#gift_card_current_balance"), data.balance);
            }
        },
    });
});

var coupon_products = [];
var coupon_value = 0;
var coupon_type = null;
var amount_to_be_purchase = 0;
var amount_to_be_purchase_checkbox = 0;
$(document).on("click", ".coupon-check", function () {
    let coupon_code = $("#coupon-code").val();

    $.ajax({
        method: "get",
        url: "/coupon/get-details/" + coupon_code,
        data: {},
        success: function (result) {
            if (!result.success) {
                $(".coupon_error").text(result.msg);
            } else {
                $("#coupon_modal").modal("hide");
                let data = result.data;
                coupon_products = data.product_ids;
                coupon_value = data.amount;
                coupon_type = data.type;
                amount_to_be_purchase = data.amount_to_be_purchase;
                amount_to_be_purchase_checkbox =
                    data.amount_to_be_purchase_checkbox;
                $("#coupon_id").val(data.id);
                $(".coupon_error").text("");
                apply_coupon_to_products();
                calculate_sub_totals();
            }
        },
    });
});

function apply_coupon_to_products() {
    if (coupon_products.length) {
        $("#product_table > tbody  > tr").each((ele, tr) => {
            let product_id = $(tr).find(".product_id").val();
            if (amount_to_be_purchase_checkbox) {
                let grand_total = __read_number($("#grand_total"));
                if (grand_total >= amount_to_be_purchase) {
                    if (coupon_products.includes(product_id)) {
                        $(tr).find(".coupon_discount_value").val(coupon_value);
                        $(tr).find(".coupon_discount_type").val(coupon_type);
                    }
                }
            } else {
                $(tr).find(".coupon_discount_value").val(coupon_value);
                $(tr).find(".coupon_discount_type").val(coupon_type);
            }
        });
    }
}

pos_form_validator = pos_form_obj.validate({
    submitHandler: function (form) {
        $("#pos-save").attr("disabled", "true");
        var data = $(form).serialize();
        data =
            data +
            "&method=" +
            $("#method").val() +
            "&terms_and_condition_id=" +
            $("#terms_and_condition_id").val();
        var url = $(form).attr("action");
        $.ajax({
            method: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function (result) {
                if (result.success == 1) {
                    if ($("#is_quotation").val()) {
                        if ($("#submit_type").val() === "print") {
                            pos_print(result.html_content);
                        } else {
                            swal("success", result.msg, "Success");
                            location.reload();
                        }
                        return false;
                    }
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
    $("#add-payment").modal("hide");
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
        "span#subtotal, span#item, span#discount, span#tax, span#delivery-cost, span.final_total_span, span.customer_points_span, span.customer_points_value_span, span.customer_total_redeemable_span "
    ).text(0);
    $(
        "#amount, #paying_amount, #discount_value, #final_total, #grand_total,  #gift_card_id, #total_tax, #coupon_id, #change, .delivery_address, .delivery_cost, #customer_points_value, #customer_total_redeemable, #rp_redeemed, #rp_redeemed_value, #is_redeem_points "
    ).val("");
    $("#status").val("final");
    $("button#submit-btn").attr("disabled", false);
    $("button#redeem_btn").attr("disabled", false); //TODO:: not working on reset
    set_default_customer();
    $("#tax_id").val("");
    $("#tax_id").selectpicker("refresh");
    $("#deliveryman_id").val("");
    $("#deliveryman_id").selectpicker("refresh");
    $("tr.product_row").remove();
}

function set_default_customer() {
    var default_customer_id = $("#default_customer_id").val();

    $("select#customer_id").val(default_customer_id).trigger("change");
}

function confirmCancel() {
    var audio = $("#mysoundclip2")[0];
    audio.play();
    if (confirm("Are you sure want to reset?")) {
        reset_pos_form();
    }
    return false;
}

$(document).on("click", "td.filter_product_add", function () {
    let product_id = $(this).data("product_id");

    let variation_id = $(this).data("variation_id");
    get_label_product_row(product_id, variation_id);
});

$(document).on("click", "#view-draft-btn", function () {
    $("#draftTransaction").modal("show");
    get_draft_transactions();
});
$(document).on("change", "#draft_start_date, #draft_end_date", function () {
    get_draft_transactions();
});
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

            $(".customer_due_span").text(
                __currency_trans_from_en(result.due, false)
            );
            $(".customer_due").text(
                __currency_trans_from_en(result.due, false)
            );
        },
    });
    getCustomerPointDetails();
});

function getCustomerPointDetails() {
    let customer_id = $("#customer_id").val();
    var product_array = [];
    $("#product_table > tbody  > tr").each((i, tr) => {
        let product_id = __read_number($(tr).find(".product_id"));
        let sub_total = __read_number($(tr).find(".sub_total"));
        product_array[i] = { product_id: product_id, sub_total: sub_total };
    });

    $.ajax({
        method: "get",
        url: "/pos/get-customer-details/" + customer_id,
        data: { store_id: $("#store_id").val(), product_array: product_array },
        dataType: "json",
        success: function (result) {
            $(".customer_mobile_span").text(result.customer.mobile_number);
            $(".customer_name_span").text(result.customer.name);
            $(".customer_points_span").text(
                __currency_trans_from_en(result.customer.total_rp, false)
            );
            $(".customer_points").val(result.customer.total_rp);
            $(".customer_points_value_span").text(
                __currency_trans_from_en(result.rp_value, false)
            );
            $(".customer_points_value").val(result.rp_value);
            $(".customer_total_redeemable_span").text(
                __currency_trans_from_en(result.total_redeemable, false)
            );
            $(".customer_total_redeemable").val(result.total_redeemable);
            if (parseInt(result.total_redeemable) > 0) {
                $(".redeem_btn").attr("disabled", false);
            } else {
                $(".redeem_btn").attr("disabled", true);
                $("#is_redeem_points").val(0);
            }

            calculate_sub_totals();
        },
    });
}
$(document).on("click", ".redeem_btn", function () {
    $("#is_redeem_points").val(1);
    $(this).attr("disabled", true);
    $("#contact_details_modal").modal("hide");
    calculate_sub_totals();
});

$("#customer_id").change();

$(document).on("change", "#tax_id", function () {
    $("#tax_id_hidden").val($(this).val());
});
$(document).on("change", "#deliveryman_id", function () {
    $("#deliveryman_id_hidden").val($(this).val());
});

$(document).on("change", "#terms_and_condition_id", function () {
    console.log($(this).val());
});
