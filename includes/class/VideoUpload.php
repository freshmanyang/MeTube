<?php


class VideoUpload
{
    private $conn; // database connection descriptor
    private $videoData, $title, $description, $keywords, $privacy, $category, $uploaded_by;
    private $sizeLimit = 1000000000; // size limitation for a single uploaded video
    private $validVideoFormats = array('avi', 'wmv', 'mp4', 'mpeg', 'rmvb', '3gp', 'mkv', 'flv');
    private $targetDir = "uploads/videos/"; // local directory for video storage
	private $ffmpegPath;
    private $ffprobePath;
//    private $ffmpegPath = realpath("ffmpeg/ffmpeg");
//    private $ffprobePath = realpath("ffmpeg/ffmpeg");

    public function __construct($conn)
    {
        $this->conn = $conn;
//        windows route
//        $this->ffmpegPath = realpath("ffmpeg/bin/ffmpeg.exe");
//        $this->ffprobePath = realpath("ffmpeg/bin/ffprobe.exe");
//        unix route
//        $this->ffmpegPath = realpath("./ffmpeg/ffmpeg");
//        $this->ffprobePath = realpath("./ffmpeg/ffprobe");
        $this->ffmpegPath = realpath("/usr/bin/ffmpeg");
        $this->ffprobePath = realpath("/usr/bin/ffprobe");
    }

    public function setData($videoData, $title, $description, $keywords, $privacy, $category, $uploaded_by)
    {
        $this->videoData = $videoData;
        $this->title = $title;
        $this->description = $description;
        $this->keywords = preg_split("/[\s,]+/", $keywords); // use (" ", \r, \t, \n, \f) to split keywords
        $this->privacy = $privacy;
        $this->category = $category;
        $this->uploaded_by = $uploaded_by;
    }

    private function isNoPostErr($postErr)
    {
        // Verify that the video upload was successful through POST method
        return $postErr == 0;
    }

    private function isValidFormat($videoFormat)
    {
        // verify video format
        $lowercase = strtolower($videoFormat);
        return in_array($lowercase, $this->validVideoFormats);
    }

    private function isValidSize($videoSize)
    {
        // verify video size
        return $videoSize <= $this->sizeLimit;
    }

    private function moveFile($tempPath, $filePath)
    {
        // move video file from temp dir to the designated dir
        return move_uploaded_file($tempPath, $filePath);
    }

    private function setFilePermission($finalFilePath)
    {
        // set file permission for uploaded files
        return chmod($finalFilePath, 0644);
    }

