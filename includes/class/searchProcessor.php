<?php

class searchProcessor
{
    private $conn;
    private $searchVideolist = array();
    private $adVancedSearchVideolist = array();

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function getthumbnail($videoid)
    {
        $query = $this->conn->prepare("SELECT file_path From thumbnails where video_id =:video_id and selected=1");
        $query->bindParam(':video_id', $videoid);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function getcategoryname($category)
    {
        $query = $this->conn->prepare("SELECT * From category where id=:id");
        $query->bindParam(':id', $category);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function getcategoryid($categoryname)
    {
        $query = $this->conn->prepare("SELECT * From category where name=:name");
        $query->bindParam(':name', $categoryname);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    private function getAllVideoID($blockuser)
    {
        if (empty($blockuser)) {
            $query = $this->conn->prepare("SELECT id From videos ");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $qMarks = str_repeat('?,', count($blockuser) - 1) . '?';
            $query = $this->conn->prepare("SELECT id From videos where uploaded_by NOT IN ($qMarks) ");
            $query->execute($blockuser);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        $videoIDList = array();
        foreach ($result as $value) {
            $videoIDList[] = $value['id'];
        }
        return $videoIDList;
    }

    private function getAllVideofromid($videoidlist)
    {
        if (empty($videoidlist)) {
            return [];
        }
        $qMarks = str_repeat('?,', count($videoidlist) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where id in ($qMarks)");
        $query->execute($videoidlist);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showAdvancedSearch($usernameLoggedIn, $keyword, $file_size_min, $file_size_max,
                                       $duration_min, $duration_max, $views_min, $views_max,
                                       $uploadDatestart, $uploadDateend, $category, $videoTitle,
                                       $uploadBy, $description)
    {
        $blockUsers = '';
        if ($usernameLoggedIn) {
            $blockUsers = $this->getBlockUsername($usernameLoggedIn);
        }
        if (!empty($keyword)) {
            $videoidlist = $this->getVideoIdFromKeyword($keyword);
        } else {
            $videoidlist = $this->getAllVideoID($blockUsers);
        }
        if (!empty($blockUsers)) {
            $videoidlist = $this->removeBlockVideo($blockUsers, $videoidlist);
        }
        if (!(empty($file_size_min) && empty($file_size_max))) {
            $videoidlist = $this->fileSizeFilter($videoidlist, $file_size_min, $file_size_max);
        }
        if (!(empty($duration_min) && empty($duration_max))) {
            $videoidlist = $this->durationFilter($videoidlist, $duration_min, $duration_max);
        }
        if (!(empty($views_min) && empty($views_max))) {
            $videoidlist = $this->viewsFilter($videoidlist, $views_min, $views_max);
        }
        if (!(empty($uploadDatestart) && empty($uploadDateend))) {
            $videoidlist = $this->uploadDateFilter($videoidlist, $uploadDatestart, $uploadDateend);
        }
        $all = "All";
        if (!(empty($category)) && strcmp($category, $all)) {
            $videoidlist = $this->categoryFilter($videoidlist, $category);
        }
        if (!empty($videoTitle)) {
            $videoidlist = $this->titleFilter($videoidlist, $videoTitle);
        }
        if (!empty($uploadBy)) {
            $videoidlist = $this->uploadbyFilter($videoidlist, $uploadBy);
        }
        if (!empty($description)) {
            $videoidlist = $this->DescriptionFilter($videoidlist, $description);
        }
        $allvideorecords = $this->getAllVideofromid($videoidlist);
        $filterAllDone = $this->checkPrivacy($allvideorecords, $usernameLoggedIn);
        return $this->getDetailHTML($filterAllDone);
    }

    private function DescriptionFilter($videolist, $description)
    {
        $keywordlist = explode(" ", $description);
        $relatedVideolist = array();
        $videoidlist = array();
        $qMarks = str_repeat('?,', count($videolist) - 1) . '?';
        foreach ($keywordlist as $value) {
            $keyword = '%' . $value . '%';
            $query = $this->conn->prepare("SELECT * from videos where id  IN ($qMarks) and description like '$keyword'");
            $query->execute($videolist);
            $videorecord = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($videorecord as $value) {
                $videoidlist[] = $value["id"];
            }
            $videoidresult = array_diff($videoidlist, $relatedVideolist);
            $relatedVideolist = array_merge($relatedVideolist, $videoidresult);
        }
        return $relatedVideolist;
    }

    private function uploadbyFilter($videolist, $uploadBy)
    {
        $keywordlist = explode(" ", $uploadBy);
        $relatedVideolist = array();
        $videoidlist = array();
        $qMarks = str_repeat('?,', count($videolist) - 1) . '?';
        foreach ($keywordlist as $value) {
            $keyword = '%' . $value . '%';
            $query = $this->conn->prepare("SELECT * from videos where id  IN ($qMarks) and uploaded_by like '$keyword'");
            $query->execute($videolist);
            $videorecord = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($videorecord as $value) {
                $videoidlist[] = $value["id"];
            }
            $videoidresult = array_diff($videoidlist, $relatedVideolist);
            $relatedVideolist = array_merge($relatedVideolist, $videoidresult);
        }
        return $relatedVideolist;
    }

    private function getKeywordfromVideoID($videoid)
    {
        $query = $this->conn->prepare("SELECT keyword.* From video_keyword inner join keyword on video_keyword.keyword_id = keyword.keyword_id where video_keyword.video_id=:videoid");
        $query->bindParam(':videoid', $videoid);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $keywordList = '';
        foreach ($result as $value) {
            $keywordList .= $value["keyword"] . '&nbsp;';
        }
        return $keywordList;
    }

    private function getDetailHTML($filterAllDone)
    {
        foreach ($filterAllDone as $value) {
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $description = $value["description"];
            $views = $value["views"];
            $upload_date = $value["upload_date"];
            $videoid = $value["id"];
            $keywordList = $this->getKeywordfromVideoID($videoid);
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $category = $value['category'];
            $categoryname = $this->getcategoryname($category);
            $categoryname = $categoryname['name'];
            $flieSize = round($value['file_size'] / 1024 / 1024, 2);
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a>";
            array_push($this->adVancedSearchVideolist,
                "<div id='singlevideo'>$videolink             
                    <div id='videocontent'><h3>$title</h3>
                    <div><ul><li>$uploaded_by</li> <li>$views views</li><li>$upload_date</li> </ul>
                     <ul><li>Catrgory:$categoryname </li><li>Duration:$duration </li> <li> Size:$flieSize M</li></ul>
                    <ul> <li>Keyword:$keywordList</li></ul></div>
                    <ul id='description'><li>Description:</li>$description</ul>
                    </div>                
                    </div>");
        }
//        var_dump($this->adVancedSearchVideolist);
        return $this->adVancedSearchVideolist;
    }

    private function titleFilter($videolist, $keyword)
    {
        $keywordlist = explode(" ", $keyword);
        $relatedVideolist = array();
        $videoidlist = array();
        $qMarks = str_repeat('?,', count($videolist) - 1) . '?';
        foreach ($keywordlist as $value) {
            $keyword = '%' . $value . '%';
            $query = $this->conn->prepare("SELECT * from videos where id  IN ($qMarks) and title like '$keyword'");
            $query->execute($videolist);
            $videorecord = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($videorecord as $value) {
                $videoidlist[] = $value["id"];
            }
            $videoidresult = array_diff($videoidlist, $relatedVideolist);
            $relatedVideolist = array_merge($relatedVideolist, $videoidresult);
        }
        return $relatedVideolist;
    }

    private function categoryFilter($videolist, $category)
    {
        $categoryid = $this->getcategoryid($category);
        $categoryid = $categoryid["id"];
        $qMarks = str_repeat('?,', count($videolist) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where  id  IN ($qMarks) and category=$categoryid ");
        $query->execute($videolist);
        $filesizeresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $finalVideoIDlist = array();
        foreach ($filesizeresult as $value) {
            $finalVideoIDlist[] = $value["id"];
        }
        return $finalVideoIDlist;
    }

    private function uploadDateFilter($videolist, $uploadDateStart, $uploadDateEnd)
    {
//      original->  2020-04-13T08:00
        $uploadDateStart = strtotime($uploadDateStart);
        $uploadDateEnd = strtotime($uploadDateEnd);
//       strtotime -> 1586779200
        $uploadDateStart = "'" . date("Y-m-d H:i:s", $uploadDateStart) . "'";
        $uploadDateEnd = "'" . date("Y-m-d H:i:s", $uploadDateEnd) . "'";
//       date -> 2020-04-13 08:00:00
//        var_dump($uploadDateStart);
//        var_dump($uploadDateEnd);
        $filterType = 'upload_date';
        return $this->rangeFilter($videolist, $filterType, $uploadDateStart, $uploadDateEnd);
    }

    private function viewsFilter($videoidlist, $min, $max)
    {
        if (empty($min) && !empty($max)) {
            $min = 0;
        }
        if (empty($max) && !empty($min)) {
            $max = 500;
        }
        $filterType = 'views';
        return $this->rangeFilter($videoidlist, $filterType, $min, $max);
    }

    private function durationFilter($videoidlist, $min, $max)
    {
        if (empty($min) && !empty($max)) {
            $min = 0;
        }
        if (empty($max) && !empty($min)) {
            $max = 120;
        }
        $min *= 60;
        $max *= 60;
        $min = "'" . gmstrftime("%H:%M:%S", $min) . "'";
        $max = "'" . gmstrftime("%H:%M:%S", $max) . "'";
        $filterType = 'video_duration';
        return $this->rangeFilter($videoidlist, $filterType, $min, $max);
    }

    private function fileSizeFilter($videoidlist, $file_size_min, $file_size_max)
    {
        if (empty($file_size_min) && !empty($file_size_max)) {
            $file_size_min = 0;
        }
        if (empty($file_size_max) && !empty($file_size_min)) {
            $file_size_max = 500;
        }
        $file_size_min *= 1024 * 1024;
        $file_size_max *= 1024 * 1024;
        $filterType = 'file_size';
        return $this->rangeFilter($videoidlist, $filterType, $file_size_min, $file_size_max);
    }

    private function rangeFilter($videoidlist, $filterType, $min, $max)
    {
        $qMarks = str_repeat('?,', count($videoidlist) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where  id  IN ($qMarks) and $filterType between $min AND $max ");
        $query->execute($videoidlist);
        $filesizeresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $finalVideoIDlist = array();
        foreach ($filesizeresult as $value) {
            $finalVideoIDlist[] = $value["id"];
        }
        return $finalVideoIDlist;
    }

    private function getVideoIdFromKeyword($keyword)
    {
        $keywordlist = explode(" ", $keyword);
//        var_dump($keywordlist);
        $relatedVideolist = array();
        $videoidlist = array();
        $keywordidlist = array();
        $relatedkeyworlist = array();
        foreach ($keywordlist as $value) {
            $keyword = '%' . $value . '%';
            $query = $this->conn->prepare("SELECT distinct video_keyword.* From keyword inner join video_keyword on keyword.keyword_id=video_keyword.keyword_id where keyword.keyword like '$keyword'");
            $query->execute();
            $videorecord = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($videorecord as $value) {
                $videoidlist[] = $value["video_id"];
                $keywordidlist[] = $value["keyword_id"];
            }
            $videoidresult = array_diff($videoidlist, $relatedVideolist);
            $relatedVideolist = array_merge($relatedVideolist, $videoidresult);
            $keywordidlist = array_diff($keywordidlist, $relatedkeyworlist);
            $relatedkeyworlist = array_merge($relatedkeyworlist, $keywordidlist);
//            delete duplicate values in array but index will also mess up, so array_values can rearrange index
            $relatedkeyworlist = array_unique($relatedkeyworlist);
            $relatedkeyworlist = array_values($relatedkeyworlist);
        }
//        print_r($relatedkeyworlist);
        //update search times
        if (empty($relatedkeyworlist)) {
            return [];
        }
        $qMarks = str_repeat('?,', count($relatedkeyworlist) - 1) . '?';
        $query = $this->conn->prepare("update keyword set search_times=search_times+1 where keyword_id IN ($qMarks)");
        $query->execute($relatedkeyworlist);
        return $relatedVideolist;
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

    private function removeBlockVideo($blockusers, $relatedVideolist)
    {
        $qMarks = str_repeat('?,', count($blockusers) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where uploaded_by in ($qMarks)");
        $query->execute($blockusers);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $blockvideoidlist = array();
        foreach ($result as $value) {
            $blockvideoidlist[] = $value["id"];
        }
//        var_dump($blockvideoidlist);
//        var_dump($relatedVideolist);
        $relatedVideolist = array_diff($relatedVideolist, $blockvideoidlist);
        return array_values($relatedVideolist);
    }

    private function checkPrivacy($videowithblock, $username)
    {
        foreach ($videowithblock as $value) {
            if ($value['privacy'] == 0) {
                $key = array_search($value, $videowithblock);
                array_splice($videowithblock, $key, 1);
            } elseif ($value['privacy'] == 2) {
                $uploaded_by = $value["uploaded_by"];
                if (!strcmp($username, $uploaded_by)) {
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

    public function showNormalSearch($usernameLoggedIn, $keyword)
    {
        $blockUsers = '';
        if ($usernameLoggedIn) {
            $blockUsers = $this->getBlockUsername($usernameLoggedIn);
        }
        if (empty($keyword)) {
            $allvideoidlist = $this->getAllVideoID($blockUsers);
            $keywordVideoResult = $this->getAllVideofromid($allvideoidlist);
        } else {
            $relatedVideolist = $this->getVideoIdFromKeyword($keyword);
            if (!empty($blockUsers)) {
                $relatedVideolist = $this->removeBlockVideo($blockUsers, $relatedVideolist);
            }
//        var_dump($relatedVideolist);
//        fetch video data
            if (empty($relatedVideolist)) {
                return [];
            }
            $qMarks = str_repeat('?,', count($relatedVideolist) - 1) . '?';
            $query = $this->conn->prepare("SELECT * From videos  where id IN ($qMarks) ");
            $query->execute($relatedVideolist);
            $keywordVideoResult = $query->fetchAll(PDO::FETCH_ASSOC);
        }
//        check privacy
        $keywordVideoResult = $this->checkPrivacy($keywordVideoResult, $usernameLoggedIn);
        return $this->getDetailHTML($keywordVideoResult);
    }

    public function showHotKeyWord()
    {
        $query = $this->conn->prepare("SELECT * From keyword order by search_times desc LIMIT 3");
        $query->execute();
        $keywordresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $topkeywordlist = array();
        foreach ($keywordresult as $value) {
            $keyword = $value["keyword"];
            $searchTimes = $value['search_times'];
            $keywordlink = "<a href='search.php?search_input=$keyword'>$keyword</a>";
            array_push($topkeywordlist, "
                <div>$keywordlink
                &nbsp;$searchTimes&nbsp;times&nbsp;
                </div>");
        }
        return $topkeywordlist;
    }
}