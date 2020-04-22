<?php


class CommentHandler
{
    private $conn, $video_id;

    public function __construct($conn, $video_id)
    {
        $this->conn = $conn;
        $this->video_id = $video_id;
    }

    public function getComments($start, $count) // start: search start with nth record
    {
        $query = $this->conn->prepare("SELECT * FROM comments WHERE video_id=:video_id 
                                       ORDER BY comment_date DESC LIMIT $start, $count"); // search 5 records each time
        $query->bindParam(":video_id", $this->video_id);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return '';
    }

    public function getLastInsertedCommentByUserId($user_id)
    {
        $query = $this->conn->prepare("SELECT * FROM comments WHERE user_id=:user_id ORDER BY comment_date DESC LIMIT 1");
        $query->bindParam(":user_id", $user_id);
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return '';
    }

    public function getLastInsertedReplyByUserId($user_id)
    {
        $query = $this->conn->prepare("SELECT * FROM replies WHERE user_id=:user_id ORDER BY comment_date DESC LIMIT 1");
        $query->bindParam(":user_id", $user_id);
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return '';
    }

    public function commentsRenderer($comments)
    {
        $elements = [];
        foreach ($comments as $comment) {
            $commentId = $comment["id"];
            $commentUserId = $comment["user_id"];
            $commentUserInfo = $this->getUserInfoById($commentUserId);
            $commentUserName = $commentUserInfo["username"];
            $commentUserAvatar = $commentUserInfo["avatar_path"];
            $timeDiff = $this->getTimeDiff($comment["comment_date"]);
            $textDiv = $this->createTextDiv($comment["text"]);

            $replyArea = $this->createReplyArea();

            $replies = $this->getRepliesByCommentId($commentId);
            $repliesDivArr = $this->repliesRenderer($replies);
            $repliesDivArrLen = count($repliesDivArr);
            $replyDivStr = '';
            if ($repliesDivArrLen == 1) {
                $replyDivStr = $repliesDivArr[0];
            } elseif ($repliesDivArrLen > 1) {
                $replyDivStr = implode("", $repliesDivArr); // combine array to a string
            }

            $channelLink =  'channel.php?channel='.$commentUserName;

            $element = "<div class='comment-box' comment-id='$commentId'>
                            <a href='$channelLink' class='user-page-link'>
                                <img src='$commentUserAvatar' alt='' user-id='$commentUserId'>
                            </a>
                            <div class='comment-renderer'>
                                <div class='comment-info-header'>
                                    <a href='$channelLink' class='user-page-link'>
                                        <span class='comment-user-name'>$commentUserName</span>
                                    </a>
                                    <span class='comment-time-span'>$timeDiff</span>
                                </div>
                                " . $textDiv . $replyArea . "
                                <div class='replies-wrapper'>" . $replyDivStr . "</div>
                            </div>
                        </div>";
            array_push($elements, $element);
        }
        return $elements;
    }

    public function getCommentsCount()
    {
        $query = $this->conn->prepare("SELECT * FROM comments WHERE video_id=:video_id");
        $query->bindParam(":video_id", $this->video_id);
        if ($query->execute()) {
            return $query->rowCount();
        }

        return '';
    }

    public function getRepliesByCommentId($comment_id)
    {
        $query = $this->conn->prepare("SELECT * FROM replies WHERE comment_id=:comment_id ORDER BY comment_date DESC");
        $query->bindParam(":comment_id", $comment_id);
        if ($query->execute()) {
            return $query->fetchAll();
        }
        return '';
    }

    public function repliesRenderer($replies)
    {
        $elements = [];
        foreach ($replies as $reply) {
            $replyId = $reply["id"];
            $replyUserId = $reply["user_id"];
            $replyUserInfo = $this->getUserInfoById($replyUserId);
            $replyUserName = $replyUserInfo["username"];
            $replyUserAvatar = $replyUserInfo["avatar_path"];
            $timeDiff = $this->getTimeDiff($reply["comment_date"]);
            $textDiv = $this->createTextDiv($reply["text"]);

            $channelLink =  'channel.php?channel='.$replyUserName;


            $element = "<div class='reply-box'>
                            <a href='$channelLink' class='user-page-link'>
                                <img src='$replyUserAvatar' alt='' user-id='$replyUserId' reply-id='$replyId'>
                            </a>
                            <div class='reply-renderer'>
                                <div class='reply-info-header'>
                                    <a href='$channelLink' class='user-page-link'>
                                        <span class='reply-user-name'>$replyUserName</span>
                                    </a>
                                    <span class='reply-time-span'>$timeDiff</span>
                                </div>
                                " . $textDiv . "
                            </div>
                        </div>";
            array_push($elements, $element);
        }
        return $elements;
    }

    public function getUserInfoById($user_id)
    {
        $query = $this->conn->prepare("SELECT username, avatar_path FROM users WHERE id=:user_id LIMIT 1");
        $query->bindParam(":user_id", $user_id);
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return '';
    }

    public function postComment($post)
    {
        $query = $this->conn->prepare("INSERT INTO comments (video_id, user_id, text, comment_date) 
                                 VALUES (:video_id, :user_id, :text, :comment_date)");
        $query->bindValue(":video_id", $post["video_id"]);
        $query->bindValue(":user_id", $post["user_id"]);
        $query->bindValue(":text", $post["text"]);
        $query->bindValue(":comment_date", date("Y-m-d H:i:s"));
        return $query->execute();
    }

    public function postReply($post){
        $query = $this->conn->prepare("INSERT INTO replies (comment_id, user_id, text, comment_date) 
                                 VALUES (:comment_id, :user_id, :text, :comment_date)");
        $query->bindValue(":comment_id", $post["comment_id"]);
        $query->bindValue(":user_id", $post["user_id"]);
        $query->bindValue(":text", $post["text"]);
        $query->bindValue(":comment_date", date("Y-m-d H:i:s"));
        return $query->execute();
    }

    public static function getTimeDiff($timePast)
    {
        $timeNow = new DateTime();
        $timePast = new DateTime($timePast);
        $timeDiff = $timeNow->diff($timePast);
        switch ($timeDiff) {
            case $timeDiff->y > 1:
                $timeDiff = $timeDiff->y . " years ago";
                break;
            case $timeDiff->y == 1:
                $timeDiff = $timeDiff->y . " year ago";
                break;
            case $timeDiff->m > 1:
                $timeDiff = $timeDiff->m . " months ago";
                break;
            case $timeDiff->m == 1:
                $timeDiff = $timeDiff->m . " month ago";
                break;
            case $timeDiff->d > 1:
                $timeDiff = $timeDiff->d . " days ago";
                break;
            case $timeDiff->d == 1:
                $timeDiff = $timeDiff->d . " day ago";
                break;
            case $timeDiff->h > 1:
                $timeDiff = $timeDiff->h . " hours ago";
                break;
            case $timeDiff->h == 1:
                $timeDiff = $timeDiff->h . " hour ago";
                break;
            case $timeDiff->i > 1:
                $timeDiff = $timeDiff->i . " minutes ago";
                break;
            case $timeDiff->i == 1:
                $timeDiff = $timeDiff->i . " minute ago";
                break;
            case $timeDiff->s > 1:
                $timeDiff = $timeDiff->s . " seconds ago";
                break;
            default:
                $timeDiff = "1 second ago";
                break;
        }
        return $timeDiff;
    }

    public static function createTextDiv($text)
    {
        $text = nl2br($text);
        if (substr_count($text, '<br>') >= 3) {
            $textCopy = $text;
            $pos_val = 0;
            for ($i = 1; $i <= 3; $i++) {
                $pos = strpos($textCopy, '<br>');
                $textCopy = substr($textCopy, $pos + 1);
                $pos_val = $pos + $pos_val + 1;
            }
            $pos_val = $pos_val - 1;
            $c = substr($text, 0, $pos_val);
            $h = substr($text, $pos_val);
            return "<p class='content-text'>$c<span id='dots'>...</span><span id='more'>$h</span></p>
                    <span class='show-more-btn' id='show_more_btn'>SHOW MORE</span>";
        } elseif (strlen($text) > 100) {
            $c = substr($text, 0, 100);
            $h = substr($text, 100);
            return "<p class='content-text'>$c<span id='dots'>...</span><span id='more'>$h</span></p>
                    <span class='show-more-btn' id='show_more_btn'>SHOW MORE</span>";
        } elseif (strlen($text) > 0 || substr_count($text, '<br>') > 0) {
            return "<p class='content-text'>$text</p>";
        } else {
            return '';
        }
    }

    public function createReplyArea(){
        if(!isset($_SESSION['uid'])){
            return '';
        }
        $loginUserId = $_SESSION['uid'];
        $loginUserInfo = $this->getUserInfoById($loginUserId);
        $loginUserAvatar = $loginUserInfo["avatar_path"];
        $loginUserName = $loginUserInfo["username"];

        $channelLink =  'channel.php?channel='.$loginUserName;

        return "<span class='reply-btn' id='reply_btn'>REPLY</span>
                <div class='reply-box' style='display: none'>
                    <a href='$channelLink' class='user-page-link'>
                        <img src='$loginUserAvatar' alt='' user-id='$loginUserId'>
                    </a>
                    <div class='reply-editor-wrapper'>
                        <div id='reply_editor' contenteditable='true'>Add a public reply...</div>
                        <div class='button-wrapper'>
                            <button class='btn btn-default btn-sm' id='cancel_reply'>CANCEL</button>
                            <button class='btn btn-secondary btn-sm' id='submit_reply' disabled>REPLY</button>
                        </div>
                    </div>
                </div>";
    }
}