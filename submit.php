<?php
require_once("./includes/config.php");
require_once("./includes/class/AccountHandler.php");
require_once("./includes/class/CommentHandler.php");
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
//    header("Location:index.php");
    echo "<script type='text/javascript'>history.go(-1)</script>";
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
//    header("Location:index.php");
    echo "<script type='text/javascript'>history.go(-1)</script>";
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

// click download button, insert record into download_list
if (isset($_POST["download"]) && isset($_POST["video_id"])) {
    if (isset($_SESSION["uid"])) { // if signIn, insert record
        $response['status'] = $userObj->insertRecordIntoDownloadList($_POST["video_id"]);
    } else { // if not signIn, do nothing with the database
        $response['status'] = false;
    }
    echo json_encode($response);
    exit;
}

// click like button
if (isset($_POST["like"]) && isset($_POST["video_id"])) {
    if (isset($_SESSION["uid"])) { // if signIn, insert record
        $video_id = $_POST["video_id"];
        if ($userObj->hasRecordInLikedList($_POST["video_id"])) {
            // if liked before, then delete record form liked_list
            $response['status'] = $userObj->deleteRecordFromLikedList($_POST["video_id"]);
        } else {
            //  if not liked before, then insert record into liked_list
            $response['status'] = $userObj->insertRecordIntoLikedList($_POST["video_id"]);
        }
        // get likedCount and dislikedCount here
        $query = $conn->prepare("SELECT * FROM liked_list WHERE video_id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->execute();
        $likedCount = $query->rowCount();
        $query = $conn->prepare("SELECT * FROM disliked_list WHERE video_id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->execute();
        $dislikedCount = $query->rowCount();
        $response['data'] = array('likedCount' => $likedCount, 'dislikedCount' => $dislikedCount);
    } else { // if not signIn, do nothing with the database
        $response['status'] = false;
        $response['data'] = 'Not signIn';
    }
    echo json_encode($response);
    exit;
}

// click dislike button
if (isset($_POST["dislike"]) && isset($_POST["video_id"])) {
    if (isset($_SESSION["uid"])) { // if signIn, insert record
        $video_id = $_POST["video_id"];
        if ($userObj->hasRecordInDislikedList($_POST["video_id"])) {
            // if liked before, then delete record form liked_list
            $response['status'] = $userObj->deleteRecordFromDislikedList($_POST["video_id"]);
        } else {
            //  if not liked before, then insert record into liked_list
            $response['status'] = $userObj->insertRecordIntoDislikedList($_POST["video_id"]);
        }
        // get likedCount and dislikedCount here
        $query = $conn->prepare("SELECT * FROM liked_list WHERE video_id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->execute();
        $likedCount = $query->rowCount();
        $query = $conn->prepare("SELECT * FROM disliked_list WHERE video_id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->execute();
        $dislikedCount = $query->rowCount();
        $response['data'] = array('likedCount' => $likedCount, 'dislikedCount' => $dislikedCount);
    } else { // if not signIn, do nothing with the database
        $response['status'] = false;
        $response['data'] = 'Not signIn';
    }
    echo json_encode($response);
    exit;
}

// click subscribe button
if (isset($_POST["subscribe"]) && isset($_POST["videoOwnerName"])) {
    if (isset($_SESSION["uid"])) { // user already signIn
        if ($userObj->isSubscribed($_POST["videoOwnerName"])) {
            // if already subscribed, do unsubscribe
            $response['status'] = $userObj->unsubscribe($_POST["videoOwnerName"]);
        } else {
            // if not subscribed, do subscribe
            $response['status'] = $userObj->subscribe($_POST["videoOwnerName"]);
        }
        $response['data'] = $userObj->getSubscribeCountByName($_POST["videoOwnerName"]);
    } else { // user not signIn
        $response['status'] = false;
        $response['data'] = 'Not signIn';
    }
    echo json_encode($response);
    exit;
}

// click comment button, post a new comment
if (isset($_POST["post_comment"]) && isset($_POST["video_id"]) && isset($_POST["user_id"]) && isset($_POST["text"])) {
    if (isset($_SESSION["uid"])) { // user already signIn
        $commentsObj = new CommentHandler($conn, $_POST["video_id"]);
        if ($commentsObj->postComment($_POST)) { // insert comment success
            $response['status'] = true;
            $commentCount = $commentsObj->getCommentsCount();
            $lastInsertedComment = $commentsObj->getLastInsertedCommentByUserId($_POST["user_id"]);
            $lastCommentDiv = $commentsObj->commentsRenderer(array($lastInsertedComment))[0];
            $response['data'] = array('comment_count' => $commentCount, 'my_new_comment' => $lastCommentDiv);
        } else { // failed to insert the new comment
            $response['status'] = false;
            $response['data'] = '';
        }
    } else { // user not sign in
        $response['status'] = false;
        $response['data'] = 'Not signIn';
    }
    echo json_encode($response);
    exit;
}

// click reply button, post a new reply
if (isset($_POST["post_reply"]) && isset($_POST["video_id"]) && isset($_POST["comment_id"]) && isset($_POST["user_id"]) && isset($_POST["text"])) {
    if (isset($_SESSION["uid"])) {
        $commentsObj = new CommentHandler($conn, $_POST["video_id"]);
        if ($commentsObj->postReply($_POST)) { // insert reply success
            $response['status'] = true;
            $lastInsertedReply = $commentsObj->getLastInsertedReplyByUserId($_POST["user_id"]);
            $lastReplyDiv = $commentsObj->repliesRenderer(array($lastInsertedReply))[0];
            $response['data'] = $lastReplyDiv;
        } else {// failed to insert the new reply
            $response['status'] = false;
            $response['data'] = '';
        }
    }else { // user not sign in
        $response['status'] = false;
        $response['data'] = 'Not signIn';
    }
    echo json_encode($response);
    exit;
}

// window scroll to bottom, get 5 more comments
if (isset($_POST["get_comment"]) && isset($_POST["video_id"]) && isset($_POST["page"])) {
    $start = $_POST["page"] * 5;
    $commentsObj = new CommentHandler($conn, $_POST["video_id"]);
    $newComments = $commentsObj->getComments($start, 5);
    if ($newComments) {
        $response['status'] = true;
        $newCommentsDiv = $commentsObj->commentsRenderer($newComments);
        $response['data'] = $newCommentsDiv;
    } else {
        $response['status'] = false;
    }
    echo json_encode($response);
    exit;
}

// if nothing above, sing out
if (isset($_SESSION['uid'])) {
    $accountHandler->signOut();
//    header("Location:index.php");
    echo "<script type='text/javascript'>history.go(-1)</script>";
}
?>


