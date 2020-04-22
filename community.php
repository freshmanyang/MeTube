<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<?php require_once("includes/class/CommunityHandler.php"); ?>
<?php require_once("includes/class/TopicHandler.php"); ?>
<?php $communityHandler = new CommunityHandler($conn); ?>
<link rel="stylesheet" href="assets/css/community.css">
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <div class="community-wrapper">
            <?php if (!isset($_GET['community_id'])): ?>
                <?php require_once("includes/components/new_community_modal.php"); ?>
                <div class="header-wrapper">
                    <p>MeTube's Communities</p>
                    <button class="btn btn-success btn-sm" id="create_community_btn">NEW</button>
                </div>
                <?php
                $communities = $communityHandler->getAllCommunities();
                if (count($communities) === 0) {
                    echo "<div class='no-data'>There is no community</div>";
                } else {
                    $communityDivs = $communityHandler->communityRenderer($communities);
                    foreach ($communityDivs as $div) {
                        echo $div;
                    }
                }
                ?>
            <?php else: ?>
                <?php $communityInfo = $communityHandler->getCommunityById($_GET['community_id']); ?>
                <?php require_once("includes/components/new_topic_modal.php"); ?>
                <div class="community-page-header">
                    <h2><?php echo $communityInfo['community_name']?></h2>
                    <?php
                    if (isset($_SESSION['uid']) && $communityHandler->userInCommunity($_GET['community_id'], $_SESSION['uid'])) {
                        echo "<button class=\"btn btn-info btn-sm\" id=\"post_btn\">POST</button>";
                    } else {
                        echo "<button class=\"btn btn-success btn-sm\" id=\"join_btn\">JOIN</button>
                              <button class=\"btn btn-info btn-sm\" id=\"post_btn\" style=\"display: none\">POST</button>";
                    }
                    ?>
                </div>
                <?php
                $topicHandler = new TopicHandler($conn);
                $topics = $topicHandler->getAllTopicsByCommunityId($_GET['community_id']);
                if (count($topics) === 0) {
                    echo "<div class='no-data' style='margin: 20px 0;'>There is no topic</div>";
                } else {
                    $topicDivs = $topicHandler->topicRenderer($topics);
                    foreach ($topicDivs as $div) {
                        echo $div;
                    }
                }
                ?>
            <?php endif ?>
        </div>
    </div>
</main>
</body>
<script type="text/javascript" defer>
    const user_id = $('.header-popup-wrapper img').attr('user-id');
    const searchParams = new URLSearchParams(window.location.search);
    const community_id = searchParams.get('community_id');

    $("#create_community_btn").on("click", function () {
        if (!user_id) {
            $("#sign_in_modal").modal('show');
            return;
        }
        $("#new_community_modal").modal('show');
    });

    $("#join_btn").on("click", function () {
        if (!user_id) {
            $("#sign_in_modal").modal('show');
            return;
        }
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {join_community: true, community_id: community_id, user_id: user_id},
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    $("#join_btn").hide();
                    $("#post_btn").show();
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });

    $("#post_btn").on("click", function () {
        $("#new_topic_modal").modal('show');
    });

    $("#new_topic_form").on("submit", function (e) {
        e.preventDefault();
        let title = $(this).find('input').val();
        let text = $(this).find('textarea').val();
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {create_topic: true, community_id: community_id, user_id: user_id, title: title, text: text},
            dataType: 'json',
            beforeSend: function () {
                $("#new_topic_modal").modal('hide');
            },
            success: function (res) {
                if (res.status && res.data.length !== 0) {
                    let body = $('.community-wrapper');
                    res.data.forEach(function (val, index) {
                        body.append(val);
                    });
                    $('.no-data').hide();
                } else {
                    $("#warning_message").text('Something went wrong. Topic was not created.');
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
</script>
</html>