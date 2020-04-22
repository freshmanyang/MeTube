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
        if ($query->execute()) {
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

    private function getThumbnailPathById($videoId)
    {
        $query = $this->conn->prepare("SELECT file_path FROM thumbnails WHERE video_id=:videoId AND selected=1");
        $query->bindParam(":videoId", $videoId);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC)['file_path'];
    }

    private function getKeywordIdArray()
    {
        $videoId = $this->getVideoId();
        $query = $this->conn->prepare("SELECT keyword_id FROM video_keyword WHERE video_id=:video_id");
        $query->bindParam(":video_id", $videoId);
        $query->execute();
        $result = $query->fetchAll();
        $keywordIDArray = array();
        foreach ($result as $k) {
            array_push($keywordIDArray, (int)$k['keyword_id']);
        }
        return $keywordIDArray;
    }

    public function getRecommendationVideos($start, $count)
    {
        $keywordIDArrayString = "(" . implode(',', array_map('intval', $this->getKeywordIdArray())) . ")";
        $query = $this->conn->prepare("SELECT
                                            COUNT(M.id) AS id_count,
                                            M.views + M.like - M.dislike AS widget,
                                            M.id,
                                            M.uploaded_by,
                                            M.title,
                                            M.privacy,
                                            M.category,
                                            M.video_duration,
                                            M.views,
                                            M.upload_date
                                        FROM
                                            (
                                            SELECT
                                                videos.*,
                                                video_keyword.keyword_id
                                            FROM
                                                videos
                                            INNER JOIN video_keyword ON videos.id = video_keyword.video_id
                                            WHERE
                                                videos.id != :video_id AND(
                                                    video_keyword.keyword_id IN $keywordIDArrayString AND videos.category = :category
                                                )
                                            UNION
                                            SELECT
                                                videos.*,
                                                video_keyword.keyword_id
                                            FROM
                                                videos
                                            INNER JOIN video_keyword ON videos.id = video_keyword.video_id
                                            WHERE
                                                videos.id != :video_id AND(
                                                    video_keyword.keyword_id IN $keywordIDArrayString AND videos.category != :category
                                                )
                                            UNION
                                            SELECT
                                                videos.*,
                                                video_keyword.keyword_id = 0
                                            FROM
                                                videos
                                            INNER JOIN video_keyword ON videos.id = video_keyword.video_id
                                            WHERE
                                                videos.id != :video_id AND(
                                                    video_keyword.keyword_id NOT IN $keywordIDArrayString AND videos.category = :category
                                                )
                                        ) AS M
                                        WHERE M.privacy != 0
                                        GROUP BY M.id
                                        ORDER BY id_count DESC, widget DESC LIMIT $start, $count "); // search 5 records each time
        $query->bindValue(":video_id", $this->getVideoId());
        $query->bindValue(":category", $this->getCategory());
        if ($query->execute()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return '';
    }

    public function recommendationsVideoRenderer($recoVideos)
    {
        $elements = [];
        foreach ($recoVideos as $recoVideo) {
            $videoId = $recoVideo['id'];
            $videoOwnerName = $recoVideo['uploaded_by'];
            $videoTitle = $recoVideo['title'];
            $videoDuration = $recoVideo['video_duration'];
            $videoViews = $recoVideo['views'];
            $timeDiff = CommentHandler::getTimeDiff($recoVideo["upload_date"]);
            $videoThumbnailPath = $this->getThumbnailPathById($videoId);

            $videoLink = 'watch.php?vid=' . $videoId;

            $element = "<div class='reco-video-wrapper' video-id='$videoId'>
                            <a href='$videoLink' class='left'>
                                <img src='$videoThumbnailPath' alt=''>
                                <span class='reco-video-duration'>$videoDuration</span>
                            </a>
                            <div class='right'>
                                <a href='$videoLink'>
                                    <div class='reco-video-title'>$videoTitle</div>
                                    <div class='reco-video-owner'>$videoOwnerName</div>
                                    <span class='reco-video-views'>$videoViews views</span>
                                    <span class='dot'>•</span>
                                    <span class='reco-video-upload-date'>$timeDiff</span>
                                </a>
                            </div>
                        </div>";
            array_push($elements, $element);
        }
        return $elements;
    }


    private function isBlocked($userName, $videoOwnerName)
    {
        $query = $this->conn->prepare("SELECT * FROM contactlist WHERE mainuser=:mainuser AND username=:username AND blocked=1 LIMIT 1");
        $query->bindParam(":mainuser", $videoOwnerName);
        $query->bindParam(":username", $userName);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    private function isFriend($userName, $videoOwnerName)
    {
        $query = $this->conn->prepare("SELECT * FROM contactlist WHERE mainuser=:mainuser AND username=:username AND groupname='friends' LIMIT 1");
        $query->bindParam(":mainuser", $videoOwnerName);
        $query->bindParam(":username", $userName);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    private function isFamily($userName, $videoOwnerName)
    {
        $query = $this->conn->prepare("SELECT * FROM contactlist WHERE mainuser=:mainuser AND username=:username AND groupname='family' LIMIT 1");
        $query->bindParam(":mainuser", $videoOwnerName);
        $query->bindParam(":username", $userName);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function checkVideoAuth()
    {
        if (!isset($_SESSION['uid'])) { // user not login
            if ($this->getPrivacyStatus() == 1) {
                // video privacy is public
                return true;
            }
        } else {  // user is login
            $loginUserName = $this->userObj->getUsername();
            $videoOwnerName = $this->getVideoOwnerName();
            if ($loginUserName == $videoOwnerName) {
                // video owner can view his own video
                return true;
            }
            if ($this->getPrivacyStatus() == 1 && !$this->isBlocked($loginUserName, $videoOwnerName)) {
                // a public video can be viewed by all non-blocked user
                return true;
            }
            if($this->getPrivacyStatus() == 2 && $this->isFriend($loginUserName, $videoOwnerName) && !$this->isBlocked($loginUserName, $videoOwnerName)){
                // a friend video can be viewed by all non-blocked friends
                return true;
            }
            if($this->getPrivacyStatus() == 3 && $this->isFamily($loginUserName, $videoOwnerName) && !$this->isBlocked($loginUserName, $videoOwnerName)){
                // a family video can be viewed by all non-blocked family members
                return true;
            }
        }
        return false;
    }
}