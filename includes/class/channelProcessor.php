<?php



class  channelProcessor
{
    private $conn,$video,$thumbnail,$user,$usernameLoggedIn,$subscribe,$mysubscription,$uservideo;
    private $allVideoPath =array();
    private $subscribePath =array();
    private $signinallVideoPath=array();

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

    public function create(){
        foreach ($this->video as $key => $value) {

            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views =$value["views"];
            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";


            array_push($this->allVideoPath,"<div>$videolink
                   <span id='videoTitle'>$title</span><br>$uploaded_by<br>$views views &emsp;&emsp;&emsp;&emsp;&emsp;&emsp; $upload_date
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
            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";


            array_push($this->signinallVideoPath,"<div><input type=\"checkbox\" name=\"videoList[]\" value = \"$videoid\">
                    <div>$videolink<span id='videoTitle'>$title</span><br>$uploaded_by<br>$views views &emsp;&emsp;&emsp;&emsp;&emsp;&emsp; $upload_date
                    </div></div> &emsp;&emsp;&emsp;");
        }

        return $this->signinallVideoPath;
    }
    public function createMySubscriptions(){
        $query = $this->conn->prepare("SELECT * From subscriptions where username=:mainuser");
        $query->bindParam(':mainuser',$this->usernameLoggedIn);
        $query->execute();
        $this->mysubscription = $query->fetchAll(PDO::FETCH_ASSOC);



        foreach ($this->mysubscription as $key => $value) {
            $username = ucfirst($value["Subscriptions"]);
//            依據subscribe找出他的影片
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
                $upload_date = date('Y-m-d H:i:s', $value["upload_date"]);
                $videoid = $value["id"];
                $thumbnailpath = $this->getthumbnail($videoid);
                $thumbnailpath = $thumbnailpath["file_path"];
                $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='100' width='200'></a><br>";
                $subscribeVideoPath .= "
                    <div >$videolink
                   <span id='videoTitle'>$title</span><br>$uploaded_by<br>$views views &emsp; $upload_date
                    </div> &emsp;&emsp;&emsp;";
                $count++;
//                每個subscribe channel 只顯示四筆
                if($count > 4) {break;}
            }
                array_push($this->subscribePath,"<p><a href=\"channel.php?channel=$username\">$username's Channel</a></p> <div class='video'> $subscribeVideoPath </div>");
            }else{
                array_push($this->subscribePath,"<p><a href=\"channel.php?channel=$username\">$username's Channel</a><p>This channel doesn't have any video yet!</p>");
            }

        }
        return $this->subscribePath;

    }

