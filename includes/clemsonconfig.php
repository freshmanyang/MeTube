<?php

    ob_start();
    session_start();
    date_default_timezone_set("America/New_York");
    try{
        $conn = new PDO("mysql:host=mysql1.cs.clemson.edu;dbname=metube_q2z1","metube_dmt3","n3aj4a83");
//        https://www.php.net/manual/en/pdo.setattribute.php
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }
    catch(PDOException $e){
        echo "Connection Failed: ". $e->getMessage();
    }
?>