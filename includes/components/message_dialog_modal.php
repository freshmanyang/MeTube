<div class="modal fade" id="message_dialog_modal" tabindex="-1" role="dialog" aria-labelledby="paired_user_name"
     aria-hidden="true" data-backdrop="false" paired-user-id="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">123</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="message_modal_body"></div>
            <div class="modal-footer">
                <div class="text-area" contenteditable="true"></div>
                <button type="button" class="btn btn-purple" id="submit_message_button">Submit</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#message_dialog_modal .text-area').on('keydown paste', function (e) { //Prevent on paste as well
        let text = $(this).text();
        if (text.length === 200 && e.keyCode !== 8) {
            e.preventDefault();
        }
    });

    // empty dialog modal when close
    $('#message_dialog_modal').on('hidden.bs.modal', function () {
        $('#message_dialog_modal .modal-body').empty();
        $('#message_dialog_modal .text-area').empty();
    });

    // user send text to another user
    $('#submit_message_button').on("click", function () {
        if ($('#message_dialog_modal .text-area').text() === '') {
            return;
        }
        let text = $('#message_dialog_modal .text-area')[0].innerText.replace(/(?:\r\n|\r|\n)/g, '<br>');
        let sender_id = $(".header-renderer img").attr('user-id');
        // user in channel page, get receiver_id from channelPage container
        let receiver_id = $("#channelPage").attr('channel-user-id');
        if (!receiver_id) {
            // if user not in channel page, get receiver id form message dialog modal
            receiver_id = $('#message_dialog_modal .receive-message').find('img').attr('user-id');
        }
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {send_message: true, sender_id: sender_id, receiver_id: receiver_id, text: text},
            dataType: 'json',
            beforeSend: function () {
                $('#message_dialog_modal .text-area').empty();
            },
            success: function (res) {
                if (res.status) { // success
                    let body = $('#message_modal_body');
                    res.data.forEach(function (val, index) {
                        body.append(val);
                    });
                    body[0].scrollTop = body[0].scrollHeight;
                } else if (!res.status && !res.data) { // failed to insert message into database
                    $("#warning_message").text('Something went wrong. Operation failed.');
                    $("#alert_modal").modal('show');
                } else if (!res.status && res.data === 'blocked') {
                    $("#warning_message").text('Sorry, your message was rejected.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });

    let getNextMessagesInterval = '';

    // when message_dialog_modal is on, request next messages for each 2 seconds
    $('#message_dialog_modal').on('shown.bs.modal', function () {
        // user in channel page, get receiver_id from channelPage container
        let user_id = $(".header-renderer img").attr('user-id');
        let paired_user_id = $(this).attr('paired-user-id');

        // delete relative notifications
        $('[id=mail_notification_content]').each(function () {
            if ($(this).attr('sender-id') === paired_user_id) {
                $(this).remove();
            }
        });

        if($("#mail_notification_popup").children().length === 1){
            $("#mail_notification_popup .no-new-messages").show();
            $("#mail_notification_button i").removeClass("new-message");
        }else{
            $("#mail_notification_popup .no-new-messages").hide();
            $("#mail_notification_button i").addClass("new-message");
        }

        getNextMessagesInterval = setInterval(function () {
            getNextMessageFromPairedUser(paired_user_id, user_id);
        }, 2000);
    });

    // when message_dialog_modal is off, clear interval function
    $('#message_dialog_modal').on('hidden.bs.modal', function () {
        clearInterval(getNextMessagesInterval);
    });

    function getNextMessageFromPairedUser(paired_user_id, user_id) {
        let last_message_id = $('#message_dialog_modal').find('.modal-body').children(':last').attr('message-id');
        if (!last_message_id) {
            // no message show in the modal, this is a new dialog
            last_message_id = 0;
        }
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {
                request_next_messages: true,
                paired_user_id: paired_user_id,
                user_id: user_id,
                last_message_id: last_message_id
            },
            dataType: 'json',
            beforeSend: function () {
                clearInterval(getNextMessagesInterval);
            },
            success: function (res) {
                if (res.status) {
                    let body = $('#message_modal_body');
                    if (res.data.length !== 0) {
                        res.data.forEach(function (val, index) {
                            body.append(val);
                        });
                        body[0].scrollTop = body[0].scrollHeight;
                    }
                    getNextMessagesInterval = setInterval(function () {
                        getNextMessageFromPairedUser(paired_user_id, user_id);
                    }, 2000);
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    }
</script>