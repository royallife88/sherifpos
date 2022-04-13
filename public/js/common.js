$(document).on("submit", "form", function () {
    $(this).validate();
});
__language = $("input#__language").val();
__currency_decimal_separator = $("input#__decimal").val();
__currency_precision = $("input#__currency_precision").val();
__currency_symbol = $("input#__currency_symbol ").val();
__currency_thousand_separator = $("input#__currency_thousand_separator").val();
__currency_symbol_placement = $("input#__currency_symbol_placement").val();
__precision = $("input#__precision").val();
__quantity_precision = $("input#__quantity_precision").val();

$(document).ready(function () {
    $(".time_picker").datetimepicker({
        format: moment_time_format,
        icons: {
            up: "fa fa-angle-up",
            down: "fa fa-angle-down",
            previous: "fa fa-angle-left",
            next: "fa fa-angle-right",
        },
    });

    $("input[name='start_date']").datepicker({
        language: __language,
        format: "yyyy-mm-dd",
        todayHighlight: true,
    });
    $("input[name='end_date']").datepicker({
        language: __language,
        format: "yyyy-mm-dd",
        todayHighlight: true,
    });
    $("input[name='start_date']").attr("autocomplete", "off");
    $("input[name='end_date']").attr("autocomplete", "off");

    $(".datepicker").datepicker({
        language: __language,
        todayHighlight: true,
    });
});

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
    dt_lang_url = base_path + "/js/datatables_lang/en.json";
} else if (language == "fr") {
    dt_lang_url = base_path + "/js/datatables_lang/fr.json";
} else if (language == "ar") {
    dt_lang_url = base_path + "/js/datatables_lang/ar.json";
} else if (language == "hi") {
    dt_lang_url = base_path + "/js/datatables_lang/hi.json";
} else if (language == "fa") {
    dt_lang_url = base_path + "/js/datatables_lang/fa.json";
} else if (language == "ur") {
    dt_lang_url = base_path + "/js/datatables_lang/ur.json";
} else if (language == "tr") {
    dt_lang_url = base_path + "/js/datatables_lang/tr.json";
} else if (language == "nl") {
    dt_lang_url = base_path + "/js/datatables_lang/nl.json";
} else {
    dt_lang_url = base_path + "/js/datatables_lang/en.json";
}
var buttons = [
    {
        extend: "print",
        footer: true,
        exportOptions: {
            columns: ":visible:not(.notexport)",
        },
    },
    {
        extend: "excel",
        footer: true,
        exportOptions: {
            columns: ":visible:not(.notexport)",
        },
    },
    {
        extend: "csvHtml5",
        footer: true,
        exportOptions: {
            columns: ":visible:not(.notexport)",
        },
    },
    {
        extend: "pdfHtml5",
        footer: true,
        exportOptions: {
            columns: ":visible:not(.notexport)",
        },
    },
    {
        extend: "copyHtml5",
        footer: true,
        exportOptions: {
            columns: ":visible:not(.notexport)",
        },
    },
    {
        extend: "colvis",
        columns: ":gt(0)",
    },
];
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

    columnDefs: [
        {
            targets: "date",
            type: "date-eu",
        },
    ],
    initComplete: function () {
        $(this.api().table().container())
            .find("input")
            .parent()
            .wrap("<form>")
            .parent()
            .attr("autocomplete", "off");
    },
    dom: "lBfrtip",
    stateSave: true,
    buttons: buttons,
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
table.columns(".hidden").visible(false);
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
    $("#terms_and_condition_hidden").val($(this).val());
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
$(document).on("click", ".print-btn", function () {
    $(".modal").modal("hide");
    $.ajax({
        method: "get",
        url: $(this).data("href"),
        data: {},
        success: function (result) {
            if (result.success) {
                print_section(result.html_content);
            }
        },
    });
});
function print_section(receipt) {
    $("#print_section").html(receipt);
    $("#print_section").printThis({
        importCSS: true,
        importStyle: true,
        loadCSS: "vendor/bootstrap/css/bootstrap.min.css",
    });
}

$(document).on("click", ".close-btn", function () {
    $(".modal").modal("hide");
});

$(document).on("click", "table.ajax_view tbody tr", function (e) {
    if (
        !$(e.target).is("td.selectable_td input[type=checkbox]") &&
        !$(e.target).is("td.selectable_td") &&
        !$(e.target).is("td.clickable_td") &&
        !$(e.target).is("a") &&
        !$(e.target).is("button") &&
        !$(e.target).hasClass("label") &&
        !$(e.target).is("li") &&
        $(this).data("href") &&
        !$(e.target).is("i")
    ) {
        $.ajax({
            url: $(this).data("href"),
            dataType: "html",
            success: function (result) {
                $(".view_modal").html(result).modal("show");
            },
        });
    }
});
$(document).on("change", "#sku", function () {
    let sku = $(this).val();

    $.ajax({
        method: "get",
        url: "/product/check-sku/" + sku,
        data: {},
        success: function (result) {
            if (!result.success) {
                swal("Error", result.msg, "error");
            }
        },
    });
});
$(document).on("click", ".btn", function () {
    let data_dismiss = $(this).data("dismiss");
    if (data_dismiss == "modal") {
        $(".modal").modal("hide");
    }
});
$(document).on("shown.bs.modal", function () {
    $(".modal-dialog").draggable({
        handle: ".modal-header, .modal-filter, .modal-footer",
    });
    $(".modal-dialog").resizable({
        // alsoResize: ".modal-dialog",
        // minWidth: 625,
        // minHeight: 175,
        handles: "n, e, s, w, ne, sw, se, nw",
    });
});

$(document).on("click", ".translation_btn", function () {
    let type = $(this).data("type");
    if ($("#translation_table_" + type).hasClass("hide")) {
        $("#translation_table_" + type).removeClass("hide");
    } else {
        $("#translation_table_" + type).addClass("hide");
    }
});
$(document).on("click", ".translation_textarea_btn", function () {
    if ($("#translation_textarea_table").hasClass("hide")) {
        $("#translation_textarea_table").removeClass("hide");
    } else {
        $("#translation_textarea_table").addClass("hide");
    }
});
