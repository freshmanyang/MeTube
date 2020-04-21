<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<link rel="stylesheet" href="assets/css/profile.css">
<main class="profile-main-section">
    <div class="profile-main-content">
        <header>
            <h1>Personal Info</h1>
            <p>Basic info, like your name and photo, that you use on MeTube services</p>
        </header>
        <section>
            <article>
                <div class="content-header">
                    <h2>Profile</h2>
                    <?php
                    echo "<p>Account: " . $userLoginInObj->getEmail() . "</p>";
                    ?>
                </div>
                <div class="profile-btn" id="editable" target-modal="#update_avatar_modal">
                    <h3>PHOTO</h3>
                    <p>Add a photo to personalize your account</p>
                    <?php
                    echo "<img src='" . $userLoginInObj->getAvatarPath() . "' class='avatar-lg' id='profile_avatar'>";
                    ?>
                </div>
                <div class="profile-btn" id="editable" target-modal="#update_name_modal">
                    <h3>NAME</h3>
                    <?php
                    echo "<p class='has-value' id='profile_full_name'>" . $userLoginInObj->getFirstName() . " " . $userLoginInObj->getLastName() . "</p>";
                    ?>
                    <i class="iconfont icon-arrowright"></i>
                </div>
                <div class="profile-btn" id="editable" target-modal="#update_username_modal">
                    <h3>USERNAME</h3>
                    <?php
                    echo "<p class='has-value' id='profile_username'>" . $userLoginInObj->getUsername() . "</p>";
                    ?>
                    <i class="iconfont icon-arrowright"></i>
                </div>
                <div class="profile-btn" id="editable" target-modal="#update_birthday_modal">
                    <h3>BIRTHDAY</h3>
                    <?php
                    echo "<p class='has-value' id='profile_birthday'>" . $userLoginInObj->getBirthday() . "</p>";
                    ?>
                    <i class="iconfont icon-arrowright"></i>
                </div>
                <div class="profile-btn" id="editable" target-modal="#update_gender_modal">
                    <h3>GENDER</h3>
                    <?php
                    echo "<p class='has-value' id='profile_gender'>" . $userLoginInObj->getGender() . "</p>";
                    ?>
                    <i class="iconfont icon-arrowright"></i>
                </div>
                <div class="profile-btn" id="editable" target-modal="#update_password_modal">
                    <h3>PASSWORD</h3>
                    <?php
                    echo "<p class='has-value' id='profile_password'>********</p>";
                    ?>
                    <i class="iconfont icon-arrowright"></i>
                </div>
            </article>
        </section>
    </div>
    <?php require_once("includes/components/update_avatar_modal.php"); ?>
    <?php require_once("includes/components/update_name_modal.php"); ?>
    <?php require_once("includes/components/update_username_modal.php"); ?>
    <?php require_once("includes/components/update_birthday_modal.php"); ?>
    <?php require_once("includes/components/update_gender_modal.php"); ?>
    <?php require_once("includes/components/update_password_modal.php"); ?>
</main>
</body>
</html>