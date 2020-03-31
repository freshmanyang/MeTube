<?php
require_once("./includes/config.php");
require_once("./includes/class/User.php");
require_once("./includes/class/Video.php");
//require_once('./includes/clemsonconfig.php');
$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
$userLoginInObj = new User($conn, $uid);
$usernameLoggedIn = isset($_SESSION['userLoggedIn']) ? $_SESSION['userLoggedIn'] : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- bootstrap-select css -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
    <!-- iconfont css -->
    <link rel="stylesheet" href="./assets/iconfont/iconfont.css">
    <!-- main style css -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <!-- ajax -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- bootstrap js-->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
            crossorigin="anonymous"></script>
    <!-- bootstrap-select js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <!-- bootstrap Tempus Dominus -->
<!--    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
    <!-- pagination-->
    <script src="../assets/js/jquery.twbsPagination.js" type="text/javascript"></script>
    <!-- local js -->
    <script src="./assets/js/common-action.js" defer></script>
    <script src="./assets/js/account.js" defer></script>
    <title>MeTube</title>
</head>
<body>
<header class="master-head-container">
    <button class="master-head-button" id="menu_button"><i class="iconfont icon-menu"></i></button>
    <a href="./index.php" class="logo">
        <i class="iconfont icon-play"></i>
        <span class="logo-name">MeTube</span>
    </a>
    <div class="search-container">
        <form class="search-form" action="./search.php">
            <div class="search-box" id="search_box">
                <input id="search_input" type="text" placeholder="Search">
            </div>
            <button class="search-button"><i class="iconfont icon-search"></i></button>
        </form>
    </div>
    <div class="end">
        <a href="./upload.php" class="master-head-button" id="upload_button"><i class="iconfont icon-upload"></i></a>
        <button class="master-head-button" id="notification_button"><i class="iconfont icon-notification"></i></button>
        <?php
        if (!isset($_SESSION['uid'])) {
            echo "<button class=\"btn btn-primary btn-sm\"  data-toggle=\"modal\" data-target=\"#sign_in_modal\">Sign In</button>";
        } else {
            echo "<button class='avatar-button'>
                <img src='" . $userLoginInObj->getAvatarPath() . "' class='avatar-sm' id='header_avatar'>
              </button>";
        }
        ?>
    </div>
    <div class="header-popup-wrapper" id="popup" style="display: none">
        <div class="header">
            <div class="header-renderer">
                <?php
                echo "<img src='" . $userLoginInObj->getAvatarPath() . "' class='avatar-md'>";
                echo "<p class='username' id='header_username'>" . $userLoginInObj->getUsername() . "</p>";
                echo "<p class='email'>" . $userLoginInObj->getEmail() . "</p>";
                ?>
            </div>
        </div>
        <div class="footer">
            <a href="./profile.php" class="endpoint">
                <div class="endpoint-content">
                    <i class="iconfont icon-profile"></i>
                    <p>Profile</p>
                </div>
            </a>
            <a href="./submit.php" class="endpoint">
            <div class="endpoint-content">
                <i class="iconfont icon-signout"></i>
                <p>Sign out</p>
            </div>
            </a>
        </div>
    </div>
</header>
<?php require_once("./includes/components/sign_up_modal.php"); ?>
<?php require_once("./includes/components/sign_in_modal.php"); ?>
<?php require_once("./includes/components/alert_modal.php"); ?>

