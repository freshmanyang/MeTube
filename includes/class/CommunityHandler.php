<?php


class CommunityHandler
{
    private $conn;
    private $count = 0;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function communityNameExist($name)
    {
        $query = $this->conn->prepare("SELECT id from community WHERE community_name=:community_name LIMIT 1");
        $query->bindParam(":community_name", $name);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    private function communityCount(){
        $query = $this->conn->prepare("SELECT id from community");
        $query->execute();
        return $query->rowCount();
    }

    public function createCommunity($name)
    {
        $this->count = $this->communityCount();
        if ($this->communityNameExist($name)) {
            return false;
        }
        $query = $this->conn->prepare("INSERT INTO community (community_name, create_date) VALUES (:community_name, :createDate)");
        $query->bindParam(":community_name", $name);
        $query->bindValue(":createDate", date("Y-m-d H:i:s"));
        if($query->execute()){
            $lastId = $this->conn->lastInsertId();
            $query = $this->conn->prepare("SELECT * FROM community WHERE id=:lastId LIMIT 1");
            $query->bindValue(":lastId", $lastId);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function joinCommunity($communityId, $userId){
        $query = $this->conn->prepare("INSERT INTO community_user (community_id, user_id) VALUES (:community_id, :user_id)");
        $query->bindParam(":community_id", $communityId);
        $query->bindParam(":user_id", $userId);
        return $query->execute();
    }

    public function userInCommunity($communityId, $userId){
        $query = $this->conn->prepare("SELECT * from community_user WHERE community_id=:community_id AND user_id=:user_id LIMIT 1");
        $query->bindParam(":community_id", $communityId);
        $query->bindParam(":user_id", $userId);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        return false;
    }

    public function getAllCommunities()
    {
        $query = $this->conn->prepare("SELECT * from community ORDER BY id");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCommunityById($communityId)
    {
        $query = $this->conn->prepare("SELECT * FROM community WHERE id=:community_id LIMIT 1");
        $query->bindValue(":community_id", $communityId);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getCommunityInfoByTopicId($topicId){
        $query = $this->conn->prepare("SELECT community.* FROM community
                                      INNER JOIN community_topic ON community.id=community_topic.community_id
                                      WHERE community_topic.topic_id=:topic_id LIMIT 1");
        $query->bindParam(":topic_id", $topicId);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function communityRenderer($communities)
    {
        $elements = [];
        foreach ($communities as $community) {
            $this->count += 1;
            $communityId = $community['id'];
            $communityName = $community['community_name'];
            $communityLink = 'community.php?community_id=' . $communityId;
            $element = "<div class='community'>
                            <a href='$communityLink' target='_blank' class='community-link'>
                                <div class='count'>#$this->count</div>
                                <div class='name'>$communityName</div>
                            </a>
                        </div>";
            array_push($elements, $element);
        }
        return $elements;
    }

}