<?php
require_once("includes/class/searchProcessor.php");
require_once('./includes/config.php');
$usernameLoggedIn = isset($_SESSION['userLoggedIn']) ? $_SESSION['userLoggedIn'] : "";
$search = new searchProcessor($conn);
if (isset($_POST['normalSearch'])) {
    echo json_encode($search->showNormalSearch($usernameLoggedIn, $_POST['search_input']));
}
if (isset($_POST['advancedSearch'])) {
    echo json_encode($search->showAdvancedSearch($usernameLoggedIn, $_POST['search_input'],
        $_POST['file_size_min'], $_POST['file_size_max'],
        $_POST['duration_min'], $_POST['duration_max'],
        $_POST['views_min'], $_POST['views_max'], $_POST['uplodate_start'], $_POST['uplodate_end'],
        $_POST['category'], $_POST['videoTitle'], $_POST['upload_by'], $_POST['description']
    ));
}
if (isset($_POST['hotkeyword'])) {
    echo json_encode($search->showHotKeyWord());
}
?>