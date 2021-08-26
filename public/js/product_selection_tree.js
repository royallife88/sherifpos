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
    $("#pct_modal_body .my-new-checkbox").each(function () {
        if ($(this).prop("checked") === true) {
            $(this).siblings().find(".accordion-toggle").click();
            $(this).parent('.top_accordion').click();
        }
    });
});
