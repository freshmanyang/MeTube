<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
            if(isset($_SESSION['uid'])){
                echo $userLoginInObj->getUsername()." Login success.</div>";
            }
            echo 'alan';
            echo 'branch';
        ?>
    </div>
</main>
</body>
</html>