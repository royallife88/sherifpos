$(document).on("change", "select.referred_by", function () {
    var referred_by = $(this).val();
    var referred_row = $(this).parents(".referred_row");
    var referred_type = $(referred_row).find("select.referred_type").val();
    var index = parseInt($(referred_row).find(".ref_row_index").val());
    $.ajax({
        method: "GET",
        url: "/customer/get-referred-by-details-html",
        data: { referred_by, referred_type, index },
        success: function (result) {
            $(referred_row).find(".referred_details").html(result);
            $(referred_row)
                .find(".referred_details")
                .find(".selectpicker")
                .selectpicker("refresh");
        },
    });
    console.log(referred_by);
});

$(document).on("change", "select.referred_type", function () {
    var referred_type = $(this).val();
    var referred_row = $(this).parents(".referred_row");
    if (referred_type == "customer") {
        $.ajax({
            method: "GET",
            url: "/customer/get-dropdown",
            data: {},
            success: function (result) {
                $(referred_row).find("select.referred_by").html(result);
                $(referred_row)
                    .find("select.referred_by")
                    .selectpicker("refresh");
            },
        });
    }
    if (referred_type == "supplier") {
        $.ajax({
            method: "GET",
            url: "/supplier/get-dropdown",
            data: {},
            success: function (result) {
                $(referred_row).find("select.referred_by").html(result);
                $(referred_row)
                    .find("select.referred_by")
                    .selectpicker("refresh");
            },
        });
    }
    if (referred_type == "employee") {
        $.ajax({
            method: "GET",
            url: "/hrm/employee/get-dropdown",
            data: {},
            success: function (result) {
                $(referred_row).find("select.referred_by").html(result);
                $(referred_row)
                    .find("select.referred_by")
                    .selectpicker("refresh");
            },
        });
    }
});

$(document).on("change", "select.reward_system", function () {
    let reward_system = $(this).val();
    let ref_details_row = $(this).parents(".ref_details_row");
    $(ref_details_row).find(".hidden_fields").addClass("hide");
    $(ref_details_row).find(".payment_status").attr("required", false);

    if (reward_system.includes("money")) {
        $(ref_details_row).find(".money_fields").removeClass("hide");
        $(ref_details_row).find(".payment_status").attr("required", true);
    }
    if (reward_system.includes("loyalty_point")) {
        $(ref_details_row).find(".loyalty_point_fields").removeClass("hide");
    }
    if (reward_system.includes("gift_card")) {
        $(ref_details_row).find(".gift_card_fields").removeClass("hide");
    }
    if (reward_system.includes("discount")) {
        $(ref_details_row).find(".discount_fields").removeClass("hide");
    }
});
var gift_card_referred_row = null;
$(document).on("click", "a.add-gift-card", function (e) {
    e.preventDefault();
    gift_card_referred_row = $(this).parents(".ref_details_row");
    $.ajax({
        url: $(this).data("href") + '?quick_add=true',
        dataType: "html",
        success: function (result) {
            $(".gift_card_modal").html(result).modal("show");
        },
    });
});

$(document).on("submit", "form#quick_add_gift_card_form", function (e) {
    e.preventDefault();
    $.ajax({
        method: "POST",
        url: "/gift-card",
        data: $(this).serialize(),
        success: function (result) {
            if (gift_card_referred_row) {
                $(gift_card_referred_row)
                    .find(".gift_card_id")
                    .val(result.gift_card_id);
                $(gift_card_referred_row)
                    .find(".gift_card_number")
                    .text(result.card_number);
                $(".gift_card_modal").modal("hide");
            }
        },
    });
});
$(document).on("click", ".add_referrals", function (e) {
    let ref_index = parseInt($("#ref_index").val());
    $("#ref_index").val(ref_index + 1);

    $.ajax({
        method: "GET",
        url: "/customer/get-referral-row",
        data: { index: ref_index },
        success: function (result) {
            $("#referral_div").append(result);
            $(".selectpicker").selectpicker("refresh");
        },
    });
});
