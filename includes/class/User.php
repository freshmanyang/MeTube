<?php


class User
{
    private $conn, $userData, $uid;

    public function __construct($conn, $uid)
    {
        $this->conn = $conn;
        $this->uid = $uid;
        $query = $conn->prepare("SELECT * FROM users WHERE id=:uid LIMIT 1");
        $query->bindParam(":uid", $uid);
        $query->execute();
        $this->userData = $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserId()
    {
        return $this->userData["id"];
    }

    public function getFirstName()
    {
        return $this->userData["first_name"];
    }

    public function setFirstName($firstName)
    {
        $query = $this->conn->prepare("UPDATE users SET first_name=:first_name WHERE id=:uid");
        $query->bindParam(":first_name", $firstName);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["first_name"] = $firstName;
            return true;
        }
        return false;
    }

    public function getLastName()
    {
        return $this->userData["last_name"];
    }

    public function setLastName($lastName)
    {
        $query = $this->conn->prepare("UPDATE users SET last_name=:last_name WHERE id=:uid");
        $query->bindParam(":last_name", $lastName);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["last_name"] = $lastName;
            return true;
        }
        return false;
    }


    public function getUsername()
    {
        return $this->userData["username"];
    }

    public function setUsername($username)
    {
        $query = $this->conn->prepare("UPDATE users SET username=:username WHERE id=:uid");
        $query->bindParam(":username", $username);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["username"] = $username;
            return true;
        }
        return false;
    }

    public function getEmail()
    {
        return $this->userData["email"];
    }

    public function getPassword(){
        return $this->userData["password"];
    }

    public function setPassword($encrypted_password){
        $query = $this->conn->prepare("UPDATE users SET password=:password WHERE id=:uid");
        $query->bindParam(":password", $encrypted_password);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["password"] = $encrypted_password;
            return true;
        }
        return false;
    }

    public function getAvatarPath()
    {
        return $this->userData["avatar_path"];
    }

    public function setAvatarPath($imageData)
    {
        $avatarUploadObj = new AvatarUpload($this->conn, $imageData, $this->uid);
        if ($avatarUploadObj->upload()) {
            $this->userData["avatar_path"] = $avatarUploadObj->getFilePath();
            return true;
        }
        return false;
    }

    public function getBirthday(){
        return $this->userData['birthday'];
    }

    public function setBirthday($birthday)
    {
        $query = $this->conn->prepare("UPDATE users SET birthday=:birthday WHERE id=:uid");
        $query->bindParam(":birthday", $birthday);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["birthday"] = $birthday;
            return true;
        }
        return false;
    }

    public function getGender(){
        return $this->userData['gender'];
    }

    public function setGender($gender)
    {
        $query = $this->conn->prepare("UPDATE users SET gender=:gender WHERE id=:uid");
        $query->bindParam(":gender", $gender);
        $query->bindParam(":uid", $this->uid);
        if ($query->execute()) {
            $this->userData["gender"] = $gender;
            return true;
        }
        return false;
    }
}