    public function addsubscribe(){

//        沒登入不能subscribe 退回登入頁面
        if(empty($this->usernameLoggedIn)){
//            return "alert('You are not login, redirect to Login page after click'); location.href = 'index.php';";
            return "You are not login, redirect to Login page after click";

        }
 /*       if(!strcmp($this->usernameLoggedIn,$this->user)) {
            return "You cannot subscribe yourself";
        }*/
        if(!$this->checksubscribe())
        {
//沒有重複 插入資料表
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

//        沒登入不能unsubscribe 退回登入頁面
        if(empty($this->usernameLoggedIn)){
            return "You are not login, redirect to Login page after click";
        }

        if($this->checksubscribe())
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

    public function checksubscribe(){
// 判斷已經重複 subscribe
        $query = $this->conn->prepare("Select * from subscriptions where username=:mainuser and Subscriptions=:subscriptions");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':subscriptions',$this->user);
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
    private function checkPlayList($playlistname){
        $query = $this->conn->prepare("SELECT * FROM playlist where mainuser=:mainuser and playlistname=:playlistname");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':playlistname',$playlistname);
        $query->execute();
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);
        return count($dbresult);
    }
    public function createPlayList($playlistname){
        if(!empty($playlistname)){
        if(!$this->checkPlayList($playlistname)){

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
    private function getVideoInfoViaPlayList($playlist){
        $query = $this->conn->prepare("SELECT * FROM playlist where mainuser=:mainuser and playlistname=:playlistname");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':playlistname', $playlist);
        $query->execute();
        return  $query->fetchAll(PDO::FETCH_ASSOC);

    }
    public function showPlayList(){
        $query = $this->conn->prepare("SELECT distinct playlistname FROM playlist where mainuser=:mainuser");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->execute();
        $dbresult = $query->fetchAll(PDO::FETCH_ASSOC);
        $allplaylist ='';
        foreach ($dbresult as  $value) {
            $allplaylist .= '<p><input type="checkbox" name="playList[]" value ='.$value["playlistname"].'>';
            $allplaylist .=  '&nbsp'.$value["playlistname"].'</p>';
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
                $upload_date = date('Y-m-d H:i:s', $value["upload_date"]);
                $videoid = $value["id"];
                $thumbnailpath = $this->getthumbnail($videoid);
                $thumbnailpath = $thumbnailpath["file_path"];
                $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='100' width='200'></a><br>";
                $playlistVideoPath .= "<div >$videolink
                   <span id='videoTitle'>$title</span><br>$uploaded_by<br>$views views &emsp; $upload_date
                    </div> &emsp;&emsp;&emsp;";
                $count++;
//                每個subscribe channel 只顯示4筆
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
     public function showchannelonly(){
        if(!$this->checksubscribe()){
            $button = "<div><button type=\"button\"  class=\"btn btn-danger\"  id='subscribe'>Subscribe</button> </div>";

        }
        else{
            $button = "<div><button type=\"button\"  class=\"btn btn-danger\"  id='unsubscribe'>Unsubscribe</button> </div>";
        }
        return "
          $button
        <ul class=\"nav nav-tabs \">
        <li class=\"active\"><a data-toggle=\"tab\" href=\"#Channel\">Channel</a></li>
       
    </ul>

    <div class=\"tab-content\">
        <div id=\"Channel\" class=\"tab-pane fade in active\">
            
            <div id=\"show\">
            </div>
           
            <div id=\"page-nav\">
                <nav aria-label=\"Page navigation\">
                    <ul class=\"pagination\" id=\"pagination\"></ul>
                </nav>
            </div>
        </div>
   
    </div>
        ";
    }
    public function showall(){
//        如果沒影片就不要出現刪除按鈕
        if(!count($this->video)){
            $deletebutton= "";
        }else{
            $deletebutton = " <input type=\"submit\" class=\"btn btn-danger\" id=\"delete\" name = \"Delete\" value =\"Delete\"><br>";
        }

//      create playlist button
        $createPlaylistButton = " <input type=\"button\" class=\"btn btn-outline-primary\" id=\"createPlayList\" name = \"createPlayList\" value =\"Create PlayList\">";
        $deletePlaylistButton = " <input type=\"button\" class=\"btn btn-outline-danger\" id=\"deletePlayList\" name = \"deletePlayList\" value =\"Delete PlayList\"><br>";


         return "<ul class=\"nav nav-tabs\" id=\"myTab1\" role=\"tablist\">
  <li id=\"channel1\" class=\"nav-item\">
    <a id=\"channel1\" class=\"nav-link active\" id=\"home-tab\" data-toggle=\"tab\" href=\"#channel2\" role=\"tab\" aria-controls=\"home\" aria-selected=\"true\">Channel</a>
  </li>
  <li class=\"nav-item\">
    <a class=\"nav-link\" id=\"profile-tab\" data-toggle=\"tab\" href=\"#mySubscriptions\" role=\"tab\" aria-controls=\"profile\" aria-selected=\"false\">My Subscriptions</a>
  </li>
  <li class=\"nav-item\">
    <a class=\"nav-link\" id=\"myPlayList1\" data-toggle=\"tab\" href=\"#myPlayList2\" role=\"tab\" aria-controls=\"contact\" aria-selected=\"false\">My Playlist</a>
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
  <div id=\"showMyPlayList\"></div></div>
</div>";
    }
}