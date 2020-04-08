<?php



class  channelProcessor
{
    private $conn,$video,$thumbnail,$user,$usernameLoggedIn,$subscribe,$mysubscription,$uservideo;
    private $allVideoPath =array();
    private $subscribePath =array();
    private $signinallVideoPath=array();
    private $sortingVideoPath=array();
    public function __construct($conn,$user,$usernameLoggedIn)
    {
        $this->user=$user;
        $this->conn = $conn;
        $this->usernameLoggedIn =$usernameLoggedIn;
        $query = $this->conn->prepare("SELECT * From videos where uploaded_by=:uploaded_by");
        $query->bindParam(':uploaded_by',$user);
        $query->execute();
        $this->video = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    private function getUserIDfromUsername($username){
        $query = $this->conn->prepare("SELECT id From users where username=:username");
        $query->bindParam(':username',$username);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);

    }
    public function create(){
        foreach ($this->video as $key => $value) {

            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views =$value["views"];
            $upload_date = $value["upload_date"];
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";


            array_push($this->allVideoPath,"<div>$videolink
                   <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;");
        }
        return $this->allVideoPath;
    }

    private function getthumbnail($videoid){
        $query = $this->conn->prepare("SELECT file_path From thumbnails where video_id =:video_id and selected=1");
        $query->bindParam(':video_id',$videoid);
        $query->execute();
        return $this->thumbnail = $query->fetch(PDO::FETCH_ASSOC);

    }
    private function getallthumbnail($videoid){
        $query = $this->conn->prepare("SELECT file_path From thumbnails where video_id =:video_id ");
        $query->bindParam(':video_id',$videoid);
        $query->execute();
        return $this->thumbnail = $query->fetchAll(PDO::FETCH_ASSOC);

    }
    public function createSignIn(){
        foreach ($this->video as $key => $value) {

            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views =$value["views"];
            $upload_date = $value["upload_date"];
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";


            array_push($this->signinallVideoPath,"<div><input type=\"checkbox\" name=\"videoList[]\" value = \"$videoid\">
                    <div>$videolink
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;");
        }

        return $this->signinallVideoPath;
    }
    public function createMySubscriptions(){
        $blockUser =$this->getBlockUsername($this->usernameLoggedIn);
        if (empty($blockUser)){
            $mainUser = "'".$this->usernameLoggedIn."'";
            $query = $this->conn->prepare("SELECT * From subscriptions where username=$mainUser ");
            $query->execute();
            $this->mysubscription = $query->fetchAll(PDO::FETCH_ASSOC);
        }
        else{
        $qMarks = str_repeat('?,', count($blockUser) - 1) . '?';
        $mainUser = "'".$this->usernameLoggedIn."'";
        $query = $this->conn->prepare("SELECT * From subscriptions where username=$mainUser and Subscriptions not in ($qMarks)");
        $query->execute($blockUser);
        $this->mysubscription = $query->fetchAll(PDO::FETCH_ASSOC);
        }


        foreach ($this->mysubscription as $key => $value) {
            $username = ucfirst($value["Subscriptions"]);
//            according subscribe to find video
            $query = $this->conn->prepare("SELECT * From videos where uploaded_by=:uploaded_by");
            $query->bindParam(':uploaded_by',$value["Subscriptions"]);
            $query->execute();
            $this->uservideo = $query->fetchAll(PDO::FETCH_ASSOC);
            $subscribeVideoPath ="";
            $count = 1;
            if($this->uservideo){
            foreach ($this->uservideo as  $value) {
                $title = $value["title"];
                $uploaded_by = $value["uploaded_by"];
                $views = $value["views"];
//                $upload_date = date('Y-m-d H:i:s', $value["upload_date"]);
                $upload_date = $value["upload_date"];
                $videoid = $value["id"];
                $thumbnailpath = $this->getthumbnail($videoid);
                $thumbnailpath = $thumbnailpath["file_path"];
                $duration = $value['video_duration'];
                $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='150' width='230'></a><br>";
                $subscribeVideoPath .= "
                    <div >$videolink
                   <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> ";
                $count++;
//                every subscribe channel only show 4 records
                if($count > 4) {break;}
            }
                array_push($this->subscribePath,"<p><a href=\"channel.php?channel=$username\" target=\"_blank\">$username's Channel </a></p> <div class='video'> $subscribeVideoPath </div>");
            }else{
                array_push($this->subscribePath,"<p><a href=\"channel.php?channel=$username\" target=\"_blank\">$username's Channel </a><p>This channel doesn't have any video yet!</p>");
            }

        }
        return $this->subscribePath;

    }

    public function addsubscribe(){

//      if no login cannot subscibe go back to login page
        if(empty($this->usernameLoggedIn)){
//            return "alert('You are not login, redirect to Login page after click'); location.href = 'index.php';";
            return "You are not login, redirect to Login page after click";

        }
 /*       if(!strcmp($this->usernameLoggedIn,$this->user)) {
            return "You cannot subscribe yourself";
        }*/
        if(!$this->checksubscribe($this->user))
        {
//if no duplicate ,insert to DB
        $query = $this->conn->prepare("INSERT INTO subscriptions (username,Subscriptions) value(:mainuser,:subscriptions)");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':subscriptions',$this->user);
        $query->execute();

//            return 'alert("Subscribe successful")';

        return 'Subscribe Successful';
        }
        else{
            return 'You already subscribed';
        }
    }
    public function unsubscribe(){

//        if no login cannot unsubscibe go back to login page
        if(empty($this->usernameLoggedIn)){
            return "You are not login, redirect to Login page after click";
        }

        if($this->checksubscribe($this->user))
        {

            $query = $this->conn->prepare("DELETE FROM subscriptions WHERE username=:mainUser AND Subscriptions=:subscriptions");
            $query->bindParam(':mainUser', $this->usernameLoggedIn);
            $query->bindParam(':subscriptions',$this->user);
            $query->execute();
//            return 'alert("Unsubscribe successful");location.href = \'channel.php?channel='.$this->user.'\';';
            return 'Unsubscribe Successful';

        }
        else{
            return 'You already unsubscribed';
        }
    }

    public function checksubscribe($channel){
// check if already subscribe
        $query = $this->conn->prepare("Select * from subscriptions where username=:mainuser and Subscriptions=:subscriptions");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':subscriptions',$channel);
        $query->execute();
        $this->subscribe = $query->fetchAll(PDO::FETCH_ASSOC);
       return count($this->subscribe);
    }
    private function deleteFile($filePath)
    {
        if (!unlink($filePath)) {
            return false;
        }
        else{
            return true;
        }
    }
    private function queryDeleteVideoList($deleteList){
        $qMarks = str_repeat('?,', count($deleteList) - 1) . '?';
        $mainUser = "'".$this->usernameLoggedIn."'";
        $query = $this->conn->prepare("Select * from videos WHERE uploaded_by= $mainUser AND id IN ($qMarks)");
        $query->execute($deleteList);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    private function queryPlaylistVideoList($videoList){
        $qMarks = str_repeat('?,', count($videoList) - 1) . '?';
        $query = $this->conn->prepare("Select * from videos WHERE  id IN ($qMarks)");
        $query->execute($videoList);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteVideo($deleteList){
        $deletevideoinfo = $this->queryDeleteVideoList($deleteList);
        foreach ($deletevideoinfo as  $value) {
            $this->deleteFile($value["file_path"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getallthumbnail($videoid);
            foreach($thumbnailpath as $thumbialvalue){
                $thumbnailpath = $thumbialvalue["file_path"];
                $this->deleteFile($thumbnailpath);}

        }

        $qMarks = str_repeat('?,', count($deleteList) - 1) . '?';
        $mainUser = "'".$this->usernameLoggedIn."'";
        $query = $this->conn->prepare("DELETE FROM videos WHERE uploaded_by= $mainUser AND id IN ($qMarks)");
        $query->execute($deleteList);
        $query = $this->conn->prepare("DELETE FROM thumbnails WHERE  video_id IN ($qMarks)");
        $query->execute($deleteList);
    }
    public function deleteVideoinplaylist($deleteList,$deleteplaylist){
//        var_dump($deleteList,$deleteplaylist);
        $qMarks = str_repeat('?,', count($deleteList) - 1) . '?';
        $mainUser = "'".$this->usernameLoggedIn."'";
        $playlist = "'".$deleteplaylist."'";
        $query = $this->conn->prepare("DELETE FROM playlist WHERE mainuser=$mainUser AND playlistname=$playlist AND video_id IN ($qMarks)");
        $query->execute($deleteList);

    }


    private function queryPlayList($playlistname){
        $query = $this->conn->prepare("SELECT * FROM playlist where mainuser=:mainuser and playlistname=:playlistname");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':playlistname',$playlistname);
        $query->execute();
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);
        return $dbresult;
    }
    public function createPlayList($playlistname){
        if(!empty($playlistname)){
        if(!count($this->queryPlayList($playlistname))){

        $query = $this->conn->prepare("INSERT INTO playlist (mainuser,playlistname) value(:mainuser,:playlistname)");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':playlistname',$playlistname);
        $query->execute();
        return "Create Playlist successful";
        }else{return "You cannot add duplicate playlist name";}

        }
        else{
            return "You can not add empty record";
        }

    }

    public function deletePlayList($playlistname){

        foreach ($playlistname as  $playlistnamelist) {

            $deleteplaylistvideoid= array();
            $playlistvideorecord = $this->queryPlayList($playlistnamelist);
            foreach ($playlistvideorecord as  $value) {
                array_push($deleteplaylistvideoid,$value["video_id"]);
            }

            $qMarks = str_repeat('?,', count($deleteplaylistvideoid) - 1) . '?';

            $mainUser = "'".$this->usernameLoggedIn."'";
            $playlistnamelist = "'".$playlistnamelist."'";
            $query = $this->conn->prepare("DELETE FROM playlist WHERE mainuser=$mainUser AND playlistname=$playlistnamelist AND video_id IN ($qMarks)");
            $query->execute($deleteplaylistvideoid);
        }
        return "Delete Playlist successful";
    }
    public function addToFavoriteList($playlistname){
        foreach ($playlistname as  $playlistnamelist) {
            $favoritelistvideoid= array();
            $playlistvideorecord = $this->queryPlayList($playlistnamelist);
            foreach ($playlistvideorecord as  $value) {
                if ($value["video_id"]){
                array_push($favoritelistvideoid,$value["video_id"]);
                }
            }
            $qMarks = str_repeat('?,', count($favoritelistvideoid) - 1) . '?';
            $mainUser = "'".$this->usernameLoggedIn."'";
            $playlistnamelist = "'".$playlistnamelist."'";
            $query = $this->conn->prepare("UPDATE playlist SET favorite = 1 WHERE mainuser=$mainUser and playlistname=$playlistnamelist AND video_id IN ($qMarks)");
            $query->execute($favoritelistvideoid);
        }
        return "Add to FavoriteList Successful";
    }
    public function addSingleVideoToFavoriteList($videoIdList,$playlistnamelist){

            $favoritelistvideoid= array();

            foreach ($videoIdList as  $value) {

                    array_push($favoritelistvideoid,$value);

            }
            $qMarks = str_repeat('?,', count($favoritelistvideoid) - 1) . '?';
            $mainUser = "'".$this->usernameLoggedIn."'";
            $playlistnamelist = "'".$playlistnamelist."'";
            $query = $this->conn->prepare("UPDATE playlist SET favorite = 1 WHERE mainuser=$mainUser and playlistname=$playlistnamelist AND video_id IN ($qMarks)");
            $query->execute($favoritelistvideoid);

        return "Add to FavoriteList Successful";
    }
    public function showFavoriteList(){
        $query = $this->conn->prepare("SELECT * FROM playlist where mainuser=:mainuser and favorite= 1");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->execute();
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);

        $favoritelistvideoid = array();

        foreach ($dbresult as  $value) {
            array_push($favoritelistvideoid, $value['video_id']);
        }

        $qMarks = str_repeat('?,', count($favoritelistvideoid) - 1) . '?';

        $query = $this->conn->prepare("SELECT * FROM videos where id IN ($qMarks)");
        $query->execute($favoritelistvideoid);
        $videolist = $query->fetchAll(PDO::FETCH_ASSOC);
        $favoritelistvideopath =array();
        foreach ($videolist as $value) {
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views = $value["views"];
            $upload_date = $value["upload_date"];
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";
            array_push($favoritelistvideopath, "<div><input type=\"checkbox\" name=\"videoList[]\" value = \"$videoid\">
                    <div>$videolink
                  <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div>&emsp;&emsp;");
        }

        return $favoritelistvideopath;
    }
    public function removeVideoFromFavoriteList($videoIdList){
        $favoritelistvideoid= array();
        foreach ($videoIdList as  $value) {
            array_push($favoritelistvideoid,$value);
        }
        $qMarks = str_repeat('?,', count($favoritelistvideoid) - 1) . '?';
        $mainUser = "'".$this->usernameLoggedIn."'";
        $query = $this->conn->prepare("UPDATE playlist SET favorite = 0 WHERE mainuser=$mainUser  AND video_id IN ($qMarks)");
        $query->execute($favoritelistvideoid);

        return "Remove Videos From FavoriteList Successful";
    }
    private function getVideoInfoViaPlayList($playlist){
        $query = $this->conn->prepare("SELECT * FROM playlist where mainuser=:mainuser and playlistname=:playlistname");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':playlistname', $playlist);
        $query->execute();
        return  $query->fetchAll(PDO::FETCH_ASSOC);

    }
    public function showPlayList(){
        $dbresult = $this->getPlayList();
        $allplaylist ='';
        foreach ($dbresult as  $value) {
            $allplaylist .= '<p><input type="checkbox" name="selectedPlayList[]" value ='.$value["playlistname"].'>';
            $allplaylist .=  '&nbsp<a href="Playlist.php?playlist='.$value["playlistname"].'&channel='.$this->user.'" target="_blank">'.$value["playlistname"].'</a></p>';
            $allVideoid = $this->getVideoInfoViaPlayList($value["playlistname"]);
            $qMarks = str_repeat('?,', count($allVideoid) - 1) . '?';
            $allvideoidarray =array();
            foreach($allVideoid as $value){
               array_push($allvideoidarray,$value['video_id']);
            }

            $query = $this->conn->prepare("select * FROM videos WHERE id IN ($qMarks)");
            $query->execute($allvideoidarray);
            $videoresult = $query->fetchAll(PDO::FETCH_ASSOC);
            $playlistVideoPath = '';
            $count =0;
            if($videoresult){
            foreach ($videoresult as  $value) {
                $title = $value["title"];
                $uploaded_by = $value["uploaded_by"];
                $views = $value["views"];
//                $upload_date = date('Y-m-d H:i:s', $value["upload_date"]);
                $upload_date = $value["upload_date"];
                $videoid = $value["id"];
                $thumbnailpath = $this->getthumbnail($videoid);
                $thumbnailpath = $thumbnailpath["file_path"];
                $duration = $value['video_duration'];
                $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='150' width='230'></a><br>";
                $playlistVideoPath .= "<div>$videolink
                   <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;";
                $count++;
//                every subscribe channel only show 4 record
                if($count > 4) {break;}
            }
            $allplaylist .=  $playlistVideoPath;
            }
            else{
                $allplaylist.='<p> This Playlist doesn\'t have any videos yet!<p>';
            }
        }
        return $allplaylist;
    }
    public function showVideoFromPlaylist($playlistname){
        $playlistvideorecord = $this->queryPlayList($playlistname);
        $videoidfromplaylist =array();


        foreach ($playlistvideorecord as  $value) {
            $videoidfromplaylist[] = $value["video_id"];
       }

        $videoinfofromplaylist = $this->queryPlaylistVideoList($videoidfromplaylist);
        if(!count($videoinfofromplaylist)){
            $deletebutton= "You don't have any videos in this playlist";
            $addToFavoriteListButton ="";
        }else{
            $deletebutton = " <p><input type=\"submit\" class=\"btn btn-danger\" id=\"deletevideoinplaylist\" name = \"deletevideoinplaylist\" value =\"Remove from PlayList\">";
            $addToFavoriteListButton = "&emsp;<input type=\"submit\" class=\"btn btn-outline-info\" id=\"addSingleVideoToFavoriteList\" name = \"addSingleVideoToFavoriteList\" value =\"Add to FavoriteList\"></p>";
        }
        $playlistTitle = '<p>Your are in Playlist - '.$playlistname.'</p>';
        $playlistTitle .="<p><a href=\"channel.php?channel=alan&tab=myPlayList2\">Go back to my PlayList!</a></p>";
        $playlistTitle .="<p><a href=\"channel.php?channel=alan&tab=myFavoriteList2\">Go back to my FavoriteList!</a></p>";
        //        $playlistTitle .= '<form action=\"channelprocess.php?channel='.$this->user.'\" method=\"post\">';
        $playlistTitle .= $deletebutton;
        $playlistTitle .= $addToFavoriteListButton;
        $VideoPathplaylist =array($playlistTitle);
        foreach ($videoinfofromplaylist as  $value) {

            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views =$value["views"];
            $upload_date = $value["upload_date"];
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";
            array_push($VideoPathplaylist,"<div><input type=\"checkbox\" name=\"videoinplayList[]\" value = \"$videoid\">
                    <div>$videolink
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div></div> &emsp;&emsp;&emsp;");

        }
//        $end='</form>';
//        array_push($VideoPathplaylist,$end);
//        var_dump($VideoPathplaylist);
   return $VideoPathplaylist;
//        return $allVideoPath;
    }
    private function getPlayList()
    {
        $query = $this->conn->prepare("SELECT distinct playlistname FROM playlist where mainuser=:mainuser");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);

    }
    public function showPlaylistDropdown(){
        $dbresult = $this->getPlayList();
        $allplaylist ='';
        foreach ($dbresult as  $value) {
            $playlist = $value["playlistname"];

            $allplaylist .= "
             <a class='dropdown-item' href='#'>$playlist</a>
            ";
        }
        return $allplaylist;
}