    private function insertVideoData($finalFilePath)
    {
        // insert video data into database
        $query = $this->conn->prepare("INSERT INTO videos(uploaded_by, title, description, privacy, file_path, category, upload_date)
                                        VALUES(:uploaded_by, :title, :description, :privacy, :file_path, :category, :upload_date)");
        $query->bindParam(":uploaded_by", $this->uploaded_by); // bindParam works only by passing reference (not by passing a direct value)
        $query->bindParam(":title", $this->title);
        $query->bindParam(":description", $this->description);
        $query->bindParam(":privacy", $this->privacy);
        $query->bindParam(":file_path", $finalFilePath);
        $query->bindParam(":category", $this->category);
        $query->bindValue(":upload_date", date("Y-m-d H:i:s"));
        return $query->execute();
    }

    private function convertVideoToMP4($filePath, $finalFilePath)
    {
        // convert video from other formats to mp4 format
    //    $cmd = "ffmpeg/ffmpeg -i $filePath $finalFilePath 2>&1";
		$cmd = "$this->ffmpegPath -i $filePath $finalFilePath 2>&1";
        $outPutLog = array();
        exec($cmd, $outPutLog, $returnCode);
        if ($returnCode != 0) {
            // if an error occurs, show the error info
            foreach ($outPutLog as $line) {
                echo $line . "<br />";
            }
            return false;
        }
        return true;
    }

    private function delFile($filePath)
    {
        // delete the original video file after convert
        if (unlink($filePath)) {
            return true;
        }
        return false;
    }

    private function getVideoDuration($finalFilePath)
    {
        // get duration for each video
        //return (int)shell_exec("ffmpeg/ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $finalFilePath");
		return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $finalFilePath");
    }

    private function uploadVideoDuration($duration, $videoID)
    {
        // upload the video duration to database
        $hours = floor($duration / 3600);
        $hours = ($hours < 10) ? "0" . $hours . ":" : $hours . ":";

        $minutes = floor(($duration - (int)$hours * 3600) / 60);
        $minutes = ($minutes < 10) ? "0" . $minutes . ":" : $minutes . ":";

        $seconds = floor($duration % 60);
        $seconds = ($seconds < 10) ? "0" . $seconds : $seconds;

        $duration = $hours . $minutes . $seconds;
        $duration = date("H:i:s", strtotime($duration));

        // insert duration into database
        $query = $this->conn->prepare("UPDATE videos SET video_duration = :duration WHERE id = :videoID");
        $query->bindParam(":duration", $duration);
        $query->bindParam(":videoID", $videoID);
        return $query->execute();
    }

    private function uploadVideoSize($finalFilePath, $videoID)
    {
        $finalFileSize = filesize($finalFilePath);
        $query = $this->conn->prepare("UPDATE videos SET file_size=:file_size where id=:videoId");
        $query->bindParam(':file_size', $finalFileSize);
        $query->bindParam(':videoId', $videoID);
        return $query->execute();
    }

    private function uploadKeywords($videoID)
    {
        foreach ($this->keywords as $keyword) {
            // insert keyword in to keyword table
            $query = $this->conn->prepare("INSERT IGNORE INTO keyword (keyword) VALUES (:keyword)");
            $query->bindParam(':keyword', $keyword);
            if ($query->execute()) {
                // get keyword_id for each keyword
                $query = $this->conn->prepare("SELECT keyword_id FROM keyword WHERE keyword=:keyword LIMIT 1");
                $query->bindParam(':keyword', $keyword);
                if (!$query->execute()) {
                    return false;
                }
                $keywordID = $query->fetch(PDO::FETCH_ASSOC)['keyword_id'];
                // insert video_id keyword_id into video_keyword table
                $query = $this->conn->prepare("INSERT IGNORE INTO video_keyword (video_id, keyword_id) 
                                               VALUES (:video_id, :keyword_id)");
                $query->bindParam(':video_id', $videoID);
                $query->bindParam(':keyword_id', $keywordID);
                if (!$query->execute()) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    private function createThumbNails($finalFilePath)
    {
        // create three thumbnails for an uploaded video
        $tnSize = "1280x720"; // size of a thumbnail
        $tnNum = 3; // number of thumbnails
        $tnPath = "uploads/videos/thumbnails"; // path to the thumbnail
        $duration = $this->getVideoDuration($finalFilePath);
        $videoID = $this->conn->lastInsertId(); // get the last generated id of the inserted data in table
        // Insert duration into database before generate thumbnails
        if (!$this->uploadVideoDuration($duration, $videoID)) {
            echo "Failed to update video duration.";
            return false;
        }
        if (!$this->uploadVideoSize($finalFilePath, $videoID)) {
            echo "Failed to update video size.";
            return false;
        }
        if (!$this->uploadKeywords($videoID)) {
            echo "Failed to insert keywords into database.";
            return false;
        }
        for ($i = 1; $i <= $tnNum; $i++) {
            $imageName = uniqid() . '.jpg';
            $interval = ($duration * 0.8) / $tnNum * $i;
            $imagePath = "$tnPath/$videoID-$imageName";

            // call ffmpeg
            //$cmd = "ffmpeg/ffmpeg -i $finalFilePath -ss $interval -s $tnSize -vframes 1 $imagePath 2>&1";
			$cmd = "$this->ffmpegPath -i $finalFilePath -ss $interval -s $tnSize -vframes 1 $imagePath 2>&1";
            $outPutLog = array();
            exec($cmd, $outPutLog, $returnCode);
            if ($returnCode != 0) {
                // if an error occurs, show the error info
                foreach ($outPutLog as $line) {
                    echo $line . "<br />";
                }
            }
            if (!$this->setFilePermission($imagePath)) {
                echo "Failed to set permission to file " . $imagePath;
                return false;
            }
            // insert into thumbnails table
            $query = $this->conn->prepare("INSERT INTO thumbnails (video_id, file_path, selected) 
                                            VALUES (:video_id, :file_path, :selected)");
            $query->bindParam(":video_id", $videoID);
            $query->bindParam(":file_path", $imagePath);
            $selected = ($i === 1) ? 1 : 0;
            $query->bindParam(":selected", $selected);
            $res = $query->execute();
            if (!$res) {
                echo "Failed to insert thumbnails into database.";
                return false;
            }
        }
        return true;
    }

    private function isProcessed($videoData, $filePath)
    {
        $videoFormat = pathinfo($filePath, PATHINFO_EXTENSION); // get video format
        $finalFilePath = $this->targetDir . uniqid() . ".mp4"; // the final file path doesn't contain the video name

        // verify if the video is successfully uploaded form client.
        if (!$this->isNoPostErr($videoData["error"])) {
            echo "Whoops, your video could not be uploaded successfully for some reasons.";
            return false;
        }
        // check if the file uploaded by client is a video.
        if (!$this->isValidFormat($videoFormat)) {
            echo "Invalid format";
            return false;
        }
        // check if the video size exceeds the limit.
        if (!$this->isValidSize($videoData["size"])) {
            echo "File size cannot exceed " . $this->sizeLimit / 1000000 . "M." . "<br/>";
            return false;
        }
        // move video from temp directory to the designated directory.
        if (!$this->moveFile($videoData["tmp_name"], $filePath)) {
            echo "Unable to move file from temp directory.";
            return false;
        }
        // insert uploaded data into database.
        if (!$this->insertVideoData($finalFilePath)) {
            echo "Cannot insert video data into database.";
            return false;
        }
        // convert other video format to mp4 format.
        if (!$this->convertVideoToMP4($filePath, $finalFilePath)) {
            echo "Cannot convert video to .mp4 format";
            return false;
        }
        // set converted video permission to 0644
        if (!$this->setFilePermission($finalFilePath)) {
            echo "Failed to set permission to file " . $finalFilePath;
            return false;
        }
        // delete the original video file
        if (!$this->delFile($filePath)) {
            echo "Failed to delete video!";
            return false;
        }

        // create thumbnails
        if (!$this->createThumbNails($finalFilePath)) {
            echo "Error in function: createThumbNails";
            return false;
        }
        echo "Video upload successful!";
        return true;
    }

    public function upload()
    {
        $filePath = $this->targetDir . uniqid() . basename($this->videoData["name"]); /* use uniqid() to create unique name for each video
                                                                                   use basename() to get video name */
        $filePath = str_replace(" ", "_", $filePath); /* replace space with _ */
        $this->isProcessed($this->videoData, $filePath);
    }
}