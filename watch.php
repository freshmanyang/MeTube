<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once("./includes/class/VideoPlayer.php"); ?>
<link rel="stylesheet" href="./assets/css/watch.css">
<script src="./assets/js/watch_page.js" defer></script>
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
        if (!isset($_GET['vid'])) {
            echo "Invalid video id";
            exit();
        }
        $videoObj = new Video($conn, $_GET['vid'], $userLoginInObj);
        $videoObj->incrementView();
        $videoPlayer = new VideoPlayer($videoObj);
        ?>
        <div class="watch-left">
            <?php
            echo $videoPlayer->create(true);
            ?>
            <div class="video-primary-info-renderer">
                <div class="upper-wrapper">
                    <h1 class="video-title">
                        <?php echo $videoObj->getTitle(); ?>
                    </h1>
                    <?php echo "<a href='" . $videoObj->getFilePath() . "' download='" . $videoObj->getTitle() . "'>
                        <button class=\"btn btn-primary btn-sm\" id=\"download_btn\" video-target='" . $videoObj->getVideoId() . "'>Download</button></a>" ?>
                </div>
                <div class="lower-wrapper">
                    <div class="left-wrapper">
                        <span class="views">
                            <?php echo $videoObj->getViews() . " views"; ?>
                        </span>
                        <span class="dot">&nbsp•&nbsp;</span>
                        <span class="upload-date">
                            <?php
                            $dateTime = date_create($videoObj->getUploadDate());
                            echo date_format($dateTime, 'Y-m-d');
                            ?>
                        </span>
                    </div>
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
                </div>
            </div>
            <div class="video-secondary-info-renderer">
                <div class="upper-wrapper">
                    <a href="" class="video-owner-page-link">
                        <?php
                        $videoOwnerAvatar = $videoObj->getVideoOwnerAvatar();
                        echo "<img src='$videoOwnerAvatar' alt=''>";
                        ?>
                    </a>
                    <div class="upload-info">
                        <a href="" class="video-owner-name"><?php echo $videoObj->getVideoOwnerName(); ?></a>
                        <div class="subscriber-count"><?php echo $videoObj->getSubscriptionCount(); ?> subscribers</div>
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
                    if (strlen($descriptionText) > 100) {
                        $c = substr($descriptionText, 0, 100);
                        $h = substr($descriptionText, 100);
                        echo "<p>$c<span id='dots'>...</span><span id='more'>$h</span></p>";
                        echo "<div class='show-more-btn' id='show_more_btn'>SHOW MORE</div>";
                    }
                    else{
                        echo "<p>$descriptionText</p>";
                    }
                    ?>
                </div>

            </div>
        </div>
        <div class="suggestion">
        </div>
    </div>
</main>
</body>
</html>
