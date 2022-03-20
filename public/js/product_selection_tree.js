$(document).on("click", ".accordion-toggle", function () {
    let id = $(this).data("id");
    if ($(".angle-class-" + id).hasClass("fa-angle-right")) {
        $(".angle-class-" + id).removeClass("fa-angle-right");
        $(".angle-class-" + id).addClass("fa-angle-down");
    } else if ($(".angle-class-" + id).hasClass("fa-angle-down")) {
        $(".angle-class-" + id).removeClass("fa-angle-down");
        $(".angle-class-" + id).addClass("fa-angle-right");
    }
});

$(document).on("change", ".my-new-checkbox", function () {
    let parent_accordion = $(this).parent().parent();
    if ($(this).prop("checked") === true) {
        $(parent_accordion).find(".my-new-checkbox").prop("checked", true);
    } else {
        $(parent_accordion).find(".my-new-checkbox").prop("checked", false);
    }
});

$(document).ready(function () {
    $("#pct_modal_body .product_checkbox").each(function () {
        if ($(this).prop("checked") === true) {
            toggleAccordianTillItem($(this));
        }
    });
});
var doc_ready = 0;
var is_edit_page = $("#is_edit_page").val();
$(document).ready(function () {
    if (is_edit_page != "1") {
        $("#search_pct").val("");
        $("#search_pct").selectpicker("refresh");
        $(".product_checkbox").prop("checked", false);
    }
    doc_ready = 1;
});
$(document).on(
    "changed.bs.select",
    "select#search_pct",
    function (e, clickedIndex, isSelected, oldValue) {
        if (doc_ready === 1 && is_edit_page != "1") {
            let selectedOptionValue = $(this).val();
            $(".product_checkbox").prop("checked", false);

            for (var i = 0; i < selectedOptionValue.length; i++) {
                var val = selectedOptionValue[i];

                let product_name = $(
                    "#search_pct option[value='" + val + "']"
                ).text();

                let product_element = $(
                    '#accordian_div a:contains("' + product_name + '")'
                );
                let related_checkbox = $(product_element)
                    .parent()
                    .parent()
                    .find(".product_checkbox");
                $(related_checkbox).prop("checked", true);

                toggleAccordianTillItem($(related_checkbox));
            }
        }
    }
);

function toggleAccordianTillItem(product) {
    let class_level = $(product)
        .closest(".class_level")
        .find(".accordion-toggle")
        .data("id");
    let category_level = $(product)
        .closest(".category_level")
        .find(".accordion-toggle")
        .data("id");
    let sub_category_level = $(product)
        .closest(".sub_category_level")
        .find(".accordion-toggle")
        .data("id");
    let brand_level = $(product)
        .closest(".brand_level")
        .find(".accordion-toggle")
        .data("id");
    let top_accordion = $(product)
        .closest(".top_accordion")
        .find(".accordion-toggle")
        .data("id");
    $("#collapse" + class_level).collapse("show");
    $("#collapse" + category_level).collapse("show");
    $("#collapse" + sub_category_level).collapse("show");
    $("#collapse" + brand_level).collapse("show");
    $("#collapse" + top_accordion).collapse("show");
}

let product_array = [];
let unique_product_array = [];
$(document).on("hidden.bs.modal", "#pctModal", function () {
    $("#sale_promotion_table tbody").empty();
    product_array = [];
    unique_product_array = [];

    $(".product_checkbox").each((i, obj) => {
        if ($(obj).prop("checked") === true) {
            product_array.push($(obj).val());
        }
    });
    unique_product_array = product_array.filter(onlyUnique);
    getProductRows(unique_product_array);
});

if (is_edit_page == "1") {
    $("#sale_promotion_table tbody").empty();
    product_array = [];
    unique_product_array = [];

    $(".product_checkbox").each((i, obj) => {
        if ($(obj).prop("checked") === true) {
            product_array.push($(obj).val());
        }
    });
    unique_product_array = product_array.filter(onlyUnique);
    getProductRows(unique_product_array);
}

function getProductRows(array) {
    $(".footer_sell_price_total").text(__currency_trans_from_en(0, false));
    $(".footer_purchase_price_total").text(__currency_trans_from_en(0, false));
    $.ajax({
        async: false,
        method: "get",
        url: "/sales-promotion/get-product-details-rows",
        data: {
            store_ids: $("#store_ids").val(),
            type: $("#type").val(),
            array: array,
        },
        dataType: "html",
        success: function (result) {
            $("#sale_promotion_table tbody").append(result);
            let sell_price_total = 0;
            let purchase_price_total = 0;
            if ($("#sell_price_total").length > 0) {
                sell_price_total = $("#sell_price_total").val();
            }
            if ($("#purchase_price_total").length > 0) {
                purchase_price_total = $("#purchase_price_total").val();
            }

            $(".footer_sell_price_total").text(
                __currency_trans_from_en(sell_price_total, false)
            );
            $(".footer_purchase_price_total").text(
                __currency_trans_from_en(purchase_price_total, false)
            );
            calculate_total_prices();
        },
    });
}
function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

$(document).on("change", "#type", function () {
    if ($(this).val() === "package_promotion") {
        $(".product_condition_div").addClass("hide");
        $(".qty_hide").removeClass("hide");
    } else {
        $(".product_condition_div").removeClass("hide");
        $(".qty_hide").addClass("hide");
    }
});
