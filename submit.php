<?php
require_once("./includes/config.php");
require_once("./includes/class/AccountHandler.php");
require_once("./includes/class/AvatarUpload.php");
require_once("./includes/class/User.php");

$accountHandler = new AccountHandler($conn);
if (isset($_SESSION["uid"])) {
    $userObj = new User($conn, $_SESSION["uid"]);
}

$response = array(
    'status' => false,
    'data' => ''
);

// test username exist
if (isset($_POST["untested_username"])) {
    $uname = $_POST["untested_username"];
    $res['exist'] = $accountHandler->usernameExisted($uname);
    echo json_encode($res);
    exit;
}

// test email exist
if (isset($_POST["untested_email"])) {
    $email = $_POST["untested_email"];
    $res['exist'] = $accountHandler->emailExisted($email);
    echo json_encode($res);
    exit;
}

// check password
if (isset($_POST["email"]) && isset($_POST["untested_password"])) {
    $email = $_POST["email"];
    $password = $_POST["untested_password"];
    $res['correct'] = $accountHandler->checkPassword($email, $password);
    echo json_encode($res);
    exit;
}

// if the sign up is posted, do register
if (isset($_POST["sign_up_submit"])) {
    $id = $accountHandler->register($_POST);
    if ($id) {
        $_SESSION["uid"] = $id;
        $userObj = new User($conn, $_SESSION["uid"]);
        $_SESSION["userLoggedIn"] = $userObj->getUsername();
    }
    header("Location:index.php");
    exit;
}

// sign in
if (isset($_POST["sign_in_submit"])) {
    $id = $accountHandler->signIn($_POST);
    if ($id) {
        $_SESSION["uid"] = $id;
        $userObj = new User($conn, $_SESSION["uid"]);
        $_SESSION["userLoggedIn"] = $userObj->getUsername();
    }
    header("Location:index.php");
    exit;
}

// update avatar
if (isset($_FILES["update_avatar"])) {
    if ($userObj->setAvatarPath($_FILES["update_avatar"])) { // upload success
        $response['status'] = true;
        $response['data'] = $userObj->getAvatarPath();
    } else { // upload failed
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// update name
if (isset($_POST["input_first_name"]) && isset($_POST["input_last_name"])) {
    if ($userObj->setFirstName($_POST["input_first_name"]) && $userObj->setLastName($_POST["input_last_name"])) {
        $response['status'] = true;
        $response['data'] = $userObj->getFirstName() . " " . $userObj->getlastName();
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// update username
if (isset($_POST["input_username"])) {
    if ($userObj->setUsername($_POST["input_username"])) {
        $response['status'] = true;
        $response['data'] = $userObj->getUsername();
        $_SESSION["userLoggedIn"] = $userObj->getUsername();
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// update birthday
if (isset($_POST["update_birthday"])) {
    if ($userObj->setBirthday($_POST["update_birthday"])) {
        $response['status'] = true;
        $response['data'] = $userObj->getBirthday();
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// update gender
if (isset($_POST["select_gender"])) {
    if ($userObj->setGender($_POST["select_gender"])) {
        $response['status'] = true;
        $response['data'] = $userObj->getGender();
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// check identity before update password
if (isset($_POST["check_identity"])) {
    if (hash('sha512', $_POST["check_identity"]) === $userObj->getPassword()) {
        $response['status'] = true;
    } else {
        $response['status'] = false;
    }
    echo json_encode($response);
    exit;
}

// update password
if (isset($_POST["old_password"]) && isset($_POST["input_password"]) && isset($_POST["confirm_password"])) {
    $encrypted_password = hash('sha512', $_POST["input_password"]);
    if ($userObj->setPassword($encrypted_password)) {
        $response['status'] = true;
    } else {
        $response['status'] = false;
    }
    echo json_encode($response);
    exit;
}

// if not of above, sing out
$accountHandler->signOut();
header("Location:index.php");
?>


