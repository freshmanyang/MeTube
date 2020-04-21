<nav class="side-nav-container" id="side_nav" style="display: none">
    <a href="index.php" class="endpoint" id="to_index">
        <div class="endpoint-content">
            <i class="iconfont icon-home"></i>
            <p>Home</p>
        </div>
    </a>
    <a href="profile.php" class="endpoint" id="to_profile">
        <div class="endpoint-content">
            <i class="iconfont icon-profile"></i>
            <p>Profile</p>
        </div>
    </a>
    <a href="contactList.php" class="endpoint" id="to_contact_list">
        <div class="endpoint-content">
            <i class="iconfont icon-people"></i>
            <p>People & sharing</p>
        </div>
    </a>
    <a href="community.php" class="endpoint" id="to_community">
        <div class="endpoint-content">
            <i class="iconfont icon-Community" style="font-weight: bold"></i>
            <p>Community</p>
        </div>
    </a>
    <a href="channel.php?channel=<?php echo $usernameLoggedIn?>" class="endpoint" id="to_channel">
        <div class="endpoint-content">
            <i class="iconfont icon-video"></i>
            <p>Your Channel</p>
        </div>
    </a>
</nav>