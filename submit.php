<?php
require_once("includes/config.php");
require_once("includes/class/AccountHandler.php");
require_once("includes/class/MessageHandler.php");
require_once("includes/class/CommentHandler.php");
require_once("includes/class/AvatarUpload.php");
require_once("includes/class/User.php");
require_once("includes/class/Video.php");
require_once("includes/class/CommunityHandler.php");
require_once("includes/class/TopicHandler.php");

$accountHandler = new AccountHandler($conn);
$messageHandler = new MessageHandler($conn);
$communityHandler = new CommunityHandler($conn);
$topicHandler = new TopicHandler($conn);
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
        // update likedCount and dislikedCount to videos table
        $query = $conn->prepare("UPDATE videos SET `like` =:likedCount WHERE id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->bindValue(":likedCount", $likedCount);
        $query->execute();
        $query = $conn->prepare("UPDATE videos SET dislike =:dislikedCount WHERE id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->bindValue(":dislikedCount", $dislikedCount);
        $query->execute();
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
        // update likedCount and dislikedCount to videos table
        $query = $conn->prepare("UPDATE videos SET `like` =:likedCount WHERE id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->bindValue(":likedCount", $likedCount);
        $query->execute();
        $query = $conn->prepare("UPDATE videos SET dislike =:dislikedCount WHERE id=:video_id");
        $query->bindParam(":video_id", $video_id);
        $query->bindValue(":dislikedCount", $dislikedCount);
        $query->execute();
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
    } else { // user not sign in
        $response['status'] = false;
        $response['data'] = 'Not signIn';
    }
    echo json_encode($response);
    exit;
}

// get 5 more comments
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

// get 5 more recommend videos
if (isset($_POST["get_recommendation"]) && isset($_POST["video_id"]) && isset($_POST["page"])) {
    $start = $_POST["page"] * 5;
    $videoObj = new Video($conn, $_POST["video_id"], '');
    $newRecommendationVideos = $videoObj->getRecommendationVideos($start, 5);
    if ($newRecommendationVideos) {
        $response['status'] = true;
        $newRecommendationVideosDiv = $videoObj->recommendationsVideoRenderer($newRecommendationVideos);
        $response['data'] = $newRecommendationVideosDiv;
    } else {
        $response['status'] = false;
    }
    echo json_encode($response);
    exit;
}

// update notifications
if (isset($_POST["update_notifications"]) && isset($_POST["user_id"])) {
    $response['data'] = $messageHandler->getNotificationsByUserId($_POST["user_id"]);
    $response['status'] = true;
    echo json_encode($response);
    exit;
}

// get all messages for paired users
if (isset($_POST["request_messages"]) && isset($_POST["paired_user_id"]) && isset($_POST["user_id"])) {
    $dialogId = $messageHandler->getDialogId($_POST["paired_user_id"], $_POST["user_id"]);
    $messageHandler->setReadStatus($dialogId, $_POST["user_id"]);
    $response['status'] = true;
    $messages = $messageHandler->getAllMessagesByDialogId($dialogId);
    $response['data'] = $messageHandler->messageRenderer($messages);
    echo json_encode($response);
    exit;
}

// get next messages for paired users
if (isset($_POST["request_next_messages"]) && isset($_POST["paired_user_id"]) && isset($_POST["user_id"]) && isset($_POST["last_message_id"])) {
    $dialogId = $messageHandler->getDialogId($_POST["paired_user_id"], $_POST["user_id"]);
    $messageHandler->setReadStatus($dialogId, $_POST["user_id"]);
    $response['status'] = true;
    $messages = $messageHandler->getNextMessages($dialogId, $_POST["last_message_id"]);
    $response['data'] = $messageHandler->messageRenderer($messages);
//    $response['data'] = array($_POST["paired_user_id"], $_POST["user_id"], $_POST["last_message_id"]);
    echo json_encode($response);
    exit;
}

// user send message to another user
if (isset($_POST["send_message"]) && isset($_POST["sender_id"]) && isset($_POST["receiver_id"]) && isset($_POST["text"])) {
    if ($messageHandler->isBlocked($_POST["sender_id"], $_POST["receiver_id"])) {
        // if user is blocked, return false
        $response['status'] = false;
        $response['data'] = 'blocked';
        echo json_encode($response);
        exit;
    }
    $sendMessage = $messageHandler->createMessage($_POST["sender_id"], $_POST["receiver_id"], $_POST["text"]);
    if ($sendMessage) {
        $response['data'] = $messageHandler->messageRenderer(array($sendMessage));
        $response['status'] = true;
    } else {
        $response['data'] = '';
        $response['status'] = false;
    }
    echo json_encode($response);
    exit;
}

// user create a new community
if (isset($_POST["create_community"]) && isset($_POST["community_name"]) && isset($_POST["user_id"])) {
    $insertedCommunity = $communityHandler->createCommunity($_POST["community_name"]);
    if ($insertedCommunity) {
        $response['status'] = $communityHandler->joinCommunity($insertedCommunity['id'], $_POST["user_id"]);
        $response['data'] = $communityHandler->communityRenderer(array($insertedCommunity));
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// user join a community
if (isset($_POST["join_community"]) && isset($_POST["community_id"]) && isset($_POST["user_id"])) {
    $response['status'] = $communityHandler->joinCommunity($_POST["community_id"], $_POST["user_id"]);
    echo json_encode($response);
    exit;
}

// create a new topic
if (isset($_POST["create_topic"]) && isset($_POST["community_id"]) && isset($_POST["user_id"]) && isset($_POST["title"]) && isset($_POST["text"])) {
    $postedTopic = $topicHandler->postNewTopic($_POST["user_id"], $_POST["title"], nl2br($_POST["text"]));
    if ($postedTopic) {
        $response['status'] = $topicHandler->bindTopicToCommunity($_POST["community_id"], $postedTopic['id']);
        $response['data'] = $topicHandler->topicRenderer(array($postedTopic));
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// leave a new comment to a topic
if (isset($_POST["post_comment"]) && isset($_POST["topic_id"]) && isset($_POST["user_id"]) && isset($_POST["text"])) {
    $postedComment = $topicHandler->postNewComment($_POST["user_id"], $_POST["text"]);
    if ($postedComment) {
        $response['status'] = $topicHandler->bindCommentToTopic($_POST["topic_id"], $postedComment["id"]);
        $commentDiv = $topicHandler->commentRenderer(array($postedComment))[0];
        $commentCount = $topicHandler->getCommentCountById($_POST["topic_id"]);
        $response['data'] = array('comment_div' => $commentDiv, 'comment_count' => $commentCount);
    } else {
        $response['status'] = false;
        $response['data'] = '';
    }
    echo json_encode($response);
    exit;
}

// if nothing above, sing out
if (isset($_SESSION['uid'])) {
    $accountHandler->signOut();
    $url = $_SERVER['HTTP_REFERER'];
    if (strpos($url, 'watch.php') === false && strpos($url, 'community.php') === false &&
        strpos($url, 'topic.php') === false) {
        header("Location:index.php");
    } else {
        echo "<script type='text/javascript'>history.go(-1)</script>";
    }
}
?>


