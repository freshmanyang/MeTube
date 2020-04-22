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

function updateNotifications(user_id) {
    $.ajax({
        type: 'post',
        url: './submit.php',
        data: {update_notifications: true, user_id: user_id},
        dataType: 'json',
        beforeSend: function () {
            // delete current notifications
            $('[id=mail_notification_content]').remove();
        },
        success: function (res) {
            if (res.status) {
                let body = $('#mail_notification_popup');
                if (res.data.length !== 0) {
                    res.data.forEach(function (val, index) {
                        body.append(val);
                        body.children(':last').on("click", function (e) {
                            showMessage(e, $(this));
                        })
                    });
                    $("#mail_notification_popup .no-new-messages").hide();
                } else {
                    $("#mail_notification_popup .no-new-messages").show();
                }
            }
        },
        error: function (xhr, status, error) { // ajax error
            let errorMessage = xhr.status + ': ' + xhr.statusText;
            $("#warning_message").text(errorMessage);
            $("#alert_modal").modal('show');
        }
    });
}

// click mail notification button to show and hide notifications
$("#mail_notification_button").on("click", function (e) {
    e.stopPropagation();
    let user_id = $(".header-renderer img").attr('user-id');
    if (!user_id) {
        return;
    }
    updateNotifications(user_id);
    $("#mail_notification_popup").toggle();
    let popup = $("#popup");
    popup.hide();
});

// hide notifications when click blanks
$(document).on("click", function (e) {
    let mailNotification = $("#mail_notification_popup");
    if (!mailNotification.is(e.target) && mailNotification.has(e.target).length === 0) {
        mailNotification.hide();
    }
});

// click notification content to show message dialog modal
// $("[id = mail_notification_content]").on("click", function (e) {
//     showMessage(e, $(this));
// });

function showMessage(e, clickElement) {
    e.stopPropagation();
    let user_id = $(".header-renderer img").attr('user-id');
    let paired_user_id = clickElement.attr('sender-id');
    let messageDialogModal = $("#message_dialog_modal");
    let pairedUserName = clickElement.find('.sender-name').text().replace('@', '');
    clickElement.parent().hide();
    $.ajax({
        type: 'post',
        url: './submit.php',
        data: {request_messages: true, paired_user_id: paired_user_id, user_id: user_id},
        dataType: 'json',
        success: function (res) {
            if (res.status) {
                messageDialogModal.find('.modal-title').text(pairedUserName);
                let body = $('#message_modal_body');
                res.data.forEach(function (val, index) {
                    body.append(val);
                });
                messageDialogModal.attr('paired-user-id', paired_user_id);
                messageDialogModal.modal("show");
                body[0].scrollTop = body[0].scrollHeight;
            }
        },
        error: function (xhr, status, error) { // ajax error
            let errorMessage = xhr.status + ': ' + xhr.statusText;
            $("#warning_message").text(errorMessage);
            $("#alert_modal").modal('show');
        }
    });
}

// // click chat button in channel.php to show message dialog modal
$("#chat_button").on("click", function (e) {
    e.stopPropagation();
    let user_id = $(".header-renderer img").attr('user-id');
    let paired_user_id = $('#channelPage').attr('channel-user-id');
    let messageDialogModal = $("#message_dialog_modal");
    let searchParams = new URLSearchParams(window.location.search);
    let pairedUserName = searchParams.get('channel');
    $.ajax({
        type: 'post',
        url: './submit.php',
        data: {request_messages: true, paired_user_id: paired_user_id, user_id: user_id},
        dataType: 'json',
        success: function (res) {
            if (res.status) {
                messageDialogModal.find('.modal-title').text(pairedUserName);
                let body = $('#message_modal_body');
                res.data.forEach(function (val, index) {
                    body.append(val);
                });
                messageDialogModal.attr('paired-user-id', paired_user_id);
                messageDialogModal.modal("show");
                body[0].scrollTop = body[0].scrollHeight;
            }
        },
        error: function (xhr, status, error) { // ajax error
            let errorMessage = xhr.status + ': ' + xhr.statusText;
            $("#warning_message").text(errorMessage);
            $("#alert_modal").modal('show');
        }
    });
});

// click avatar to show and hide popup element
$(".avatar-button").on("click", function (e) {
    e.stopPropagation();
    $("#popup").toggle();
    let mailNotification = $("#mail_notification_popup");
    mailNotification.hide();
});

// hide popup element when click blanks
$(document).on("click", function (e) {
    let popup = $("#popup");
    if (!popup.is(e.target) && popup.has(e.target).length === 0) {
        popup.hide();
    }
});

// click profile button, show modal
$(".profile-btn").on("click", function () {
    let modalName = $(this).attr("target-modal");
    $(modalName).modal('show');
});

// reset modal when dismiss
$('.modal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
    $(this).find('form').removeClass('was-validated');
    $(this).find('.invalid-feedback').text('');
    $(this).find('.custom-file-label').text('');
});

// show sign in modal if user click button that needs to sign in
$('#to_profile,#to_contact_list, #to_upload, #to_channel, #upload_button, #mail_notification_button').on('click', function (e) {
    let user_id = $('.header-popup-wrapper img').attr('user-id');
    if (!user_id) {
        e.preventDefault();
        e.stopPropagation();
        $("#sign_in_modal").modal('show');
    }
});
