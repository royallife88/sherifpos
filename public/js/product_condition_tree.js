$(document).on("click", ".pci-accordion-toggle", function () {
    let id = $(this).data("id");
    if ($(".pci-angle-class-" + id).hasClass("fa-angle-right")) {
        $(".pci-angle-class-" + id).removeClass("fa-angle-right");
        $(".pci-angle-class-" + id).addClass("fa-angle-down");
    } else if ($(".pci-angle-class-" + id).hasClass("fa-angle-down")) {
        $(".pci-angle-class-" + id).removeClass("fa-angle-down");
        $(".pci-angle-class-" + id).addClass("fa-angle-right");
    }
});

$(document).on("change", ".pci-my-new-checkbox", function () {
    let parent_accordion = $(this).parent().parent();
    if ($(this).prop("checked") === true) {
        $(parent_accordion).find(".pci-my-new-checkbox").prop("checked", true);
    } else {
        $(parent_accordion).find(".pci-my-new-checkbox").prop("checked", false);
    }
});

$(document).ready(function () {
    $("#pct_modal_body .pci_product_checkbox").each(function () {
        if ($(this).prop("checked") === true) {
            pciToggleAccordianTillItem($(this));
        }
    });
});

$(document).on("change", "#search_pci", function () {
    let product_name = $("#search_pci option:selected").text();
    let product_element = $(
        '#pci_accordian_div a:contains("' + product_name + '")'
    );
    let related_checkbox = $(product_element)
        .parent()
        .parent()
        .find(".pci_product_checkbox");
    $(related_checkbox).prop("checked", true);
    $(this).val("");
    $(this).selectpicker("refresh");
    pciToggleAccordianTillItem($(related_checkbox));
});

function pciToggleAccordianTillItem(product) {
    let pci_class_level = $(product)
        .closest(".pci_class_level")
        .find(".pci-accordion-toggle")
        .data("id");
    let pci_category_level = $(product)
        .closest(".pci_category_level")
        .find(".pci-accordion-toggle")
        .data("id");
    let pci_sub_category_level = $(product)
        .closest(".pci_sub_category_level")
        .find(".pci-accordion-toggle")
        .data("id");
    let brand_level = $(product)
        .closest(".pci_brand_level")
        .find(".pci-accordion-toggle")
        .data("id");
    let top_accordion = $(product)
        .closest(".top_accordion")
        .find(".pci-accordion-toggle")
        .data("id");
    $("#pci-collapse" + pci_class_level).collapse("show");
    $("#pci-collapse" + pci_category_level).collapse("show");
    $("#pci-collapse" + pci_sub_category_level).collapse("show");
    $("#pci-collapse" + brand_level).collapse("show");
    $("#pci-collapse" + top_accordion).collapse("show");
}

$(document).on("click", ".remove_row_sp", function () {
    let product_id = parseInt($(this).data("product_id"));
    $(this).parents("tr").remove();
    $(".product_checkbox").each((i, obj) => {
        if ($(obj).prop("checked") === true) {
            if (parseInt($(obj).val()) === product_id) {
                let index = unique_product_array.indexOf(product_id);
                unique_product_array.splice(index, 1);
                $(obj).prop("checked", false);
            }
        }
    });
    calculate_total_prices();
});

$(document).ready(function () {
    calculate_total_prices();
});

$(document).on('change', '.qty', function() {
    calculate_total_prices();
});

function calculate_total_prices() {
    var total_purchase_price = 0;
    var total_sell_price = 0;
    $("#sale_promotion_table > tbody > tr").each((ele, tr) => {
        let purchase_price = __read_number($(tr).find(".purchase_price"));
        let sell_price = __read_number($(tr).find(".sell_price"));
        let qty = __read_number($(tr).find(".qty"));
        total_purchase_price += purchase_price * qty;
        total_sell_price += sell_price * qty;
    });
    $(".footer_purchase_price_total").text(
        __currency_trans_from_en(total_purchase_price, false)
    );
    $(".footer_sell_price_total").text(
        __currency_trans_from_en(total_sell_price, false)
    );
    $('#actual_sell_price').val(total_sell_price);
}
