$(document).on("submit", "form", function () {
    $(this).validate();
});

__currency_decimal_separator = $('input#__decimal').val();
__currency_precision = $('input#__currency_precision').val();
__currency_symbol  = $('input#__currency_symbol ').val();
__currency_thousand_separator  = $('input#__currency_thousand_separator').val();
__currency_symbol_placement  = $('input#__currency_symbol_placement').val();
__precision  = $('input#__precision').val();
__quantity_precision  = $('input#__quantity_precision').val();


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
    symbol = '';
    var format = '%s%v';
    if (show_symbol) {
        symbol = s;
        format = '%s %v';
        if (__currency_symbol_placement == 'after') {
            format = '%v %s';
        }
    }
    if (is_quantity) {
        precision = __quantity_precision;
    }
    return accounting.formatMoney(input, symbol, precision, thousand, decimal, format);
}
function __currency_convert_recursively(element, use_page_currency = false) {
    element.find('.display_currency').each(function () {
        var value = $(this).text();
        var show_symbol = $(this).data('currency_symbol');
        if (show_symbol == undefined || show_symbol != true) {
            show_symbol = false;
        }
        var highlight = $(this).data('highlight');
        if (highlight == true) {
            __highlight(value, $(this));
        }
        var is_quantity = $(this).data('is_quantity');
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
    return __currency_trans_from_en(input, show_symbol, use_page_currency, precision);
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
    if (input_element.hasClass('input_quantity')) {
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
    if (input_element.hasClass('input_quantity')) {
        precision = __quantity_precision;
    }
    input_element.val(value);
}

$('.datepicker').datepicker();
