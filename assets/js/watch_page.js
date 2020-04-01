$(function () {
    // click download button, insert record into download_list
    $('#download_btn').on("click", function () {
        let video_id = $('.video-player').attr('video-target');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {download: true, video_id: video_id},
            dataType: 'json',
            success: function (res) {
                // do nothing
            }
        });
    });

    // click like button, insert record into liked_list
    $('#like_btn').on("click", function () {
        let video_id = $('.video-player').attr('video-target');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {like: true, video_id: video_id},
            dataType: 'json',
            success: function (res) {
                console.log(res.data);
                if (res.status) {
                    // submit success
                    $('#like_btn, #like_btn i').toggleClass("clicked");
                    $('#like_count').text(res.data.likedCount);
                    $('#dislike_count').text(res.data.dislikedCount);
                    if ($('#dislike_btn, #dislike_btn i').hasClass("clicked")) {
                        $('#dislike_btn, #dislike_btn i').removeClass("clicked")
                    }
                } else if (!res.status && res.data !== "Not signIn") {
                    // submit success but failed insert record
                    $("#warning_message").text('Something went wrong. Operation failed.');
                    $("#alert_modal").modal('show');
                } else {
                    // user not signIn, open the signIn modal
                    $("#sign_in_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });

    // click dislike button, insert record into liked_list
    $('#dislike_btn').on("click", function () {
        let video_id = $('.video-player').attr('video-target');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {dislike: true, video_id: video_id},
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    // submit success
                    $('#dislike_btn, #dislike_btn i').toggleClass("clicked");
                    $('#like_count').text(res.data.likedCount);
                    $('#dislike_count').text(res.data.dislikedCount);
                    if ($('#like_btn, #like_btn i').hasClass("clicked")) {
                        $('#like_btn, #like_btn i').removeClass("clicked")
                    }
                } else if (!res.status && res.data !== "Not signIn") {
                    // submit success but failed insert record
                    $("#warning_message").text('Something went wrong. Operation failed.');
                    $("#alert_modal").modal('show');
                } else {
                    // user not signIn, open the signIn modal
                    $("#sign_in_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });

    // subscribe/unsubscribe
    $('#subscribe_btn').on("click", function () {
        let videoOwnerName = $('.video-owner-name').text();
        $(this).toggleClass('btn-danger');
        $(this).toggleClass('btn-secondary');
        let currentText = $(this).text();
        $(this).text(currentText === 'SUBSCRIBE' ? 'SUBSCRIBED' : 'SUBSCRIBE');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {subscribe: true, videoOwnerName: videoOwnerName},
            dataType: 'json',
            success: function (res) {
                if (!res.status) { // not success
                    if (res.data !== "Not signIn") { // server side error
                        $("#warning_message").text('Something went wrong. Operation failed.');
                        $("#alert_modal").modal('show');
                    } else {// user not signIn, open the signIn modal
                        $("#sign_in_modal").modal('show');
                    }
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });

    // bind event for show more/show less btn
    $('#show_more_btn').on("click",function () {
        let showMoreBtn = $('.video-secondary-info-renderer #show_more_btn');
        let showMoreBtnText = showMoreBtn.text();
        $('.video-secondary-info-renderer #dots').toggle();
        $('.video-secondary-info-renderer #more').toggle();
        showMoreBtn.text(showMoreBtnText === 'SHOW MORE' ? 'SHOW LESS' : 'SHOW MORE');
    })
});