<?php


class searchProcessor
{
    private $conn,$usernameLoggedIn;
    private $allVideoPath = array();
    public function __construct($conn,$usernameLoggedIn)
    {
        $this->conn = $conn;
        $this->usernameLoggedIn = $usernameLoggedIn;
    }
    private function getthumbnail($videoid)
    {
        $query = $this->conn->prepare("SELECT file_path From thumbnails where video_id =:video_id and selected=1");
        $query->bindParam(':video_id', $videoid);
        $query->execute();
        return $this->thumbnail = $query->fetch(PDO::FETCH_ASSOC);

    }
    public function showAdvancedSearch($file_size_min,$file_size_max){
        if(! (empty($file_size_min) && empty($file_size_max))){
            $fileSizeFilter = $this->fileSizeFilter($file_size_min,$file_size_max);
        }
        foreach ($fileSizeFilter as $value) {
            $title = $value["title"];
            $uploaded_by = $value["uploaded_by"];
            $views = $value["views"];
            $upload_date = date('Y-m-d', strtotime($value["upload_date"]));
//            $upload_date = date('Y-m-d H:i:s',$value["upload_date"]);
            $videoid = $value["id"];
            $thumbnailpath = $this->getthumbnail($videoid);
            $thumbnailpath = $thumbnailpath["file_path"];
            $duration = $value['video_duration'];
            $videolink = "<a href='watch.php?vid=$videoid'><img src='$thumbnailpath' alt='$title' height='200' width='300'></a><br>";
            array_push($this->allVideoPath, "<div>$videolink
                   <span id='videoTitle'>$title</span><br> <div class='wrapper'><div class='left'>$uploaded_by<br>$views views</div>  <div class ='right'><span style='float:right'>$duration</span><br><span style='float:right'>$upload_date</span> </div></div>
                    </div> &emsp;&emsp;&emsp;");
        }
        return $this->allVideoPath;
//        check_privacy
    }
    private function fileSizeFilter($file_size_min,$file_size_max){
        if(empty($file_size_min)){
            $file_size_min = 0;
        }
        if(empty($file_size_max)){
            $file_size_max = 0;
        }
        $query = $this->conn->prepare("SELECT * From videos where file_size between :file_size_min AND :file_size_max ");
        $query->bindParam(':file_size_min', $file_size_min);
        $query->bindParam(':file_size_max', $file_size_max);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}