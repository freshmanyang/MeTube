<?php
require_once('./includes/header.php');
require_once('./includes/class/contactListProcessor.php');
require_once("includes/nav.php");
if (empty($usernameLoggedIn)) {
    echo "<script>alert('You are not login, redirect to home page after click'); location.href = 'index.php';</script>";
//    header("refresh:3;url=index.php");
}
$conntactList = new contactListProcessor($conn, $usernameLoggedIn);
?>
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
        echo "<div class = 'contactPage'>";
        echo "<div class = 'contactPage_head'> <h1><a href=\"contactList.php\">Contact List</a></h1><br><h3>" . "Hello " . $usernameLoggedIn . "</h3><br>";
        if (isset($_POST['Delete'])) {
            if (isset($_POST['contactList'])) {
//    var_dump($_POST['contactList']);
                $conntactList->deleteContact($_POST['contactList']);
                header("Location: contactList.php");
            }
        } elseif (isset($_POST['Block'])) {
            if (isset($_POST['contactList'])) {
                $conntactList->blockContact($_POST['contactList'], 1);
                header("Location: contactList.php");
            }
        } elseif (isset($_POST['unBlock'])) {
            if (isset($_POST['contactList'])) {
                $conntactList->blockContact($_POST['contactList'], 0);
                header("Location: contactList.php");
            }
        }
        if (isset($_POST['contactName'])) {
//    print_r($_POST['contactName']);
            $block = 0;
            if (isset($_POST['toBlock'])) {
                $block = 1;
            }
            $message = $conntactList->addContact($_POST['contactName'], $_POST['groupName'], $block);
            if (empty($message)) {
                header("Location: contactList.php");
            }
            echo "<p id='contactListError'>Error! " . $message . '</p>';
        }
        ?>
        <!--Add new contact-->
        <!-- Button trigger modal -->
        <button type="button" id="showaddcontact" class="btn btn-primary" data-toggle="modal"
                data-target="#contactModal">Add New Contact
        </button>
        <br>
        <div class="modal" id="contactModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Contact</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="contact_form" action="contactList.php" enctype="multipart/form-data" method="POST">
                            <div class="form-group">
                                <label for="contactName">Username:</label> <input type="text" id="contactName"
                                                                                  name="contactName" required>
                            </div>
                            <div class="form-group">
                                <label for="groupName">Groupname:&nbsp</label>
                                <select id="groupName" name="groupName">
                                    <option value="family">family</option>
                                    <option value="friends">friends</option>
                                    <option value="favorite">favorite</option>
                                </select>
                                <!--                    &nbsp html space-->
                                <label for="Blocked">Blocked:&nbsp&nbsp&nbsp</label><input type="checkbox" id="Blocked"
                                                                                           name="toBlock" value="1">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitForm" class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $("#submitForm").on('click', function () {
                $("#contact_form").submit();
            });
        </script>
        <!--add new contact end-->
        <?php
        $queryResult = $conntactList->query();
        if (empty($queryResult)) {
            echo "<h5>" . 'Your contact list is empty' . "</h5>";
            die;
        }
        ?>
    </div>
    <div class='contactList'>
        <div class="filter">
            <h5>Your contact list is below:</h5>
            <form action="contactList.php" method="post">
                <div class="btn-group">
                    <select name="groupfilter">
                        <option value="none" selected disabled hidden>
                            Select an Option
                        </option>
                        <?php echo $conntactList->getviewfilter() ?>
                    </select>
                </div>
                <input type="submit" class="btn btn-outline-info" name="viewFilter" value="Filter">
        </div>
        <div class="viewtable">
            <table class="table table-striped">
                <thead class="thead-dark">
                <tr>
                    <th scope="col"><label for="selectcontactbtn">Select All:</label>
                        <input type="checkbox" id="selectcontactbtn" value="Select All"/></th>
                    <th scope="col">Selected</th>
                    <th scope="col">Username</th>
                    <th scope="col">Groupname</th>
                    <th scope="col">Blocked</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (isset($_POST['viewFilter'])) {
                    if (isset($_POST['groupfilter'])) {
                        echo $conntactList->viewFilter($_POST['groupfilter']);
                    } else {
                        echo $conntactList->fetchData();
                    }
                } else {
                    echo $conntactList->fetchData();
                }
                ?>
                </tbody>
            </table>
            <input type="submit" class="btn btn-outline-danger" name="Delete" value="Delete">
            <input type="submit" class="btn btn-outline-warning" name="Block" value="Block">
            <input type="submit" class="btn btn-outline-success" name="unBlock" value="Unblock">
            <input type="reset" class="btn btn-outline-dark">
        </div>
        </form>
    </div><!--contactList div-->
    </div> <!--contactPage div-->
    </div>
</main>
<script>
    // select all btn or unselect all
    var selectcontactbtn = document.getElementById("selectcontactbtn");
    var selectedConatactlist = document.getElementsByName('contactList[]');
    $('#selectcontactbtn').click(function () {
        if (this.checked) {
            // Iterate each checkbox
            $(selectedConatactlist).each(function () {
                this.checked = true;
            });
        } else {
            $(selectedConatactlist).each(function () {
                this.checked = false;
            });
        }
    });
</script>
</body>
</html>