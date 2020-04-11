<?php


class searchProcessor
{
    private $conn,$usernameLoggedIn;
    public function __construct($conn,$usernameLoggedIn)
    {
        $this->conn = $conn;
        $this->usernameLoggedIn = $usernameLoggedIn;
    }
    public function showAdvancedSearch($file_size_min,$file_size_max){
        if(!empty($file_size_min) && empty($file_size_max)){
            $this->filesizefilter($file_size_min,$file_size_max);
        }
    }
    private function filesizefilter($file_size_min,$file_size_max){}

}