<?php


class MessageHandler
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function getIdByUsername($username)
    {
        $query = $this->conn->prepare("SELECT id FROM users WHERE username=:username LIMIT 1");
        $query->bindParam(":username", $username);
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC)['id'];
        }
        return false;
    }

    private function getUsernameById($userId)
    {
        $query = $this->conn->prepare("SELECT username FROM users WHERE id=:userId LIMIT 1");
        $query->bindParam(":userId", $userId);
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC)['username'];
        }
        return false;
    }

    private function getAvatarPathById($userId)
    {
        $query = $this->conn->prepare("SELECT avatar_path FROM users WHERE id=:userId LIMIT 1");
        $query->bindParam(":userId", $userId);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['avatar_path'];
    }

    public function isBlocked($senderId, $receiverId)
    {
        $senderName = $this->getUsernameById($senderId);
        $receiverName = $this->getUsernameById($receiverId);
        $query = $this->conn->prepare("SELECT * FROM contactlist WHERE mainuser=:mainuser AND username=:username AND blocked=1 LIMIT 1");
        $query->bindParam(":mainuser", $receiverName);
        $query->bindParam(":username", $senderName);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function getDialogId($senderId, $receiverId)
    {
        $query = $this->conn->prepare("SELECT id FROM dialog WHERE FIND_IN_SET(:senderId, contact_id_pair) AND FIND_IN_SET(:receiverId, contact_id_pair) LIMIT 1");
        $query->bindParam(":senderId", $senderId);
        $query->bindParam(":receiverId", $receiverId);
        $query->execute();
        $dialogId = $query->fetch(PDO::FETCH_ASSOC)['id'];
        if (!$dialogId) {
            return $this->createDialog($senderId, $receiverId);
        }
        return $dialogId;
    }

    private function getPariedUserId($dialogId, $userId)
    {
        $query = $this->conn->prepare("SELECT contact_id_pair FROM dialog WHERE id=:dialogId LIMIT 1");
        $query->bindParam(":dialogId", $dialogId);
        $query->execute();
        $contactIdPairStr = $query->fetch(PDO::FETCH_ASSOC)['contact_id_pair'];
        $contactIdPairArr = explode(",", $contactIdPairStr);
        if ($contactIdPairArr[0] == $userId) {
            return $contactIdPairArr[1];
        }
        return $contactIdPairArr[0];
    }

    private function createDialog($senderId, $receiverId)
    {
        $contactIdPairString = (string)$senderId . "," . (string)$receiverId;
        $query = $this->conn->prepare("INSERT INTO dialog (contact_id_pair) VALUES (:contactIdPairString)");
        $query->bindParam(":contactIdPairString", $contactIdPairString);
        if ($query->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function createMessage($senderId, $receiverId, $text)
    {
        $dialogId = $this->getDialogId($senderId, $receiverId);
        if ($dialogId) {
            $query = $this->conn->prepare("INSERT INTO message (dialog_id, sender_id, text, upload_date) 
                                           VALUES (:dialogId, :senderId, :text, :uploadDate)");
            $query->bindParam(":dialogId", $dialogId);
            $query->bindParam(":senderId", $senderId);
            $query->bindParam(":text", $text);
            $query->bindValue(":uploadDate", date("Y-m-d H:i:s"));
            if ($query->execute()) {
                // create notification after user send a message to another user
                $lastId = $this->conn->lastInsertId();
                $query = $this->conn->prepare("SELECT * FROM message WHERE id=:lastId LIMIT 1");
                $query->bindValue(":lastId", $lastId);
                $query->execute();
                $lastMessage = $query->fetch(PDO::FETCH_ASSOC);
                $this->createNotification($senderId, $receiverId);
                return $lastMessage;
            }
        }
        return false;
    }

    public function getAllMessagesByDialogId($dialogId)
    {
        $query = $this->conn->prepare("SELECT * FROM message WHERE dialog_id=:dialogId ORDER BY id ASC");
        $query->bindParam(":dialogId", $dialogId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNextMessages($dialogId, $lastMessageId){
        $query = $this->conn->prepare("SELECT * FROM message WHERE dialog_id=:dialogId AND id>:lastMessageId ORDER BY id ASC");
        $query->bindParam(":dialogId", $dialogId);
        $query->bindParam(":lastMessageId", $lastMessageId);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function messageRenderer($messages)
    {
        $elements = [];
        foreach ($messages as $message) {
            $messageId = $message['id'];
            $senderId = $message['sender_id'];
            $dialogId = $message['dialog_id'];
            $text = $message['text'];

            $userId = $_SESSION['uid'];
            $userName = $this->getUsernameById($userId);
            $userAvatarPath = $this->getAvatarPathById($userId);
            $userChannelLink = 'channel.php?channel=' . $userName;

            $pairedUserId = $this->getPariedUserId($dialogId, $userId);
            $pairedUserName = $this->getUsernameById($pairedUserId);
            $pairedUserAvatarPath = $this->getAvatarPathById($pairedUserId);
            $pairedUserChannelLink = 'channel.php?channel=' . $pairedUserName;


            if ($userId != $senderId) {
                // this is a message received from the paired user
                $element = "<div class='receive-message' message-id='$messageId'>
                                <a href='$pairedUserChannelLink' class='user-page-link'>
                                    <img src='$pairedUserAvatarPath' alt='' user-id='$pairedUserId'>
                                </a>
                                <div class='message-box'>$text</div>
                            </div>";
                array_push($elements, $element);
            } else {
                // this is a message send by user
                $element = "<div class='send-message' message-id='$messageId'>
                                <div class='message-box'>$text</div>
                                <a href='$userChannelLink' class='user-page-link'>
                                    <img src='$userAvatarPath' alt='' user-id='$userId'>
                                </a>
                            </div>";
                array_push($elements, $element);
            }
        }
        return $elements;
    }

    private function createNotification($senderId, $receiverId)
    {
        $dialogId = $this->getDialogId($senderId, $receiverId);
        if ($dialogId) {
            $query = $this->conn->prepare("INSERT INTO notification (receiver_id, sender_id, dialog_id, create_date) 
                                           VALUES (:receiverId, :senderId, :dialogId, :createDate)");
            $query->bindParam(":receiverId", $receiverId);
            $query->bindParam(":senderId", $senderId);
            $query->bindParam(":dialogId", $dialogId);
            $query->bindValue(":createDate", date("Y-m-d H:i:s"));
            return $query->execute();
        }
        return false;
    }

    public function getNotificationsByUserId($userId)
    {
        $query = $this->conn->prepare("SELECT * FROM notification WHERE receiver_id=:userId and read_status=0 ORDER BY id DESC");
        $query->bindParam(":userId", $userId);
        $query->execute();
        return $this->notificationRenderer($query->fetchAll(PDO::FETCH_ASSOC));
    }

    public function notificationRenderer($notifications)
    {
        $elements = [];
        foreach ($notifications as $notification) {
            $senderId = $notification['sender_id'];
            $senderName = $this->getUsernameById($senderId);
            $dialogId = $notification['dialog_id'];
            $notificationId = $notification['id'];
            $timeDiff = CommentHandler::getTimeDiff($notification['create_date']);

            $element = "<div class='mail-notification-content' id='mail_notification_content' sender-id='$senderId' notification-id='$notificationId'>
                            <span class='sender-name'>@$senderName</span>
                            <span class='text'> send you a message</span>
                            <span class='time-diff'>• $timeDiff</span>
                        </div>";

            array_push($elements, $element);
        }
        return $elements;
    }

    public function deleteNotificationById($notificationId)
    {
        $query = $this->conn->prepare("DELETE FROM notification WHERE id=:notificationId");
        $query->bindParam(":notificationId", $notificationId);
        return $query->execute();
    }

    public function setReadStatus($dialogId, $userId)
    {
        $query = $this->conn->prepare("UPDATE notification SET `read_status`=1 WHERE dialog_id=:dialogId AND receiver_id=:userId");
        $query->bindParam(":dialogId", $dialogId);
        $query->bindParam(":userId", $userId);
        return $query->execute();
    }

}
