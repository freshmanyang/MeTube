<nav class="side-nav-container" id="side_nav" style="display: none">
    <a href="./index.php" class="endpoint">
        <div class="endpoint-content">
            <i class="iconfont icon-home"></i>
            <p>Home</p>
        </div>
    </a>
    <a href="./profile.php" class="endpoint">
        <div class="endpoint-content">
            <i class="iconfont icon-profile"></i>
            <p>Profile</p>
        </div>
    </a>
    <a href="./contactList.php" class="endpoint">
        <div class="endpoint-content">
            <i class="iconfont icon-people"></i>
            <p>People & sharing</p>
        </div>
    </a>
    <a href="./upload.php" class="endpoint">
        <div class="endpoint-content">
            <i class="iconfont icon-video"></i>
            <p>Your video</p>
        </div>
    </a>
    <a href="./channel.php?channel=<?php echo $usernameLoggedIn?>" class="endpoint">
        <div class="endpoint-content">
<!--            <i class="iconfont icon-video"></i>-->
            <p>Your Channel</p>
        </div>
    </a>
</nav>