    public function addVideoTOPlaylist($playlist,$vid){
        $query = $this->conn->prepare("INSERT INTO playlist (mainuser,playlistname,video_id) value(:mainuser,:playlistname,:videoid)");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':playlistname',$playlist);
        $query->bindParam(':videoid',$vid);
        $result = $query->execute();
        if ($result){
        return 'Add Video to Playlist successful';
        }
        return 'Add Video to Playlist failed';
    }
    public function fromVideoGetChannel($videoid){
        $query = $this->conn->prepare("SELECT * FROM videos where id=:videoid");
        $query->bindParam(':videoid', $videoid);
        $query->execute();
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);
//        var_dump($dbresult);
        return $dbresult[0]['uploaded_by'];
    }
    public function showsubscribe($videoid){
        $channel =$this->fromVideoGetChannel($videoid);
        if (!strcmp($channel, $this->usernameLoggedIn)){
            return "";
        }
        if(!$this->checksubscribe($channel)){
            return  "<div><button type=\"button\"  class=\"btn btn-danger\"  id='subscribe'>Subscribe</button> </div>";

        }
        else{
            return  "<div><button type=\"button\"  class=\"btn btn-danger\"  id='unsubscribe'>Unsubscribe</button> </div>";
        }
    }
    public function sortingVideos($category){

        $query = $this->conn->prepare("SELECT * From videos where uploaded_by=:uploaded_by order by $category desc");
        $query->bindParam(':uploaded_by',$this->usernameLoggedIn);
        $query->execute();
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);


        foreach ($dbresult as $key => $value) {
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views =$value["views"];
            $upload_date = $value["upload_date"];
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";


            array_push($this->sortingVideoPath,"
                    <div>$videolink
                    <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div>
                    </div> &emsp;&emsp;");
        }

        return $this->sortingVideoPath;
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
     public function showchannelonly($channel){
         $blockUser =$this->getBlockUsername($this->usernameLoggedIn);
         foreach($blockUser as $value){
             $channel = ucfirst($channel);
             $value = ucfirst($value);
             if(!strcmp($channel,$value)){
                 return "Unfortunately! You are blocked by this user!";
             }
         }

        if(!$this->checksubscribe($this->user)){
            $button = "<div><button type=\"button\"  class=\"btn btn-danger\"  id='subscribe'>Subscribe</button> </div>";

        }
        else{
            $button = "<div><button type=\"button\"  class=\"btn btn-danger\"  id='unsubscribe'>Unsubscribe</button> </div>";
        }
         return "
          $button
        <ul class=\"nav nav-tabs\" id=\"myTab1\" role=\"tablist\">
  <li id=\"channel1\" class=\"nav-item\">
    <a id=\"channel1\" class=\"nav-link active\" id=\"home-tab\" data-toggle=\"tab\" href=\"#channel2\" role=\"tab\" aria-controls=\"home\" aria-selected=\"true\">Channel</a>
         </li >
</ul >
<div class=\"tab-content\" id = \"myTabContent\" >
  <div class=\"tab-pane fade show active\" id = \"channel2\" role = \"tabpanel\" aria-labelledby = \"home-tab\" >
          <div id = \"show\" >
            </div>
    <div id = \"page-nav\" >
            <nav aria-label = \"Page navigation\" >
            <ul class=\"pagination\" id = \"pagination\" ></ul>
            </nav>
        </div>
</div>
 
</div>";
    }
    public function showall(){
//        if no videos, donot show delete button
        if(!count($this->video)){
            $deletebutton= "";
        }else{
            $deletebutton = " <input type=\"submit\" class=\"btn btn-danger\" id=\"delete\" name = \"Delete\" value =\"Delete videos permanent\"><br>";
        }

        $createPlaylistButton = "<input type=\"button\" class=\"btn btn-outline-primary\" id=\"createPlayList\" name = \"createPlayList\" value =\"Create PlayList\">";
        $deletePlaylistButton = "<input type=\"submit\" class=\"btn btn-outline-danger\" id=\"deletePlayList\" name = \"deletePlayList\" value =\"Remove whole PlayList\">";
        $addToFavoriteListButton = "<input type=\"submit\" class=\"btn btn-outline-info\" id=\"addToFavoriteList\" name = \"addToFavoriteList\" value =\"Add to FavoriteList\">";
        $removeVideofromFavoritelistbtn= "<input type=\"submit\" class=\"btn btn-outline-info\" id=\"removeFromFavoriteList\" name = \"removeFromFavoriteList\" value =\"Remove From FavoriteList\">";
         return "<ul class=\"nav nav-tabs\" id=\"myTab1\" role=\"tablist\">
  <li id=\"channel1\" class=\"nav-item\">
    <a id=\"channel1\" class=\"nav-link active\" id=\"home-tab\" data-toggle=\"tab\" href=\"#channel2\" role=\"tab\" aria-controls=\"home\" aria-selected=\"true\">Channel</a>
  </li>
  <li class=\"nav-item\">
    <a class=\"nav-link\" id=\"profile-tab\" data-toggle=\"tab\" href=\"#mySubscriptions\" role=\"tab\" aria-controls=\"profile\" aria-selected=\"false\">My Subscriptions</a>
  </li>
  <li class=\"nav-item\">
    <a class=\"nav-link\" id=\"myPlayList1\" data-toggle=\"tab\" href=\"#myPlayList2\" role=\"tab\" aria-controls=\"contact\" aria-selected=\"false\">My PlayList</a>
  </li>
  <li class=\"nav-item\">
    <a class=\"nav-link\" id=\"myFavoriteList1\" data-toggle=\"tab\" href=\"#myFavoriteList2\" role=\"tab\" aria-controls=\"contact\" aria-selected=\"false\">My FavoriteList</a>
  </li>
  <li class=\"nav-item\">
    <a class=\"nav-link\" id=\"sortingVideos1\" data-toggle=\"tab\" href=\"#sortingVideos2\" role=\"tab\" aria-controls=\"contact\" aria-selected=\"false\">Videos Sorting</a>
  </li>

</ul>
<div class=\"tab-content\" id=\"myTabContent\">
  <div class=\"tab-pane fade show active\" id=\"channel2\" role=\"tabpanel\" aria-labelledby=\"home-tab\">
  <form action=\"channelprocess.php?channel=$this->user\" method=\"post\">
            $deletebutton
            <div id=\"show\">
            </div>
  </form>
   <div id=\"page-nav\">
            <nav aria-label=\"Page navigation\">
            <ul class=\"pagination\" id=\"pagination\"></ul>
            </nav>
        </div>
</div>

  <div class=\"tab-pane fade\" id=\"mySubscriptions\" role=\"tabpanel\" aria-labelledby=\"profile-tab\">
  <div id=\"showSubscriptions\"></div>
  </div>
  
  <div class=\"tab-pane fade\" id=\"myPlayList2\" role=\"tabpanel\" aria-labelledby=\"contact-tab\"> 
  $createPlaylistButton
  <form action=\"channelprocess.php?channel=$this->user\" method=\"post\">
  $deletePlaylistButton
  $addToFavoriteListButton
  <div id=\"showMyPlayList\"></div>
  </form>
  </div>
  
  <div class=\"tab-pane fade\" id=\"myFavoriteList2\" role=\"tabpanel\" aria-labelledby=\"contact-tab\">
     <form action=\"channelprocess.php?channel=$this->user\" method=\"post\">
     $removeVideofromFavoritelistbtn
     <div id=\"showMyFavoriteList\"></div>
     </form>
  </div>
  <div class=\"tab-pane fade\" id=\"sortingVideos2\" role=\"tabpanel\" aria-labelledby=\"profile-tab\">
  <div class=\"btn-group\">
        <button type=\"button\" id=\"sortingvideos\" class=\"btn btn-success dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
            Sorting by
        </button>
        <div class=\"dropdown-menu\" id=\"sortingList\">
            <a class='dropdown-item' href='#'>Views</a>
            <a class='dropdown-item' href='#'>Uploading_time</a>
            <a class='dropdown-item' href='#'>Video_title</a>
            <a class='dropdown-item' href='#'>Duration</a>
        </div>
    </div>
  <div id=\"showSortingVideos\"></div>
   <div id=\"page-nav\">
            <nav aria-label=\"Page navigation\">
            <ul id=\"pagination-sorting\" class=\"pagination-sm\"></ul>
            </nav>
   </div>
   
  </div>
</div>";
    }
}