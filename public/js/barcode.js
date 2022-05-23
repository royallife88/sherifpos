$(document).ready(function () {
    //Add products
    if ($("#search_product_for_label").length > 0) {
        $("#search_product_for_label")
            .autocomplete({
                source: "/product/get-products",
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

    $("input#is_show_price").change(function () {
        if ($(this).is(":checked")) {
            $("div#price_type_div").show();
        } else {
            $("div#price_type_div").hide();
        }
    });

    $("button#labels_preview").click(function () {
        if (
            $("form#preview_setting_form table#product_table tbody tr").length >
            0
        ) {
            $("#preview_setting_form").validate({
                rules: {
                    free_text: {
                        maxlength: 60,
                    },
                },
            });
            if ($("#preview_setting_form").valid()) {
                var url =
                    base_path +
                    "/barcode/print-barcode?" +
                    $("form#preview_setting_form").serialize();

                window.open(url, "newwindow");
            }
        } else {
            swal("No product selected.").then((value) => {
                $("#search_product_for_label").focus();
            });
        }
    });

    $(document).on("click", "button#print_label", function () {
        window.print();
    });
});

function get_label_product_row(product_id, variation_id) {
    if (product_id) {
        var row_count = parseInt($("#row_count").val());
        $("#row_count").val(row_count + 1);
        $.ajax({
            method: "GET",
            url: "/barcode/add-product-row",
            dataType: "html",
            data: {
                product_id: product_id,
                row_count: row_count,
                variation_id: variation_id,
            },
            success: function (result) {
                $("table#product_table tbody").append(result);
            },
        });
    }
}
