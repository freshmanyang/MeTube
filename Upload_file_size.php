<?php
public function uploadVideo($videoUploadData){
//            Must add before generate thumbnails function
            $videoId = $this->con->lastInsertId();
            if(!$this->updateFileSize($finalFilePath,$videoId)){
                echo 'Update file size to Database failed';
                return false;
            }
//            must pass $videoid to generateThumbnails and comment out $videoId = $this->con->lastInsertId();
            if(!$this->generateThumbnails($finalFilePath,$videoId)){
                echo 'Get video duration failed';
                return false;
            }
}

private function updateFileSize($finalFilePath,$videoId){
        $finalFileSize = filesize($finalFilePath);
        $query = $this->con->prepare("UPDATE videos SET file_size=:file_size where id=:videoId");
        $query->bindParam(':file_size',$finalFileSize);
        $query->bindParam(':videoId',$videoId);
        return $query->execute();
    }

?>