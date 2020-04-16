<?php
require_once('./includes/class/showAllVideo.php');
require_once('./includes/config.php');
if (isset($_POST['showallvideo'])) {
    $allvideo = new showAllVideo($conn);
    echo json_encode($allvideo->createwithBlock($_POST['loginUser']));
}
?>