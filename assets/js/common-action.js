$(function () {
    // click menu button, show or hide nav bar
    const main = $("#main");
    const nav = $("#side_nav");
    $("#menu_button").on("click", function () {
        nav.toggle();
    });

    // change the search box border
    const outline_box = $("#search_box");
    $("#search_input").on({
        focus: function () {
            outline_box.addClass("has-focus");
        },
        blur: function () {
            if (outline_box.hasClass("has-focus")) {
                outline_box.removeClass("has-focus");
            }
        }
    });

    // change input file box text after choosing a file
    $(".custom-file-input").on("change", function () {
        let fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });


    // click avatar to show and hide popup element
    $(".avatar-button").on("click", function (e) {
        e.stopPropagation();
        $("#popup").toggle();
    });

    // hide popup element when click blanks
    $(document).on("click", function (e) {
        let popup = $("#popup");
        if (!popup.is(e.target) && popup.has(e.target).length === 0) {
            popup.hide();
        }
    });

    // click profile button, show modal
    $(".profile-btn").on("click",function () {
        let modalName = $(this).attr("target-modal");
        $(modalName).modal('show');
    });

    // reset modal when dismiss
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find('form').removeClass('was-validated');
        $(this).find('.invalid-feedback').text('');
        $(this).find('.custom-file-label').text('');
    })

});
