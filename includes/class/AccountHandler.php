<?php

// user sign up, sign in and sign out
class AccountHandler
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function usernameExisted($uname)
    {
        $query = $this->conn->prepare("SELECT username FROM users WHERE username=:uname LIMIT 1");
        $query->bindParam(":uname", $uname);
        $query->execute();
        if ($query->rowCount() != 0) {
            // found username in table, username existed
            return true;
        }
        return false;
    }

    public function emailExisted($email)
    {
        $query = $this->conn->prepare("SELECT email FROM users WHERE email=:email LIMIT 1");
        $query->bindParam(":email", $email);
        $query->execute();
        if ($query->rowCount() != 0) {
            // found email in table
            return true;
        }
        return false;
    }

    public function checkPassword($email, $password)
    {
        $password = hash("sha512", $password);
        $query = $this->conn->prepare("SELECT id FROM users WHERE email=:email AND password=:password LIMIT 1");
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $password);
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC)['id'];
        }
        return '';
    }

    public function register($postData)
    {
        $query = $this->conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, sign_up_date, avatar_path)
                          VALUES (:first_name, :last_name, :username, :email, :password, :sign_up_date, :avatar_path)");
        $query->bindParam(":first_name", $postData['input_first_name']);
        $query->bindParam(":last_name", $postData['input_last_name']);
        $query->bindParam(":username", $postData['input_username']);
        $query->bindParam(":email", $postData['input_email']);
        $query->bindValue(":password", hash('sha512', $postData['input_password']));
        $query->bindValue(":sign_up_date", date("Y-m-d H:i:s"));
        $query->bindValue(":avatar_path", "assets/imgs/avatars/default.png");
        if ($query->execute()) {
            return $this->conn->lastInsertId();
        }
        return '';
    }

    public function signIn($postData)
    {
        return $this->checkPassword($postData['input_email'], $postData['input_password']);
    }

    public function signOut()
    {
        unset($_SESSION["uid"]);
        unset($_SESSION["userLoggedIn"]);
    }
}
