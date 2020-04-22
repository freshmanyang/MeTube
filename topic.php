<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<?php require_once("includes/class/CommunityHandler.php"); ?>
<?php require_once("includes/class/TopicHandler.php"); ?>
<?php $communityHandler = new CommunityHandler($conn); ?>
<?php $topicHandler = new TopicHandler($conn); ?>
<link rel="stylesheet" href="assets/css/topic.css">
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <div class="main-wrapper">
            <?php if (!isset($_GET['topic_id'])): ?>
                <?php
                echo 'Invalid topic';
                exit();
                ?>
            <?php else: ?>
                <?php $communityInfo = $communityHandler->getCommunityInfoByTopicId($_GET['topic_id']) ?>
                <div class="community-name-wrapper">
                    <?php echo "<h1 class='community-name' community-id='" . $communityInfo['id'] . "'>" . $communityInfo['community_name'] . "</h1>" ?>
                </div>
                <?php
                $topicHandler = new TopicHandler($conn);
                $topicInfo = $topicHandler->getTopicById($_GET['topic_id']);
                $channelLink = 'channel.php?channel=';
                echo $topicHandler->topicRenderer(array($topicInfo))[0];
                ?>
                <div class="comments-section">
                    <div class="comments-header-renderer">
                        <?php if (isset($_SESSION["uid"])): ?>
                            <?php if ($communityHandler->userInCommunity($communityInfo['id'], $_SESSION["uid"])): ?>
                                <div class="comment-box" id="editor_box">
                                    <a href="<?php echo $channelLink . $userLoginInObj->getUsername() ?>"
                                       class="user-page-link">
                                        <?php
                                        $userAvatarPath = $userLoginInObj->getAvatarPath();
                                        echo "<img src='$userAvatarPath' alt='' user-id='$uid'>";
                                        ?>
                                    </a>
                                    <div class="comment-editor-wrapper">
                                        <div id="comment_editor" contenteditable="true">Add a public comment...</div>
                                        <div class="button-wrapper">
                                            <button class="btn btn-default btn-sm" id="cancel_comment">CANCEL</button>
                                            <button class="btn btn-secondary btn-sm" id="submit_comment" disabled>
                                                COMMENT
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="need-join" style="display: none">
                                    <p>Join community to leave a comment</p>
                                    <button class="btn btn-success btn-sm" id="need_join_btn">JOIN</button>
                                </div>
                            <?php else: ?>
                                <div class="comment-box" id="editor_box" style="display: none">
                                    <a href="<?php echo $channelLink . $userLoginInObj->getUsername() ?>"
                                       class="user-page-link">
                                        <?php
                                        $userAvatarPath = $userLoginInObj->getAvatarPath();
                                        echo "<img src='$userAvatarPath' alt='' user-id='$uid'>";
                                        ?>
                                    </a>
                                    <div class="comment-editor-wrapper">
                                        <div id="comment_editor" contenteditable="true">Add a public comment...</div>
                                        <div class="button-wrapper">
                                            <button class="btn btn-default btn-sm" id="cancel_comment">CANCEL</button>
                                            <button class="btn btn-secondary btn-sm" id="submit_comment" disabled>
                                                COMMENT
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="need-join">
                                    <p>Join this community to leave a comment</p>
                                    <button class="btn btn-success btn-sm" id="need_join_btn">JOIN</button>
                                </div>
                            <?php endif ?>
                        <?php else: ?>
                            <div class="need-login">
                                <p>Sign in to leave a comment</p>
                                <button class="btn btn-primary btn-sm" id="need_login_btn">SIGN IN</button>
                            </div>
                        <?php endif ?>
                        <div class="comments-wrapper"></div>
                    </div>
                    <?php
                    $comments = $topicHandler->getAllCommentsById($_GET['topic_id']);
                    if (count($comments) == 0) {
                        echo "<div class='no-data'>There is no comment</div>";
                    } else {
                        $commentDivs = $topicHandler->commentRenderer($comments);
                        foreach ($commentDivs as $div) {
                            echo $div;
                        }
                    }
                    ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</main>
</body>
<script type="text/javascript" defer>
    const user_id = $('.header-popup-wrapper img').attr('user-id');
    const searchParams = new URLSearchParams(window.location.search);
    const topic_id = searchParams.get('topic_id');

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
        let text = $('#comment_editor')[0].innerText.replace(/(?:\r\n|\r|\n)/g, '<br>');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {post_comment: true, topic_id: topic_id, user_id: user_id, text: text},
            dataType: 'json',
            success: function (res) {
                if (res.status && res.data.length !== 0) { // success
                    // set the comment box and comment btn to default style
                    let contentDefault = 'Add a public comment...';
                    let commentBox = $('#comment_editor');
                    let submitBtn = $('#submit_comment');
                    let commentCount = $('.comment-count span');
                    commentBox.removeClass('is-focus');
                    commentBox.text(contentDefault);
                    submitBtn.removeClass('btn-primary');
                    submitBtn.addClass('btn-secondary');
                    submitBtn.attr('disabled', true);
                    if (res.data.comment_count) {
                        commentCount.text(res.data.comment_count + ' Comments');
                    }
                    if (res.data.comment_div) {
                        $('.comments-wrapper').prepend(res.data.comment_div);
                    }
                    $('.no-data').hide();
                } else {
                    $("#warning_message").text('Something went wrong. Submit comment failed.');
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

    $('#need_login_btn').on("click", function () {
        $("#sign_in_modal").modal('show');
    });

    $("#need_join_btn").on("click", function () {
        if (!user_id) {
            $("#sign_in_modal").modal('show');
            return;
        }
        let community_id = $('.community-name').attr('community-id');
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {join_community: true, community_id: community_id, user_id: user_id},
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    $('.need-join').hide();
                    $("#editor_box").show();
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });
</script>
</html>