<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<?php require_once("includes/class/VideoPlayer.php"); ?>
<?php require_once("includes/class/CommentHandler.php"); ?>
<?php require_once('./includes/class/channelProcessor.php'); ?>
?>
<?php $channel = new channelProcessor($conn, '', $usernameLoggedIn); ?>
<link rel="stylesheet" href="assets/css/watch.css">
<script src="assets/js/watch_page.js" defer></script>
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
        if (!isset($_GET['vid'])) {
            echo "Invalid video id";
            exit();
        }
        $videoObj = new Video($conn, $_GET['vid'], $userLoginInObj);
        $checkAuth = $videoObj->checkVideoAuth();
        if ($checkAuth) {
            $videoObj->incrementView();
        }
        $videoPlayer = new VideoPlayer($videoObj);
        $commentsObj = new CommentHandler($conn, $_GET['vid']);
        $comments = $commentsObj->getComments(0, 5);
        $recommendationVideos = $videoObj->getRecommendationVideos(0, 5);
        $channelLink = 'channel.php?channel=';
        ?>
        <div class="watch-left">
            <?php if (!$checkAuth): ?>
                <div class="hide-media" id="video_player" video-id="<?php echo $videoObj->getVideoId() ?>"><p>You don't
                        have access to this video</p></div>
            <?php else: ?>
                <?php
                echo $videoPlayer->create(true);
                ?>
            <?php endif ?>
            <div class="video-primary-info-renderer">
                <div class="upper-wrapper">
                    <h1 class="video-title">
                        <?php echo $videoObj->getTitle(); ?>
                    </h1>
                    <?php
                    if ($checkAuth) {
                        echo "<a href='" . $videoObj->getFilePath() . "' download='" . $videoObj->getTitle() . "'>
                        <button class=\"btn btn-primary btn-sm\" id=\"download_btn\" video-id='" . $videoObj->getVideoId() . "'>Download</button></a>";
                    }
                    ?>
                </div>
                <div class="lower-wrapper">
                    <div class="left-wrapper">
                        <span class="views">
                            <?php echo $videoObj->getViews() . " views"; ?>
                        </span>
                        <span class="dot">•</span>
                        <span class="upload-date">
                            <?php
                            $dateTime = date_create($videoObj->getUploadDate());
                            echo date_format($dateTime, 'Y-m-d');
                            ?>
                        </span>
                    </div>
                    <?php if ($checkAuth): ?>
                        <div class="right-wrapper">
                            <button class="watch-left-btn <?php echo $userLoginInObj->hasRecordInLikedList($videoObj->getVideoId()) ? 'clicked' : ''; ?> "
                                    id="like_btn">
                                <i class="iconfont icon-thumb-up <?php echo $userLoginInObj->hasRecordInLikedList($videoObj->getVideoId()) ? 'clicked' : ''; ?>"></i>
                                <span id="like_count">
                                    <?php
                                    echo $videoObj->getLikedCount();
                                    ?>
                                </span>
                            </button>
                            <button class="watch-left-btn <?php echo $userLoginInObj->hasRecordInDislikedList($videoObj->getVideoId()) ? 'clicked' : ''; ?>"
                                    id="dislike_btn">
                                <i class="iconfont icon-thumb-down <?php echo $userLoginInObj->hasRecordInDislikedList($videoObj->getVideoId()) ? 'clicked' : ''; ?>"></i>
                                <span id="dislike_count">
                                    <?php
                                    echo $videoObj->getDislikedCount();
                                    ?>
                                </span>
                            </button>
                            <button class="watch-left-btn" id="save_btn">
                                <i class="iconfont icon-save"></i>
                                <span>SAVE</span>
                            </button>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <?php require_once("includes/components/add_to_playlist_modal.php"); ?>
            <div class="video-secondary-info-renderer">
                <div class="upper-wrapper">
                    <a href="<?php echo $channelLink . $videoObj->getVideoOwnerName(); ?>"
                       class="video-owner-page-link">
                        <?php
                        $videoOwnerAvatar = $videoObj->getVideoOwnerAvatar();
                        $videoOwnerId = $videoObj->getUserId();
                        echo "<img src='$videoOwnerAvatar' alt='' user-id='$videoOwnerId'>";
                        ?>
                    </a>
                    <div class="upload-info">
                        <?php
                        $videoOwnerName = $videoObj->getVideoOwnerName();
                        $subscribeCount = $userLoginInObj->getSubscribeCountByName($videoOwnerName);
                        $href = $channelLink . $videoObj->getVideoOwnerName();
                        echo "<a href='$href' class=\"video-owner-name\">$videoOwnerName</a>";
                        echo "<div class=\"subscriber-count\">$subscribeCount subscribers</div>";
                        ?>
                    </div>
                    <div class="btn-wrapper">
                        <?php
                        $subscribed = $userLoginInObj->isSubscribed($videoObj->getVideoOwnerName());
                        if ($subscribed) {
                            echo "<button class=\"btn btn-secondary btn-sm\" id=\"subscribe_btn\">SUBSCRIBED</button>";
                        } else {
                            echo "<button class=\"btn btn-danger btn-sm\" id=\"subscribe_btn\">SUBSCRIBE</button>";
                        }
                        ?>
                    </div>
                </div>
                <div class="lower-wrapper">
                    <?php
                    $descriptionText = $videoObj->getDescription();
                    $descriptionText = nl2br($descriptionText); // convert \n and \r\n to <br \>
                    if (strlen($descriptionText) > 100) {
                        $c = substr($descriptionText, 0, 100);
                        $h = substr($descriptionText, 100);
                        echo "<p>$c<span id='dots'>...</span><span id='more'>$h</span></p>";
                        echo "<span class='show-more-btn' id='show_more_btn'>SHOW MORE</span>";
                    } else {
                        echo "<p>$descriptionText</p>";
                    }
                    ?>
                </div>
            </div>
            <?php if ($checkAuth): ?>
                <div class="comments-section">
                    <div class="comments-header-renderer">
                        <?php
                        $commentCount = $commentsObj->getCommentsCount();
                        echo "<div class='comment-count'> $commentCount Comments</div>"
                        ?>
                        <?php if (isset($_SESSION["uid"])): ?>
                            <div class="comment-box">
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
                                        <button class="btn btn-secondary btn-sm" id="submit_comment" disabled>COMMENT
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="comments-wrapper">
                        <?php if (isset($comments)): ?>
                            <!-- Display comments -->
                            <?php
                            $commentElements = $commentsObj->commentsRenderer($comments);
                            foreach ($commentElements as $commentElement) {
                                echo $commentElement;
                            }
                            ?>
                        <?php endif ?>
                    </div>
                    <div class="loading-image-wrapper">
                        <img class="loading-image" src="assets/imgs/loading.gif" style="display: none">
                    </div>
                    <div class="no-more-data" style="display: none;">No more data</div>
                </div>
            <?php endif ?>
        </div>
        <div class="suggestion">
            <div class="reco-header">Up Next</div>
            <div class="reco-videos-container">
                <?php if (isset($recommendationVideos)): ?>
                    <!-- Display recommend videos -->
                    <?php
                    $recommendationVideoElements = $videoObj->recommendationsVideoRenderer($recommendationVideos);
                    foreach ($recommendationVideoElements as $recommendationVideoElement) {
                        echo $recommendationVideoElement;
                    }
                    ?>
                <?php endif ?>
            </div>
            <div class="loading-image-wrapper">
                <img class="loading-image" src="assets/imgs/loading.gif" style="display: none">
            </div>
            <div class="no-more-data" style="display: none;">No more data</div>
        </div>
    </div>
</main>
</body>
</html>
