<?php


class AvatarUpload
{
    private $conn, $imageData, $uid;
    private $targetDir = "uploads/avatars/";
    private $defaultAvatarPath = './assets/imgs/avatars/default.png';
    private $finalFilePath = '';

    public function __construct($conn, $imageData, $uid)
    {
        $this->conn = $conn;
        $this->imageData = $imageData;
        $this->uid = $uid;
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

    private function getOldAvatarPath()
    {
        $query = $this->conn->prepare("SELECT avatar_path FROM users WHERE id=:uid LIMIT 1");
        $query->bindParam(":uid", $this->uid);
        if (!$query->execute()) {
            return false;
        }
        return $query->fetch(PDO::FETCH_ASSOC)['avatar_path'];
    }

    private function updateAvatarPath($finalFilePath)
    {
        $oldAvatarPath = $this->getOldAvatarPath();
        if (!$oldAvatarPath) {
            return false;
        }

        if ($oldAvatarPath !== $this->defaultAvatarPath) {
            // if old avatar is not default, delete it
            if (!$this->delFile($oldAvatarPath)) {
                return false;
            }
        }

        // insert new image path into database
        $query = $this->conn->prepare("UPDATE users SET avatar_path=:avatar_path WHERE id=:uid");
        $query->bindParam(":uid", $this->uid);
        $query->bindParam(":avatar_path", $finalFilePath);
        return $query->execute();
    }

    private function delFile($filePath)
    {
        // delete the original image file after convert
        if (unlink($filePath)) {
            return true;
        }
        return false;
    }

    private function isProcessed($imageData, $filePath)
    {
        $imageFormat = pathinfo($filePath, PATHINFO_EXTENSION); // get image format
        $this->finalFilePath = $this->targetDir . uniqid() . "." . $imageFormat; // the final file path doesn't contain the image name


        if (!$this->updateAvatarPath($this->finalFilePath)) {
            return false;
        }

        if (!$this->moveFile($imageData["tmp_name"], $this->finalFilePath)) {
            return false;
        }

        if (!$this->setFilePermission($this->finalFilePath)) {
            return false;
        }

        return true;
    }

    public function upload()
    {
        $filePath = $this->targetDir . uniqid() . basename($this->imageData["name"]);
        $filePath = str_replace(" ", "_", $filePath); /* replace space with _ */
        return $this->isProcessed($this->imageData, $filePath);
    }

    public function getFilePath()
    {
        return $this->finalFilePath;
    }
}