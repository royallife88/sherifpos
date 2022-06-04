$(document).on("click", ".footer_currency", function () {
    let currency_id = $(this).data("currency_id");

    $.each(currency_obj, function (key, value) {
        if (currency_id == value.currency_id) {
            converted_to_rate = value.conversion_rate;
        }
    });

    $(".currency_total_" + currency_id).each(function () {
        let this_ele = $(this);
        let conversion_rate = this_ele.data("conversion_rate");
        let total_base_value = parseFloat(this_ele.data("base_conversion"));
        $(this_ele)
            .siblings()
            .each(function () {
                total_base_value += parseFloat($(this).data("base_conversion"));
                $(this).text(__currency_trans_from_en(0, false));
            });
        let converted_value = total_base_value / conversion_rate;
        $(this_ele).text(__currency_trans_from_en(converted_value, false));
    });
});
$(document).on("click", ".dashboard_currency", function () {
    let currency_id = $(this).data("currency_id");

    $.each(currency_obj, function (key, value) {
        if (currency_id == value.currency_id) {
            converted_to_rate = value.conversion_rate;
        }
    });

    $(".currency_total_" + currency_id).each(function () {
        let this_ele = $(this);
        let conversion_rate = this_ele.data("conversion_rate");
        let total_base_value = parseFloat(this_ele.data("base_conversion"));
        $(this_ele)
            .siblings()
            .each(function () {
                total_base_value += parseFloat($(this).data("base_conversion"));
                $(this).find('.total').text(__currency_trans_from_en(0, false));
            });
        let converted_value = total_base_value / conversion_rate;
        $(this_ele).find('.total').text(__currency_trans_from_en(converted_value, false));
    });
});

$(document).on("click", ".table_totals", function () {
    $(".currency_total").each(function () {
        let orig_value = $(this).data("orig_value");
        $(this).text(__currency_trans_from_en(orig_value, false));
    });
});

$(document).on("click", ".currency_total_th", function () {
    let currency_id = $(this).data("currency_id");
    let th = $(this).parent("th");

    $.each(currency_obj, function (key, value) {
        if (currency_id == value.currency_id) {
            converted_to_rate = value.conversion_rate;
        }
    });

    let this_ele = $(th).find(".currency_total_" + currency_id);
    let conversion_rate = this_ele.data("conversion_rate");
    let total_base_value = parseFloat(this_ele.data("base_conversion"));

    $(this_ele)
        .siblings()
        .each(function () {
            total_base_value += parseFloat($(this).data("base_conversion"));
            $(this).find(".total").text(__currency_trans_from_en(0, false));
        });
    let converted_value = total_base_value / conversion_rate;
    $(this_ele)
        .find(".total")
        .text(__currency_trans_from_en(converted_value, false));
});

$(document).on("click", ".currency_total_row_td", function () {
    let currency_id = $(this).data("currency_id");
    let td = $(this).parent("td");

    $.each(currency_obj, function (key, value) {
        if (currency_id == value.currency_id) {
            converted_to_rate = value.conversion_rate;
        }
    });

    let this_ele = $(td).find(".currency_total_" + currency_id);
    let conversion_rate = this_ele.data("conversion_rate");
    let total_base_value = parseFloat(this_ele.data("base_conversion"));
    $(td)
        .siblings()
        .find("h6")
        .each(function () {
            total_base_value += parseFloat($(this).data("base_conversion"));
            $(this).find(".total").text(__currency_trans_from_en(0, false));
        });
    let converted_value = total_base_value / conversion_rate;
    $(this_ele)
        .find(".total")
        .text(__currency_trans_from_en(converted_value, false));
});
