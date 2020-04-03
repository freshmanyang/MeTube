<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once('./includes/class/channelProcessor.php');?>
<?php
$channel = new channelProcessor($conn,$_GET['channel'],$usernameLoggedIn);
?>
<link rel="stylesheet" href="./assets/css/playlist.css">
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <form action="channelprocess.php?channel=<?php echo $_GET['channel']?>&playlist=<?php echo $_GET['playlist']?>" method="post">
        <div id="showvideofromplaylist">

        </div>
        </form>
    </div>

<script>
       $(function(){
           var user='<?php echo $_GET['channel']; ?>';
           var playlist='<?php echo $_GET['playlist']; ?>';
            $.ajax({
                type:'POST',
                url:'channelprocess.php',
                data:{
                    showVideoFromPlaylist:"1",
                    user:user,
                    playlist:playlist
                },
                datatype:'json',
                success:function(result){
                    final = JSON.parse(result);
                    console.log(final);
                    final.forEach(arrayfunction);
                    function arrayfunction(value){
                        document.getElementById("showvideofromplaylist").innerHTML += value;
                    }
                }
                });
       });
</script>



</main>
</body>
</html>
