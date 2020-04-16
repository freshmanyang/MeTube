<?php

class showAllVideo
{
    private $conn, $video, $categoryList, $categorydb, $categoryFilter, $thumbnail, $categoryFilterquery;
    private $allVideoPath = array();
    private $allVideoPathwithBlock = array();

    public function __construct($con)
    {
        $this->conn = $con;
        $query = $this->conn->prepare("SELECT * From videos");
        $query->execute();
        $this->video = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($username)
    {
        $videowithprivacy = $this->checkPrivacy($this->video, $username);
        $this->allVideoPath = [];
        foreach ($videowithprivacy as $key => $value) {
            $filePath = $value["file_path"];
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views = $value["views"];
//            $upload_date = date('Y-m-d H:i:s', $value["upload_date"]);
            $upload_date = date('Y-m-d', strtotime($value["upload_date"]));
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a>";
            array_push($this->allVideoPath, "<div>$videolink
                    <br>
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;");
        }
        return $this->allVideoPath;
    }

    public function categoryFilter($category, $username)
    {
//        if(!strcmp($category,'All')){
//            return   header("Location: index.php");
//
//        }
        $query = $this->conn->prepare("SELECT videos.* From videos inner join category on videos.category = category.id where category.name=:category");
        $query->bindParam(':category', $category);
        $query->execute();
        $this->categoryFilterquery = $query->fetchAll(PDO::FETCH_ASSOC);
        $this->categoryFilterquery = $this->checkPrivacy($this->categoryFilterquery, $username);
        $this->categoryFilter = '';
        foreach ($this->categoryFilterquery as $key => $value) {
            $filePath = $value["file_path"];
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views = $value["views"];
            $upload_date = date('Y-m-d', strtotime($value["upload_date"]));
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a>";
            $this->categoryFilter .= "<div>$videolink<br>                 
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;";
        }
        return $this->categoryFilter;
    }

    private function getthumbnail($videoid)
    {
        $query = $this->conn->prepare("SELECT file_path From thumbnails where video_id =:video_id and selected=1");
        $query->bindParam(':video_id', $videoid);
        $query->execute();
        return $this->thumbnail = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategoryListWithBlock($username)
    {
        $blockUsers = $this->getBlockUsername($username);
        if (empty($blockUsers)) {
            $query = $this->conn->prepare("SELECT * From videos ");
            $query->execute();
            $this->categorydb = $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $qMarks = str_repeat('?,', count($blockUsers) - 1) . '?';
            $query = $this->conn->prepare("SELECT videos.* From videos inner join category on videos.category = category.id where uploaded_by NOT IN ($qMarks)");
            $query->execute($blockUsers);
            $this->categorydb = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        $privacyresult = $this->checkPrivacy($this->categorydb, $username);
        if (!count($privacyresult)) {
            return '';
        }
        $videowithprivacy = array();
        foreach ($privacyresult as $key => $value) {
            array_push($videowithprivacy, $value['category']);
        }
        $qMarks2 = str_repeat('?,', count($videowithprivacy) - 1) . '?';
        $query = $this->conn->prepare("SELECT distinct name From category where id IN ($qMarks2)");
        $query->execute($videowithprivacy);
        $categorynamelist = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categorynamelist as $value) {
            $category = $value["name"];
            $this->categoryList .= "
             <a class='dropdown-item' href='#'>$category</a>
            ";
        }
        return $this->categoryList;
    }

    private function checkBlock($username)
    {
        $query = $this->conn->prepare("SELECT * From contactlist where username=:username and blocked = 1");
        $query->bindParam(':username', $username);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getBlockUsername($username)
    {
        $dbresult = $this->checkBlock($username);
        $blockUsers = array();
        foreach ($dbresult as $key => $value) {
            array_push($blockUsers, $value['mainuser']);
        }
        return $blockUsers;
    }

    private function checkPrivacy($videowithblock, $username)
    {
        foreach ($videowithblock as $value) {
            if ($value['privacy'] == 0) {
                $key = array_search($value, $videowithblock);
                array_splice($videowithblock, $key, 1);
            } elseif ($value['privacy'] == 2) {
                $uploaded_by = $value["uploaded_by"];
                if(!strcmp($username,$uploaded_by)){
                    continue;
                }
                $query = $this->conn->prepare("SELECT * From contactlist where username=:username and mainuser=:mainuser");
                $query->bindParam(':username', $username);
                $query->bindParam(':mainuser', $uploaded_by);
                $query->execute();
                $dbresult = $query->fetch(PDO::FETCH_ASSOC);
                if (strcmp($dbresult['groupname'], 'friends')) {
                    $key = array_search($value, $videowithblock);
                    array_splice($videowithblock, $key, 1);
                }
            }
        }
        return $videowithblock;
    }

    public function createwithBlock($username)
    {
        $blockUsers = $this->getBlockUsername($username);
        if (empty($blockUsers)) {
            return $this->create($username);
        }
        $qMarks = str_repeat('?,', count($blockUsers) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where uploaded_by NOT IN ($qMarks)");
        $query->execute($blockUsers);
        $videoresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $videowithprivacy = $this->checkPrivacy($videoresult, $username);
//        if(empty($videowithprivacy)){
//            return '';
//        }
        $this->allVideoPathwithBlock = [];
        foreach ($videowithprivacy as $key => $value) {
            $filePath = $value["file_path"];
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views = $value["views"];
//            $upload_date = date('Y-m-d H:i:s', $value["upload_date"]);
            $upload_date = date('Y-m-d', strtotime($value["upload_date"]));
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a>";
            array_push($this->allVideoPathwithBlock, "<div>$videolink
                    <br>
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;");
        }
        return $this->allVideoPathwithBlock;
    }

    public function getCategoryVideoswithBlock($username, $category)
    {
//        auto direct to index.php doesnt need below one
//        if(!strcmp($category,'All')){
//            return   $this->createwithBlock($username);
//        }
        $blockUsers = $this->getBlockUsername($username);
        if (empty($blockUsers)) {
            return $this->categoryFilter($category, $username);
        }
        $qMarks = str_repeat('?,', count($blockUsers) - 1) . '?';
        $category = "'" . $category . "'";
        $query = $this->conn->prepare("SELECT videos.* From videos inner join category on videos.category = category.id where category.name=$category and videos.uploaded_by NOT IN ($qMarks)");
        $query->execute($blockUsers);
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $dbresult = $this->checkPrivacy($dbresult, $username);
        $categoryFilterquerywithBlock = '';
        foreach ($dbresult as $value) {
            $filePath = $value["file_path"];
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views = $value["views"];
            $upload_date = date('Y-m-d', strtotime($value["upload_date"]));
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a>";
            $categoryFilterquerywithBlock .= "<div>$videolink<br>                 
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;";
        }
        return $categoryFilterquerywithBlock;
    }
}

?>