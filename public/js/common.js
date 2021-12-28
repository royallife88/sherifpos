$(document).on("submit", "form", function () {
    $(this).validate();
});

$(".time_picker").timepicker({
    step: 15,
});

__currency_decimal_separator = $("input#__decimal").val();
__currency_precision = $("input#__currency_precision").val();
__currency_symbol = $("input#__currency_symbol ").val();
__currency_thousand_separator = $("input#__currency_thousand_separator").val();
__currency_symbol_placement = $("input#__currency_symbol_placement").val();
__precision = $("input#__precision").val();
__quantity_precision = $("input#__quantity_precision").val();

function __currency_trans_from_en(
    input,
    show_symbol = true,
    use_page_currency = false,
    precision = __currency_precision,
    is_quantity = false
) {
    if (use_page_currency && __p_currency_symbol) {
        var s = __p_currency_symbol;
        var thousand = __p_currency_thousand_separator;
        var decimal = __p_currency_decimal_separator;
    } else {
        var s = __currency_symbol;
        var thousand = __currency_thousand_separator;
        var decimal = __currency_decimal_separator;
    }
    symbol = "";
    var format = "%s%v";
    if (show_symbol) {
        symbol = s;
        format = "%s %v";
        if (__currency_symbol_placement == "after") {
            format = "%v %s";
        }
    }
    if (is_quantity) {
        precision = __quantity_precision;
    }
    return accounting.formatMoney(
        input,
        symbol,
        precision,
        thousand,
        decimal,
        format
    );
}
function __currency_convert_recursively(element, use_page_currency = false) {
    element.find(".display_currency").each(function () {
        var value = $(this).text();
        var show_symbol = $(this).data("currency_symbol");
        if (show_symbol == undefined || show_symbol != true) {
            show_symbol = false;
        }
        var highlight = $(this).data("highlight");
        if (highlight == true) {
            __highlight(value, $(this));
        }
        var is_quantity = $(this).data("is_quantity");
        if (is_quantity == undefined || is_quantity != true) {
            is_quantity = false;
        }
        if (is_quantity) {
            show_symbol = false;
        }
        $(this).text(
            __currency_trans_from_en(
                value,
                show_symbol,
                use_page_currency,
                __currency_precision,
                is_quantity
            )
        );
    });
}
function __add_percent(amount, percentage = 0) {
    var amount = parseFloat(amount);
    var percentage = isNaN(percentage) ? 0 : parseFloat(percentage);
    return amount + (percentage / 100) * amount;
}
function __substract_percent(amount, percentage = 0) {
    var amount = parseFloat(amount);
    var percentage = isNaN(percentage) ? 0 : parseFloat(percentage);
    return amount - (percentage / 100) * amount;
}
function __get_principle(amount, percentage = 0, minus = false) {
    var amount = parseFloat(amount);
    var percentage = isNaN(percentage) ? 0 : parseFloat(percentage);
    if (minus) {
        return (100 * amount) / (100 - percentage);
    } else {
        return (100 * amount) / (100 + percentage);
    }
}
function __get_percent_value(amount, percentage = 0) {
    var percentage = isNaN(percentage) ? 0 : parseFloat(percentage);
    return (percentage / 100) * amount;
}
function __number_uf(input, use_page_currency = false) {
    if (use_page_currency && __currency_decimal_separator) {
        var decimal = __p_currency_decimal_separator;
    } else {
        var decimal = __currency_decimal_separator;
    }
    return accounting.unformat(input, decimal);
}
function __number_f(
    input,
    show_symbol = false,
    use_page_currency = false,
    precision = __currency_precision
) {
    return __currency_trans_from_en(
        input,
        show_symbol,
        use_page_currency,
        precision
    );
}
function __read_number(input_element, use_page_currency = false) {
    return __number_uf(input_element.val(), use_page_currency);
}
function __write_number(
    input_element,
    value,
    use_page_currency = false,
    precision = __currency_precision
) {
    if (input_element.hasClass("input_quantity")) {
        precision = __quantity_precision;
    }
    input_element.val(__number_f(value, false, use_page_currency, precision));
}
function __write_number_without_decimal_format(
    input_element,
    value,
    use_page_currency = false,
    precision = __currency_precision
) {
    if (input_element.hasClass("input_quantity")) {
        precision = __quantity_precision;
    }
    input_element.val(value);
}

