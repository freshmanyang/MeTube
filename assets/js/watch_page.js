$(function () {
    // click download button, insert record into download_list
    $('#download_btn').on("click", function () {
        let video_id = $('#video_player').attr('video-id');
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
        let video_id = $('#video_player').attr('video-id');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {like: true, video_id: video_id},
            dataType: 'json',
            success: function (res) {
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
        let video_id = $('#video_player').attr('video-id');
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
                    } else {
                        // if not sign in, reset the subscribe button style
                        $('#subscribe_btn').toggleClass('btn-danger');
                        $('#subscribe_btn').toggleClass('btn-secondary');
                        $('#subscribe_btn').text('SUBSCRIBE');
                        // open the signIn modal
                        $("#sign_in_modal").modal('show');
                    }
                } else { // if success, refresh the subscription count
                    let newText = res.data + ' subscribers';
                    $('.subscriber-count').text(newText);
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
    $('.video-secondary-info-renderer #show_more_btn').on("click", function () {
        let showMoreBtn = $('.video-secondary-info-renderer #show_more_btn');
        let showMoreBtnText = showMoreBtn.text();
        $('.video-secondary-info-renderer #dots').toggle();
        $('.video-secondary-info-renderer #more').toggle();
        showMoreBtn.text(showMoreBtnText === 'SHOW MORE' ? 'SHOW LESS' : 'SHOW MORE');
    });

    // comment editor on focus
    $('#comment_editor').on("focus", function () {
        let contentDefault = 'Add a public comment...';
        let content = $(this).text();
        if (content === contentDefault) {
            $(this).addClass('is-focus');
            $(this).text('');
        }
    });

    // comment editor on blur
    $('#comment_editor').on("blur", function () {
        let contentDefault = 'Add a public comment...';
        let content = $(this).text();
        if (!content) {
            $(this).removeClass('is-focus');
            $(this).text(contentDefault);
        }
    });

    // comment editor on input, enable comment button
    $('#comment_editor').on("input", function () {
        let content = $(this).text();
        let submitBtn = $('#submit_comment');
        if (content) {
            submitBtn.removeClass('btn-secondary');
            submitBtn.addClass('btn-primary');
            submitBtn.removeAttr('disabled');

        } else {
            submitBtn.removeClass('btn-primary');
            submitBtn.addClass('btn-secondary');
            submitBtn.attr('disabled', true);
        }
    });

    // click cancel btn to cancel comment
    $('#cancel_comment').on("click", function () {
        let contentDefault = 'Add a public comment...';
        let commentBox = $('#comment_editor');
        let submitBtn = $('#submit_comment');
        let content = commentBox.text();
        if (content) {
            commentBox.removeClass('is-focus');
            commentBox.text(contentDefault);
            submitBtn.removeClass('btn-primary');
            submitBtn.addClass('btn-secondary');
            submitBtn.attr('disabled', true);
        }
    });

    // click comment btn to submit a new comment
    $('#submit_comment').on("click", function () {
        let video_id = $('#video_player').attr('video-id');
        let user_id = $(this).parent().parent().prev().children().attr('user-id');
        let text = $('#comment_editor')[0].innerText.replace(/(?:\r\n|\r|\n)/g, '<br>');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {post_comment: true, video_id: video_id, user_id: user_id, text: text},
            dataType: 'json',
            success: function (res) {
                if (!res.status) { // not success
                    if (res.data !== "Not signIn") { // server side error
                        $("#warning_message").text('Something went wrong. Submit comment failed.');
                        $("#alert_modal").modal('show');
                    } else {
                        // open the signIn modal
                        $("#sign_in_modal").modal('show');
                    }
                } else { // success
                    // set the comment box and comment btn to default style
                    let contentDefault = 'Add a public comment...';
                    let commentBox = $('#comment_editor');
                    let submitBtn = $('#submit_comment');
                    let commentCount = $('.comment-count');
                    commentBox.removeClass('is-focus');
                    commentBox.text(contentDefault);
                    submitBtn.removeClass('btn-primary');
                    submitBtn.addClass('btn-secondary');
                    submitBtn.attr('disabled', true);
                    if (res.data.comment_count) {
                        commentCount.text(res.data.comment_count + ' Comments');
                    }
                    if (res.data.my_new_comment) {
                        $('.comments-wrapper').prepend(res.data.my_new_comment);
                        let newCommentBox = $('.comments-wrapper').children(":first");
                        newCommentBox.find("#show_more_btn").on("click", function () {
                            showMoreAndLessForCommentBox($(this));
                        });
                        newCommentBox.find("#reply_editor").on("focus", function () {
                            replyEditorOnFocus($(this));
                        });
                        newCommentBox.find("#reply_editor").on("blur", function () {
                            replyEditorOnBlur($(this));
                        });
                        newCommentBox.find("#reply_editor").on("input", function () {
                            replyEditorOnInput($(this));
                        });
                        newCommentBox.find('#reply_btn').on("click", function () {
                            showOrHideReplyArea($(this));
                        });
                        newCommentBox.find('#cancel_reply').on("click", function () {
                            clickCancelReplyBtn($(this));
                        });
                        newCommentBox.find('#submit_reply').on("click", function () {
                            clickSubmitReplyBtn($(this));
                        });
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

    // reply editor on focus
    $('[id = reply_editor]').on("focus", function () {
        replyEditorOnFocus($(this));
    });

    function replyEditorOnFocus(replyEditor) {
        let contentDefault = 'Add a public reply...';
        let content = replyEditor.text();
        if (content === contentDefault) {
            replyEditor.addClass('is-focus');
            replyEditor.text('');
        }
    }

    // reply editor on blur
    $('[id = reply_editor]').on("blur", function () {
        replyEditorOnBlur($(this));
    });

    function replyEditorOnBlur(replyEditor) {
        let contentDefault = 'Add a public reply...';
        let content = replyEditor.text();
        if (!content) {
            replyEditor.removeClass('is-focus');
            replyEditor.text(contentDefault);
        }
    }

    // reply editor on input, enable reply button
    $('[id = reply_editor]').on("input", function () {
        replyEditorOnInput($(this));
    });

    function replyEditorOnInput(replyEditor) {
        let content = replyEditor.text();
        let submitBtn = replyEditor.next().find('#submit_reply');
        if (content) {
            submitBtn.removeClass('btn-secondary');
            submitBtn.addClass('btn-primary');
            submitBtn.removeAttr('disabled');

        } else {
            submitBtn.removeClass('btn-primary');
            submitBtn.addClass('btn-secondary');
            submitBtn.attr('disabled', true);
        }
    }

    // click cancel_reply btn to cancel reply
    $('[id = cancel_reply]').on("click", function () {
        clickCancelReplyBtn($(this));
    });

    function clickCancelReplyBtn(cancelReplyBtn) {
        let contentDefault = 'Add a public reply...';
        let replyBox = cancelReplyBtn.parent().prev();
        let submitReplyBtn = cancelReplyBtn.next();
        let content = replyBox.text();
        if (content) {
            replyBox.removeClass('is-focus');
            replyBox.text(contentDefault);
            submitReplyBtn.removeClass('btn-primary');
            submitReplyBtn.addClass('btn-secondary');
            submitReplyBtn.attr('disabled', true);
        }
    }

    // click submit_reply btn to submit reply
    $('[id = submit_reply]').on("click", function () {
        clickSubmitReplyBtn($(this));
    });

    function clickSubmitReplyBtn(submitReplyBtn) {
        let video_id = $('#video_player').attr('video-id');
        let comment_id = submitReplyBtn.parent().parent().parent().parent().parent().attr('comment-id');
        let user_id = submitReplyBtn.parent().parent().prev().children().attr('user-id');
        let text = submitReplyBtn.parent().prev()[0].innerText.replace(/(?:\r\n|\r|\n)/g, '<br>');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {post_reply: true, video_id: video_id, comment_id: comment_id, user_id: user_id, text: text},
            dataType: 'json',
            success: function (res) {
                if (!res.status) { // not success
                    if (res.data !== "Not signIn") { // server side error
                        $("#warning_message").text('Something went wrong. Submit reply failed.');
                        $("#alert_modal").modal('show');
                    } else {
                        // open the signIn modal
                        $("#sign_in_modal").modal('show');
                    }
                } else { // success
                    // set the comment box and comment btn to default style
                    let contentDefault = 'Add a public reply...';
                    let replyBox = submitReplyBtn.parent().prev();
                    let commentCount = $('.comment-count');
                    replyBox.removeClass('is-focus');
                    replyBox.text(contentDefault);
                    submitReplyBtn.removeClass('btn-primary');
                    submitReplyBtn.addClass('btn-secondary');
                    submitReplyBtn.attr('disabled', true);
                    if (res.data) {
                        let repltesWrapper = submitReplyBtn.parent().parent().parent().next();
                        repltesWrapper.prepend(res.data);
                        let newCommentBox = repltesWrapper.children(":first");
                        newCommentBox.find("#show_more_btn").on("click", function () {
                            showMoreAndLessForCommentBox($(this));
                        });
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


    // bind event for show more/show less btn
    $('.comments-wrapper #show_more_btn').on("click", function () {
        showMoreAndLessForCommentBox($(this));
    });

    function showMoreAndLessForCommentBox(showMoreBtn) {
        let showMoreBtnText = showMoreBtn.text();
        let prev = showMoreBtn.prev();
        let dots = prev.find('#dots');
        let more = prev.find('#more');
        dots.toggle();
        more.toggle();
        showMoreBtn.text(showMoreBtnText === 'SHOW MORE' ? 'SHOW LESS' : 'SHOW MORE');
    }

    // bind event for show/hide reply box
    $('[id = reply_btn]').on("click", function () {
        showOrHideReplyArea($(this));
    });

    function showOrHideReplyArea(replyBtn) {
        replyBtn.next().toggle();
    }

    // live loading comments
    $(function loadComments() {
        let video_id = $('#video_player').attr('video-id');
        let page = 1; // current page
        $(window).scroll(function () {
            let thisFunc = arguments.callee;
            let self = $(this);
            if ($(document).height() - $(window).height() - $(window).scrollTop() < 10) {
                self.unbind('scroll', thisFunc); // cancel scroll event binding to prevent multiple ajax request
                $.ajax({
                    type: 'post',
                    url: './submit.php',
                    data: {get_comment: true, video_id: video_id, page: page},
                    dataType: 'json',
                    beforeSend: function () {
                        $('.comments-section .loading-image-wrapper .loading-image').show();
                    },
                    success: function (res) {
                        if (res.status) { // success
                            $('.comments-section .loading-image-wrapper .loading-image').hide();
                            let res_length = res.data.length;
                            for (let i = 0; i < res_length; i++) {
                                $('.comments-wrapper').append(res.data[i]);
                                let newCommentBox = $('.comments-wrapper').children(":last");
                                newCommentBox.find("#show_more_btn").on("click", function () {
                                    showMoreAndLessForCommentBox($(this));
                                });
                                newCommentBox.find("#reply_editor").on("focus", function () {
                                    replyEditorOnFocus($(this));
                                });
                                newCommentBox.find("#reply_editor").on("blur", function () {
                                    replyEditorOnBlur($(this));
                                });
                                newCommentBox.find("#reply_editor").on("input", function () {
                                    replyEditorOnInput($(this));
                                });
                                newCommentBox.find('#reply_btn').on("click", function () {
                                    showOrHideReplyArea($(this));
                                });
                                newCommentBox.find('#cancel_reply').on("click", function () {
                                    clickCancelReplyBtn($(this));
                                });
                                newCommentBox.find('#submit_reply').on("click", function () {
                                    clickSubmitReplyBtn($(this));
                                });
                            }
                            if (res_length === 5) { // maybe there is more data in database we can get
                                page += 1;
                                setTimeout(function () { // restore the scroll event binding
                                    self.bind('scroll', thisFunc);
                                }, 500);
                            } else { // no more data to request
                                // there's no need to restore the scroll function binding
                                $('.comments-section .no-more-data').show();
                            }

                        } else { // no data get from the server side
                            $('.comments-section .no-more-data').show();
                            $('.comments-section .loading-image-wrapper .loading-image').hide();
                        }
                    },
                    error: function (xhr, status, error) { // ajax error
                        let errorMessage = xhr.status + ': ' + xhr.statusText;
                        $("#warning_message").text(errorMessage);
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    });

    // live loading comments
    $(function loadRecommendVideos() {
        let video_id = $('#video_player').attr('video-id');
        let page = 1; // current page
        $(window).scroll(function () {
            let thisFunc = arguments.callee;
            let self = $(this);
            if ($(document).height() - $(window).height() - $(window).scrollTop() < 10 || $(window).scrollTop() >= $('.reco-videos-container').height() / 3) {
                self.unbind('scroll', thisFunc); // cancel scroll event binding to prevent multiple ajax request
                $.ajax({
                    type: 'post',
                    url: './submit.php',
                    data: {get_recommendation: true, video_id: video_id, page: page},
                    dataType: 'json',
                    beforeSend: function () {
                        $('.suggestion .loading-image-wrapper .loading-image').show();
                    },
                    success: function (res) {
                        if (res.status) { // success
                            $('.suggestion .loading-image-wrapper .loading-image').hide();
                            let res_length = res.data.length;
                            for (let i = 0; i < res_length; i++) {
                                $('.reco-videos-container').append(res.data[i]);
                            }
                            if (res_length === 5) { // maybe there is more data in database we can get
                                page += 1;
                                setTimeout(function () { // restore the scroll event binding
                                    self.bind('scroll', thisFunc);
                                }, 500);
                            } else { // no more data to request
                                // there's no need to restore the scroll function binding
                                $('.suggestion .no-more-data').show();
                            }

                        } else { // no data get from the server side
                            $('.suggestion .no-more-data').show();
                            $('.suggestion .loading-image-wrapper .loading-image').hide();
                        }
                    },
                    error: function (xhr, status, error) { // ajax error
                        let errorMessage = xhr.status + ': ' + xhr.statusText;
                        $("#warning_message").text(errorMessage);
                        $("#alert_modal").modal('show');
                    }
                });
            }
        });
    });
});