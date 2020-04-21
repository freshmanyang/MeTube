<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<?php require_once("includes/class/VideoUpload.php"); ?>
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
        if (!isset($_POST['submit'])) {
            // test if media_upload_process script is issued by the submit button rather than by URL
            echo "No file uploaded!";
            exit();
        }

        $videoUploadObj = new VideoUpload($conn);
        $videoUploadObj->setData($_FILES['file'],
                                 $_POST['title'],
                                 $_POST['description'],
                                 $_POST['keywords'],
                                 $_POST['privacy'],
                                 $_POST['category'],
            $userLoginInObj->getUserName());
        $res = $videoUploadObj->upload();
        ?>
    </div>
</main>
</body>
</html>
