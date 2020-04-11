<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once("./includes/class/VideoPlayer.php"); ?>
<?php require_once('./includes/class/channelProcessor.php'); ?>
<?php
$channel = new channelProcessor($conn,'',$usernameLoggedIn);

?>
<link rel="stylesheet" href="./assets/css/watch.css">
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
        if (!isset($_GET['vid'])) {
            echo "Invalid video id";
            exit();
        }
        $videoObj = new Video($conn, $_GET['vid'], $userLoginInObj);
        $videoObj->incrementView();
        $videoPlayer = new VideoPlayer($videoObj);
        ?>
        <div class="watch-left">
            <?php
                echo $videoPlayer->create(false);
            ?>
            <button type="button"  class="btn btn-primary"  id='addtoplaylist'>Add Video to Playlist</button>
            <?php echo  $channel->showsubscribe($_GET['vid']);?>
        </div>
        <div class="suggestion">

        </div>
    </div>

<!-- modal   add video to playlist -->
    <div class="modal" id ="addtoPlaylistModal" tabindex="-1" role="dialog" ">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="">
                    <!--playlist browse dropdownlist-->
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="updateplaylistname">Playlist</span>
                    </button>
                    <div class="dropdown-menu" id="playlist">

                       <?php
                        echo $channel->showPlaylistDropdown();
                        ?>
                    </div>
                </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" id="addVideotoPlaylist" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>

    <!--subscribe button alert modal-->
    <div class="modal" id ="myModal" tabindex="-1" role="dialog" data-backdrop ='static' data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message</h5>
                </div>
                <div class="modal-body">
                    <p id="subscribeResult"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirm" class="btn btn-primary">Confirm</button>

                </div>
            </div>
        </div>
    </div>


    <script>
        //    subscribe button handler
        var subscribebtn = document.getElementById("subscribe");
        var unsubscribebtn = document.getElementById("unsubscribe");
        var whichbutton;
        if(subscribebtn){
            whichbutton = 1;
            subscribebtn.addEventListener("click", popup);
        }
        if(unsubscribebtn){
            whichbutton = 2;
            unsubscribebtn.addEventListener("click", popup);
        }
        function popup(){
            var user='<?php echo $channel->fromVideoGetChannel($_GET['vid']); ?>';
            $.ajax({
                type:'POST',
                url:'channelprocess.php',
                data:{
                    subscribe:"1",
                    button:whichbutton,
                    user:user
                },
                success:function(result) {
                    $('#myModal').modal("show");
                    document.getElementById("subscribeResult").innerText = result;

                }

            })

        };
        //subscribe button modal confirm button
        $("#confirm").on('click', function() {
            <?php if($usernameLoggedIn) {
            echo 'location.href = \'watch.php?vid='.$_GET['vid'].'\';';
        }else{
            echo 'location.href = \'index.php\'';
        }?>

        });
        <!-- handle add video to playlist button  -->
        var playlist='';
        $("#addtoplaylist").on('click', function() {
            $('#addtoPlaylistModal').modal("show");
        });
        $('#playlist a').on('click', function(){
             playlist = ($(this).text());
            $("#updateplaylistname").text(playlist);
        });
        $("#addVideotoPlaylist").on('click', function() {
             var href = "channelprocess.php?videoaddtoplaylist="+playlist+"&vid="+ <?php echo $_GET['vid']?>;
             window.location.assign(href);
        });




    </script>


</main>
</body>
</html>
