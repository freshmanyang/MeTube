<?php


class Video
{
    private $conn, $videoData, $userObj;

    public function __construct($conn, $input, $userObj)
    {
        $this->conn = $conn;
        $this->userObj = $userObj;
        if (is_array($input)) {
            $this->videoData = $input;
        } else { // if not array, thus is vid
            $query = $this->conn->prepare("SELECT * FROM videos WHERE id=:id");
            $query->bindParam(":id", $input);
            $query->execute();
            $this->videoData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function getVideoId()
    {
        return $this->videoData['id'];
    }

    public function getUserId()
    {
        $query = $this->conn->prepare("SELECT users.id FROM users INNER JOIN videos ON 
                                       videos.uploaded_by=users.username WHERE videos.id=:video_id");
        $query->bindParam(":video_id", $this->videoData['id']);
        if($query->execute()){
            return $query->fetch(PDO::FETCH_ASSOC)['id'];
        }
        return '';
    }

    public function getTitle()
    {
        return $this->videoData['title'];
    }

    public function getDescription()
    {
        return $this->videoData['description'];
    }

    public function getPrivacyStatus()
    {
        return $this->videoData['privacy'];
    }

    public function getFilePath()
    {
        return $this->videoData['file_path'];
    }

    public function getCategory()
    {
        return $this->videoData['category'];
    }

    public function getUploadDate()
    {
        return $this->videoData['upload_date'];
    }

    public function getViews()
    {
        return $this->videoData['views'];
    }

    public function incrementView()
    {
        $query = $this->conn->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
        $query->bindValue(":id", $this->getVideoId());
        if ($query->execute()) {
            $this->videoData['views'] += 1;
        }
    }

    public function getVideoDuration()
    {
        return $this->videoData['video_duration'];
    }

    public function getLikedCount()
    {
        $query = $this->conn->prepare("SELECT * FROM liked_list WHERE video_id=:video_id");
        $query->bindParam(":video_id", $this->videoData['id']);
        $query->execute();
        return $query->rowCount();

    }


    public function getDislikedCount()
    {
        $query = $this->conn->prepare("SELECT * FROM disliked_list WHERE video_id=:video_id");
        $query->bindParam(":video_id", $this->videoData['id']);
        $query->execute();
        return $query->rowCount();
    }

    public function getVideoOwnerName()
    {
        $query = $this->conn->prepare("SELECT uploaded_by FROM videos WHERE id=:video_id");
        $query->bindParam(":video_id", $this->videoData['id']);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['uploaded_by'];
    }

    public function getVideoOwnerAvatar()
    {
        $videoOwnerName = $this->getVideoOwnerName();
        $query = $this->conn->prepare("SELECT avatar_path FROM users WHERE username=:username");
        $query->bindParam(":username", $videoOwnerName);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['avatar_path'];
    }
}