function __print_receipt(section_id = null) {
    setTimeout(function () {
        window.print();
        if ($("#edit_pos_form").length > 0) {
            setTimeout(() => {
                window.close();
            }, 1500);
        }
    }, 1000);
}
function incrementImageCounter() {
    img_counter++;
    if (img_counter === img_len) {
        window.print();
    }
}

$(".datepicker").datepicker();
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
var language = $("#__language").val();

if (language === undefined || language === null || language === "") {
    language = $.cookie("pos.language");
    window.location.replace(
        base_path + "/general/switch-language/" + $.cookie("pos.language")
    );
}
if ($.cookie("pos.language") !== language) {
    $.cookie("pos.language", language);
    window.location.replace(
        base_path + "/general/switch-language/" + $.cookie("pos.language")
    );
}
if (language == "en") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/en-gb.json";
} else if (language == "fr") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json";
} else if (language == "ar") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json";
} else if (language == "hi") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/hi.json";
} else if (language == "pr") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/fa.json";
} else if (language == "ur") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/ur.json";
} else if (language == "tr") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/tr.json";
} else if (language == "du") {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/nl_nl.json";
} else {
    dt_lang_url = "//cdn.datatables.net/plug-ins/1.11.3/i18n/en-gb.json";
}
var datatable_params = {
    lengthChange: true,
    paging: true,
    info: false,
    bAutoWidth: false,
    order: [],
    language: {
        url: dt_lang_url,
    },
    lengthMenu: [
        [10, 25, 50, 75, 100, 200, 500, -1],
        [10, 25, 50, 75, 100, 200, 500, "All"],
    ],
    dom: "lBfrtip",
    buttons: [
        {
            extend: "print",
            exportOptions: {
                columns: ":visible:not(.notexport)",
            },
        },
        {
            extend: "excel",
            exportOptions: {
                columns: ":visible:not(.notexport)",
            },
        },
        {
            extend: "csvHtml5",
            exportOptions: {
                columns: ":visible:not(.notexport)",
            },
        },
        {
            extend: "pdfHtml5",
            exportOptions: {
                columns: ":visible:not(.notexport)",
            },
        },
        {
            extend: "copyHtml5",
            exportOptions: {
                columns: ":visible:not(.notexport)",
            },
        },
        {
            extend: "colvis",
            columns: ":gt(0)",
        },
    ],
    footerCallback: function (row, data, start, end, display) {
        var intVal = function (i) {
            return typeof i === "string"
                ? i.replace(/[\$,]/g, "") * 1
                : typeof i === "number"
                ? i
                : 0;
        };

        this.api()
            .columns(".sum", { page: "current" })
            .every(function () {
                var column = this;
                if (column.data().count()) {
                    var sum = column.data().reduce(function (a, b) {
                        a = intVal(a);
                        if (isNaN(a)) {
                            a = 0;
                        }

                        b = intVal(b);
                        if (isNaN(b)) {
                            b = 0;
                        }

                        return a + b;
                    });
                    $(column.footer()).html(
                        __currency_trans_from_en(sum, false)
                    );
                }
            });
    },
};
var table = $(".dataTable").DataTable(datatable_params);
table.columns('.hidden').visible(false);
function sum_table_col(table, class_name) {
    var sum = 0;
    table
        .find("tbody")
        .find("tr")
        .each(function () {
            if (
                parseFloat(
                    $(this)
                        .find("." + class_name)
                        .data("orig-value")
                )
            ) {
                sum += parseFloat(
                    $(this)
                        .find("." + class_name)
                        .data("orig-value")
                );
            }
        });

    return sum;
}

$(document).ready(function () {
    $("#terms_and_condition_id").change();
});

$(document).on("change", "#terms_and_condition_id", function () {
    let terms_and_condition_id = $(this).val();

    if (terms_and_condition_id) {
        $.ajax({
            method: "get",
            url: "/terms-and-conditions/get-details/" + terms_and_condition_id,
            data: {},
            success: function (result) {
                $(".tac_description_div span").html(result.description);
            },
        });
    }
});
