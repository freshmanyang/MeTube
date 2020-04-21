<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<?php require_once('./includes/class/channelProcessor.php'); ?>
<?php
if (empty($usernameLoggedIn)) {
    echo "<script>alert('You are not login, redirect to home page after click'); location.href = 'index.php';</script>";
//    header("refresh:3;url=index.php");
}
$channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
?>
<link rel="stylesheet" href="assets/css/playlist.css">
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <form action="channelprocess.php?channel=<?php echo $_GET['channel'] ?>&playlist=<?php echo $_GET['playlist'] ?>"
              method="post">
            <div id="showvideofromplaylist">
            </div>
            <div>
                <?php if ($usernameLoggedIn) {
                    echo " <label for=\"selectoneplaylistbtn\">Select All:</label>
                <input type=\"checkbox\" id=\"selectoneplaylistbtn\"  value=\"Select All\"/>";
                }
                ?>
                <div id="showvideosrecord"></div>
            </div>
        </form>
    </div>
    <script>
        $(function () {
            var user = '<?php echo $_GET['channel']; ?>';
            var playlist = '<?php echo $_GET['playlist']; ?>';
            $.ajax({
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    showVideoFromPlaylist: "1",
                    user: user,
                    playlist: playlist
                },
                datatype: 'json',
                success: function (result) {
                    final = JSON.parse(result);
                    final.forEach(arrayfunction);

                    function arrayfunction(value) {
                        document.getElementById("showvideofromplaylist").innerHTML += value;
                    }
                }
            });
            $.ajax({
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    showVideoFromPlaylistrecord: "1",
                    user: user,
                    playlist: playlist
                },
                datatype: 'json',
                success: function (result) {
                    final = JSON.parse(result);
                    final.forEach(arrayfunction);

                    function arrayfunction(value) {
                        document.getElementById("showvideosrecord").innerHTML += value;
                    }
                }
            });
// select all btn or unselect all
            var selectoneplaylistbtn = document.getElementById("selectoneplaylistbtn");
            var selectedPlayList = document.getElementsByName('videoinplayList[]');
            if (selectoneplaylistbtn) {
                $('#selectoneplaylistbtn').click(function () {
                    if (this.checked) {
                        // Iterate each checkbox
                        $(selectedPlayList).each(function () {
                            this.checked = true;
                        });
                    } else {
                        $(selectedPlayList).each(function () {
                            this.checked = false;
                        });
                    }
                });
            }
        });
    </script>
</main>
</body>
</html>
