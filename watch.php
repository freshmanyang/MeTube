<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once("./includes/class/VideoPlayer.php"); ?>
<link rel="stylesheet" href="./assets/css/watch.css">
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
        </div>
        <div class="suggestion">

        </div>
    </div>
</main>
</body>
</html>
