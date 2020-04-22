<?php

class contactListProcessor
{
    private $conn, $usernameLoggedIn, $sqlData, $table, $view, $viewquery, $viewfilter, $filtertable;

    public function __construct($conn, $usernameLoggedIn)
    {
        $this->conn = $conn;
        $this->usernameLoggedIn = $usernameLoggedIn;
    }

    public function query()
    {
        $query = $this->conn->prepare("SELECT * From contactlist WHERE mainuser =:mainUser");
        $query->bindParam(':mainUser', $this->usernameLoggedIn);
        $query->execute();
        $this->sqlData = $query->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlData;
    }

    private function checkDuplicate($username)
    {
        $query = $this->conn->prepare("SELECT * From contactlist WHERE mainuser =:mainUser and username=:username");
        $query->bindParam(':mainUser', $this->usernameLoggedIn);
        $query->bindParam(':username', $username);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function userexist($username)
    {
        $query = $this->conn->prepare("SELECT * From users WHERE username=:username");
        $query->bindParam(':username', $username);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchData()
    {
        $this->query();
        $count = 1;
        foreach ($this->sqlData as $key => $value) {
            $block = '';
            if ($value['blocked'] == 1) {
                $block = 'V';
            }
            $channelLink = 'channel.php?channel=' . $value['username'];
            $this->table .= '<tr> <th scope="row">' . $count . '</th>';
            $this->table .= '<td>' . '<input type="checkbox" name="contactList[]" value = "' . $value['username'] . '"> </td>';
            $this->table .= '<td><a href="' . $channelLink . '">' . $value['username'] . '</a></td>' . '<td>' . $value['groupname'] . '</td>' . '<td>' . $block . '</td>';
            $this->table .= '</tr>';
            $count++;
        }
        return "$this->table";
    }

    public function deleteContact($deleteList)
    {
        $qMarks = str_repeat('?,', count($deleteList) - 1) . '?';
        $mainUser = "'" . $this->usernameLoggedIn . "'";
        $query = $this->conn->prepare("DELETE FROM contactlist WHERE mainuser= $mainUser AND username IN ($qMarks)");
        $query->execute($deleteList);
    }

    public function addContact($conntactName, $groupName, $block)
    {
        if (!strcmp($conntactName, $this->usernameLoggedIn)) {
            return "You cannot add yourself";
        }
        if (!empty($this->checkDuplicate($conntactName))) {
            return "You already have this person " . $conntactName . " in your contact list";
        }
        if (empty($this->userexist($conntactName))) {
            return "The username " . $conntactName . " that you input not exist in MeTube system";
        }
        $query = $this->conn->prepare("INSERT INTO contactlist (mainuser,username,groupname,blocked) value(:mainuser,:contactName,:groupName,:blocked)");
        $query->bindParam(':mainuser', $this->usernameLoggedIn);
        $query->bindParam(':contactName', $conntactName);
        $query->bindParam(':groupName', $groupName);
        $query->bindParam(':blocked', $block);
        $query->execute();
    }

    public function blockContact($blockList, $block)
    {
        $qMarks = str_repeat('?,', count($blockList) - 1) . '?';
        $mainUser = "'" . $this->usernameLoggedIn . "'";
        $query = $this->conn->prepare("UPDATE contactlist set blocked= $block WHERE mainuser= $mainUser AND username IN ($qMarks)");
        $query->execute($blockList);
        $query = $this->conn->prepare("DELETE from subscriptions  WHERE username=$mainUser AND Subscriptions IN ($qMarks)");
        $query->execute($blockList);
    }

    public function getviewfilter()
    {
        $query = $this->conn->prepare("SELECT distinct groupname From contactlist WHERE mainuser =:mainUser");
        $query->bindParam(':mainUser', $this->usernameLoggedIn);
        $query->execute();
        $this->viewquery = $query->fetchAll(PDO::FETCH_ASSOC);
//        print_r($this->viewquery);
        $this->view = '<option value="All">All</option>';
        foreach ($this->viewquery as $key => $value) {
            $this->view .= '<option value="' . $value['groupname'] . '">' . $value['groupname'] . '</option>';
        }
        return "$this->view";
    }

    public function viewFilter($groupname)
    {
        if (!strcmp($groupname, 'All')) {
            return $this->fetchData();
        }
        $query = $this->conn->prepare("SELECT * From contactlist WHERE mainuser =:mainUser and groupname=:groupname");
        $query->bindParam(':mainUser', $this->usernameLoggedIn);
        $query->bindParam(':groupname', $groupname);
        $query->execute();
        $this->viewfilter = $query->fetchAll(PDO::FETCH_ASSOC);
        $count = 1;
        foreach ($this->viewfilter as $key => $value) {
            $block = '';
            if ($value['blocked'] == 1) {
                $block = 'V';
            }
            $channelLink = 'channel.php?channel=' . $value['username'];
            $this->filtertable .= '<tr> <th scope="row">' . $count . '</th>';
            $this->filtertable .= '<td>' . '<input type="checkbox" name="contactList[]" value = "' . $value['username'] . '"> </td>';
            $this->filtertable .= '<td><a href="' . $channelLink . '">' . $value['username'] . '</a></td>' . '<td>' . $value['groupname'] . '</td>' . '<td>' . $block . '</td>';
            $this->filtertable .= '</tr>';
            $count++;
        }
        return "$this->filtertable";
    }
}