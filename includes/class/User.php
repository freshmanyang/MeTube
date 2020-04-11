<?php


class User
{
    private $conn, $userData, $uid;

    public function __construct($conn, $uid)
    {
        $this->conn = $conn;
        $this->uid = $uid;
        $query = $conn->prepare("SELECT * FROM users WHERE id=:uid LIMIT 1");
        $query->bindParam(":uid", $uid);
        $query->execute();
        $this->userData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserId()
    {
        return $this->userData["id"];
    }

    public function getFirstName()
    {
        return $this->userData["first_name"];
    }

    public function setFirstName($firstName)
    {
        $query = $this->conn->prepare("UPDATE users SET first_name=:first_name WHERE id=:uid");
        $query->bindParam(":first_name", $firstName);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["first_name"] = $firstName;
            return true;
        }
        return false;
    }

    public function getLastName()
    {
        return $this->userData["last_name"];
    }

    public function setLastName($lastName)
    {
        $query = $this->conn->prepare("UPDATE users SET last_name=:last_name WHERE id=:uid");
        $query->bindParam(":last_name", $lastName);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["last_name"] = $lastName;
            return true;
        }
        return false;
    }


    public function getUsername()
    {
        return $this->userData["username"];
    }

    public function setUsername($username)
    {
        $query = $this->conn->prepare("UPDATE users SET username=:username WHERE id=:uid");
        $query->bindParam(":username", $username);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["username"] = $username;
            return true;
        }
        return false;
    }

    public function getEmail()
    {
        return $this->userData["email"];
    }

    public function getPassword()
    {
        return $this->userData["password"];
    }

    public function setPassword($encrypted_password)
    {
        $query = $this->conn->prepare("UPDATE users SET password=:password WHERE id=:uid");
        $query->bindParam(":password", $encrypted_password);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["password"] = $encrypted_password;
            return true;
        }
        return false;
    }

    public function getAvatarPath()
    {
        return $this->userData["avatar_path"];
    }

    public function setAvatarPath($imageData)
    {
        $avatarUploadObj = new AvatarUpload($this->conn, $imageData, $this->uid);
        if ($avatarUploadObj->upload()) {
            $this->userData["avatar_path"] = $avatarUploadObj->getFilePath();
            return true;
        }
        return false;
    }

    public function getBirthday()
    {
        return $this->userData['birthday'];
    }

    public function setBirthday($birthday)
    {
        $query = $this->conn->prepare("UPDATE users SET birthday=:birthday WHERE id=:uid");
        $query->bindParam(":birthday", $birthday);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["birthday"] = $birthday;
            return true;
        }
        return false;
    }

    public function getGender()
    {
        return $this->userData['gender'];
    }

    public function setGender($gender)
    {
        $query = $this->conn->prepare("UPDATE users SET gender=:gender WHERE id=:uid");
        $query->bindParam(":gender", $gender);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["gender"] = $gender;
            return true;
        }
        return false;
    }

    // if download a video, insert record into download_list
    public function insertRecordIntoDownloadList($video_id)
    {
        $query = $this->conn->prepare("INSERT IGNORE INTO download_list (video_id, user_id)
                                            VALUES (:video_id, :user_id)");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        return $query->execute();
    }

    // check if user liked this video
    public function hasRecordInLikedList($video_id)
    {
        $query = $this->conn->prepare("SELECT video_id FROM liked_list WHERE user_id=:user_id AND video_id=:video_id LIMIT 1");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['video_id'];
    }

    // click like button, insert record into liked_list
    public function insertRecordIntoLikedList($video_id)
    {
        if ($this->hasRecordInDislikedList($video_id)) {
            if (!$this->deleteRecordFromDislikedList($video_id)){
                return false;
            }
        }
        $query = $this->conn->prepare("INSERT INTO liked_list (video_id, user_id)
                                            VALUES (:video_id, :user_id)");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        return $query->execute();
    }

    // user can also cancel the like operation
    public function deleteRecordFromLikedList($video_id)
    {
        $query = $this->conn->prepare("DELETE FROM liked_list WHERE video_id=:video_id AND user_id=:user_id");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        return $query->execute();
    }

    // check if user disliked this video
    public function hasRecordInDislikedList($video_id)
    {
        $query = $this->conn->prepare("SELECT video_id FROM disliked_list WHERE user_id=:user_id AND video_id=:video_id LIMIT 1");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['video_id'];
    }

    // click like button, insert record into disliked_list
    public function insertRecordIntoDislikedList($video_id)
    {
        if ($this->hasRecordInLikedList($video_id)) {
            if(!$this->deleteRecordFromLikedList($video_id)){
                return false;
            }
        }
        $query = $this->conn->prepare("INSERT INTO disliked_list (video_id, user_id)
                                            VALUES (:video_id, :user_id)");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        return $query->execute();
    }

    // user can also cancel the dislike operation
    public function deleteRecordFromDislikedList($video_id)
    {
        $query = $this->conn->prepare("DELETE FROM disliked_list WHERE video_id=:video_id AND user_id=:user_id");
        $query->bindParam(":video_id", $video_id);
        $query->bindParam(":user_id", $this->uid);
        return $query->execute();
    }

    // click subscribe button to subscribe a user
    public function subscribe($user_name_to_subscribe){
        $query = $this->conn->prepare("INSERT INTO subscriptions (username, Subscriptions)
                                            VALUES (:username, :user_name_to_subscribe)");
        $query->bindParam(":username", $this->userData["username"]);
        $query->bindParam(":user_name_to_subscribe", $user_name_to_subscribe);
        return $query->execute();
    }

    public function unsubscribe($user_name_to_subscribe){
        $query = $this->conn->prepare("DELETE FROM subscriptions WHERE username=:username AND Subscriptions=:user_name_to_subscribe");
        $query->bindParam(":username", $this->userData["username"]);
        $query->bindParam(":user_name_to_subscribe", $user_name_to_subscribe);
        return $query->execute();
    }

    // check if a user has already subscribed to another user
    public function isSubscribed($user_name_to_subscribe){
        $query = $this->conn->prepare("SELECT username FROM subscriptions WHERE username=:username AND Subscriptions=:user_name_to_subscribe LIMIT 1");
        $query->bindParam(":username", $this->userData["username"]);
        $query->bindParam(":user_name_to_subscribe", $user_name_to_subscribe);
        $query->execute();
        return $query->rowCount();
    }

    public function getSubscribeCountByName($user_name){
        $query = $this->conn->prepare("SELECT * FROM subscriptions WHERE Subscriptions=:user_name");
        $query->bindParam(":user_name", $user_name);
        $query->execute();
        return $query->rowCount();
    }

}