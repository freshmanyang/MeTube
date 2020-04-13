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
        return  $query->fetch(PDO::FETCH_ASSOC);

    }
    private function getcategoryname($category){
        $query = $this->conn->prepare("SELECT * From category where id=:id");
        $query->bindParam(':id', $category);
        $query->execute();
        return  $query->fetch(PDO::FETCH_ASSOC);
    }
    private function getAllVideoID($blockuser){
        if(empty($blockuser)){
            $query = $this->conn->prepare("SELECT id From videos ");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        else{
        $qMarks = str_repeat('?,', count($blockuser) - 1) . '?';
        $query = $this->conn->prepare("SELECT id From videos where uploaded_by NOT IN ($qMarks) ");
        $query->execute($blockuser);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        $videoIDList=array();
        foreach($result as $value){
            $videoIDList[] = $value['id'];
        }

        return $videoIDList;
    }
    private function getAllVideofromid($videoidlist){
        if(empty($videoidlist)){return [];}
        $qMarks = str_repeat('?,', count($videoidlist) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where id in ($qMarks)");
        $query->execute($videoidlist);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function showAdvancedSearch($usernameLoggedIn,$keyword,$file_size_min,$file_size_max){
        $blockUsers ='';
        if ($usernameLoggedIn) {
            $blockUsers = $this->getBlockUsername($usernameLoggedIn);
        }
        if(!empty($keyword)){
            $videoidlist = $this->getVideoIdFromKeyword($keyword);
        }
        else{
            $videoidlist = $this->getAllVideoID($blockUsers);
        }

        if(!empty($blockUsers)){
            $videoidlist = $this->removeBlockVideo($blockUsers,$videoidlist);
        }
//        var_dump($videoidlist);


        if(! (empty($file_size_min) && empty($file_size_max))){
            $videoidlist = $this->fileSizeFilter($videoidlist,$file_size_min,$file_size_max);
        }

        $fileSizeFilter = $this->getAllVideofromid($videoidlist);




        $filterAllDone = $this->checkPrivacy($fileSizeFilter,$usernameLoggedIn);
        foreach ($filterAllDone as $value) {
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $description = $value["description"];
            $views = $value["views"];
            $upload_date = $value["upload_date"];
            $videoid = $value["id"];
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
                    <div id='videocontent'><h3>$title</h3><ul><li>$uploaded_by</li> <li>$views views</li><li>$upload_date</li> </ul>
                     <ul><li>Catrgory:$categoryname </li><li>Duration:$duration </li> <li> Size:$flieSize M</li></ul>
                    <p>Description:$description</p>
                    </div>                
                    </div>");
        }
//        var_dump($this->adVancedSearchVideolist);
        return $this->adVancedSearchVideolist;
//        check_privacy
    }
    private function fileSizeFilter($videoidlist,$file_size_min,$file_size_max){

        if(empty($file_size_min) && !empty($file_size_max) ){
            $file_size_min = 0;
        }
        if(empty($file_size_max) && !empty($file_size_min)){
            $file_size_max = 500;
        }
        $file_size_min *= 1024*1024;
        $file_size_max *= 1024*1024;
        $qMarks = str_repeat('?,', count($videoidlist) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where  id  IN ($qMarks) and file_size between $file_size_min AND $file_size_max ");
        $query->execute($videoidlist);
        $filesizeresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $finalVideoIDlist = array();
        foreach($filesizeresult as $value){
            $finalVideoIDlist[] = $value["id"];
        }
        return $finalVideoIDlist;
    }
    private function getVideoIdFromKeyword($keyword){
        $keywordlist = explode(" ", $keyword);
//        var_dump($keywordlist);
        $relatedVideolist = array();
        $videoidlist = array();
        $keywordidlist = array();
        $relatedkeyworlist =array();
        foreach($keywordlist as $value){
            $keyword = '%'.$value.'%';
            $query = $this->conn->prepare("SELECT distinct video_keyword.* From keyword inner join video_keyword on keyword.keyword_id=video_keyword.keyword_id where keyword.keyword like '$keyword'");
            $query->execute();

            $videorecord = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($videorecord as $value){
                $videoidlist[] = $value["video_id"];
                $keywordidlist[] = $value["keyword_id"];
            }
            $videoidresult = array_diff($videoidlist, $relatedVideolist);
            $relatedVideolist= array_merge($relatedVideolist,$videoidresult);
            $keywordidlist = array_diff($keywordidlist, $relatedkeyworlist);
            $relatedkeyworlist= array_merge($relatedkeyworlist,$keywordidlist);
//            delete duplicate values in array but index will also mess up, so array_values can rearrange index
            $relatedkeyworlist=array_unique($relatedkeyworlist);
            $relatedkeyworlist =array_values($relatedkeyworlist);
        }
//        print_r($relatedkeyworlist);
        //update search times
        if(empty($relatedkeyworlist)){return [];}
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

    private function getBlockUsername($username){
        $dbresult = $this->checkBlock($username);
        $blockUsers = array();
        foreach ($dbresult as $key => $value) {
            array_push($blockUsers, $value['mainuser']);
        }
        return $blockUsers;
    }
    private function removeBlockVideo($blockusers,$relatedVideolist){
        $qMarks = str_repeat('?,', count($blockusers) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos where uploaded_by in ($qMarks)");
        $query->execute($blockusers);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $blockvideoidlist = array();
        foreach($result as $value){
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
    public function showNormalSearch($usernameLoggedIn,$keyword){
        $blockUsers ='';
        if ($usernameLoggedIn){
        $blockUsers = $this->getBlockUsername($usernameLoggedIn);
        }
        if(empty($keyword)){
             $allvideoidlist = $this->getAllVideoID($blockUsers);
             $keywordVideoResult = $this->getAllVideofromid($allvideoidlist);
        }
        else{
        $relatedVideolist = $this->getVideoIdFromKeyword($keyword);
        if(!empty($blockUsers)){
        $relatedVideolist = $this->removeBlockVideo($blockUsers,$relatedVideolist);

        }
//        var_dump($relatedVideolist);
//        fetch video data
        if(empty($relatedVideolist)){return [];}
        $qMarks = str_repeat('?,', count($relatedVideolist) - 1) . '?';
        $query = $this->conn->prepare("SELECT * From videos  where id IN ($qMarks) ");
        $query->execute($relatedVideolist);
        $keywordVideoResult = $query->fetchAll(PDO::FETCH_ASSOC);
        }
//        check privacy
        $keywordVideoResult = $this->checkPrivacy($keywordVideoResult,$usernameLoggedIn);

        foreach($keywordVideoResult as $value){
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $description = $value["description"];
            $views = $value["views"];
            $upload_date = $value["upload_date"];
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $category = $value['category'];
            $categoryname = $this->getcategoryname($category);
            $categoryname = $categoryname['name'];
            $flieSize = round($value['file_size'] / 1024 / 1024, 2);
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a>";
            array_push($this->searchVideolist,
                "<div id='singlevideo'>$videolink             
                    <div id='videocontent'><h3>$title</h3><ul><li>$uploaded_by</li> <li>$views views</li><li>$upload_date</li> </ul>
                     <ul><li>Catrgory:$categoryname </li><li>Duration:$duration </li> <li> Size:$flieSize M</li></ul>
                    <p>Description:$description</p>
                    </div>                
                    </div>");
        }
        return $this->searchVideolist;
    }
}