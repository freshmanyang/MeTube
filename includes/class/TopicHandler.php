<?php


class TopicHandler
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllTopicsByCommunityId($communityId)
    {
        $query = $this->conn->prepare("SELECT topic.* FROM topic INNER JOIN community_topic ON topic.id=community_topic.topic_id
                                       WHERE community_topic.community_id=:community_id");
        $query->bindParam(":community_id", $communityId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopicById($topicId)
    {
        $query = $this->conn->prepare("SELECT * FROM topic WHERE id=:topic_id");
        $query->bindParam(":topic_id", $topicId);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function postNewTopic($userId, $title, $text)
    {
        $query = $this->conn->prepare("INSERT INTO topic (title, post_by, text, post_date) 
                                       VALUES (:title, :user_id, :text, :post_date)");
        $query->bindParam(":title", $title);
        $query->bindParam(":user_id", $userId);
        $query->bindParam(":text", $text);
        $query->bindValue(":post_date", date("Y-m-d H:i:s"));
        if ($query->execute()) {
            $lastId = $this->conn->lastInsertId();
            $query = $this->conn->prepare("SELECT * FROM topic WHERE id=:lastId LIMIT 1");
            $query->bindValue(":lastId", $lastId);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function bindTopicToCommunity($communityId, $topicId)
    {
        $query = $this->conn->prepare("INSERT INTO community_topic (community_id, topic_id) VALUES (:community_id, :topic_id)");
        $query->bindParam(":community_id", $communityId);
        $query->bindParam(":topic_id", $topicId);
        return $query->execute();
    }

    public function getCommentCountById($topicId)
    {
        $query = $this->conn->prepare("SELECT * from topic_topic_comment WHERE topic_id=:topic_id");
        $query->bindParam(":topic_id", $topicId);
        $query->execute();
        return $query->rowCount();
    }

    public function topicRenderer($topics)
    {
        $elements = [];
        foreach ($topics as $topic) {
            $topicId = $topic['id'];
            $topicTitle = $topic['title'];
            $topicText = $topic['text'];
            $timeDiff = CommentHandler::getTimeDiff($topic['post_date']);
            $postUser = new User($this->conn, $topic['post_by']);
            $postUserName = $postUser->getUsername();

            $commentCount = $this->getCommentCountById($topicId);
            if ($commentCount == 0) {
                $commentCount = '0 Comment';
            } else {
                $commentCount = $commentCount . ' Comments';
            }

            $channelLink = 'channel.php?channel=' . $postUserName;
            $topicLink = 'topic.php?topic_id=' . $topicId;

            $element = "<div class='topic-wrapper'>
                            <a href='$topicLink' target='_blank' class='topic-link' style='display: block'>
                                <div class=\"topic-info\">
                                    <span>posted by</span>
                                    <object><a href=\"$channelLink\" target=\"_blank\" class='user-page-link'>$postUserName</a></object>
                                    <span>• $timeDiff</span>
                                </div>
                                <h3 class=\"topic-title\">$topicTitle</h3>
                                <div class=\"topic-content\">$topicText</div>
                                <div class=\"comment-count\">
                                    <i class=\"iconfont icon-comment-filled\"></i>
                                    <span>$commentCount</span>
                                </div>
                            </a>
                        </div>";

            array_push($elements, $element);
        }
        return $elements;
    }

    public function getAllCommentsById($topicId)
    {
        $query = $this->conn->prepare("SELECT topic_topic_comment.topic_id, topic_comment.* FROM topic_topic_comment
                                      INNER JOIN topic_comment ON topic_topic_comment.topic_comment_id=topic_comment.id
                                      WHERE topic_topic_comment.topic_id=:topic_id ORDER BY topic_comment.comment_date DESC");
        $query->bindParam(":topic_id", $topicId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function postNewComment($userId, $text)
    {
        $query = $this->conn->prepare("INSERT INTO topic_comment (comment_by, text, comment_date) 
                                       VALUES (:comment_by, :text, :comment_date)");
        $query->bindParam(":comment_by", $userId);
        $query->bindParam(":text", $text);
        $query->bindValue(":comment_date", date("Y-m-d H:i:s"));
        if ($query->execute()) {
            $lastId = $this->conn->lastInsertId();
            $query = $this->conn->prepare("SELECT * FROM topic_comment WHERE id=:lastId LIMIT 1");
            $query->bindValue(":lastId", $lastId);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function bindCommentToTopic($topicId, $commentId){
        $query = $this->conn->prepare("INSERT INTO topic_topic_comment (topic_id, topic_comment_id) VALUES (:topic_id, :comment_id)");
        $query->bindParam(":comment_id", $commentId);
        $query->bindParam(":topic_id", $topicId);
        return $query->execute();
    }

    public function commentRenderer($comments)
    {
        $elements = [];
        foreach ($comments as $comment) {
            $commentId = $comment['id'];
            $commentText = $comment['text'];
            $timeDiff = CommentHandler::getTimeDiff($comment['comment_date']);
            $commentUser = new User($this->conn, $comment['comment_by']);
            $commentUserId = $commentUser->getUserId();
            $commentUserName = $commentUser->getUsername();
            $commentUserAvatar = $commentUser->getAvatarPath();
            $channelLink = 'channel.php?channel=' . $commentUserName;

            $element = "<div class='comment-box' comment-id='$commentId'>
                            <a href='$channelLink' class='user-page-link'>
                                <img src='$commentUserAvatar' alt='' user-id='$commentUserId'>
                            </a>
                            <div class='comment-renderer'>
                                <div class='comment-info-header'>
                                    <a href='$channelLink' class='user-page-link'>
                                        <span class='comment-user-name'>$commentUserName</span>
                                    </a>
                                    <span class='comment-time-span'>• $timeDiff</span>
                                </div>
                                <p class='comment-text'>$commentText</p>
                            </div>
                        </div>";
            array_push($elements, $element);
        }
        return $elements;
    }
}