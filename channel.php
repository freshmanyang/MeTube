<?php
require_once('./includes/header.php');
require_once('./includes/class/channelProcessor.php');
require_once("./includes/nav.php");
if(!isset($_GET['channel'])){
    echo "<script>alert('You are not choose any channel, redirect to Home page after click'); location.href = 'index.php';</script>";
}
$channel = new channelProcessor($conn,$_GET['channel'],$usernameLoggedIn);

?>
<main class="main-section-container" id="main">
    <div class="main-content-container">
<div id ="channelPage">

<div class="container">
    <h2>Welcome to <?php echo ucfirst($_GET['channel'])?>'s Channel</h2>
    <?php
    if(!strcmp($usernameLoggedIn,$_GET['channel'])) {
        echo $channel->showall();
    }
    else{
        echo $channel->showchannelonly($_GET['channel']);
    }
     ?>

</div>

</div>

<!--  create playlist button modal-->
<div class="modal" id ="createPlaylistModal" tabindex="-1" role="dialog" ">
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
                <form id="playList_Form" action="channelprocess.php?channel=<?php echo $_GET['channel']?>" enctype="multipart/form-data" method="POST">
                <label for="PlayList">PlayList Name:</label>
                    <input type="text" id="PlayList" name="PlayList">
                </form>

                </p>
            </div>
            <div class="modal-footer">
                <button type="button" id="addPlayListToDB" class="btn btn-primary">Add</button>
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


    <script type="text/javascript">
    //handler create playlist button
    $("#createPlayList").on('click', function() {
        $('#createPlaylistModal').modal("show");
    });
    //submit add playlist form
    $("#addPlayListToDB").on('click', function() {
        $("#playList_Form").submit();

    });

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
        var user='<?php echo $_GET['channel']; ?>';
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
    //subscribe modal confirm button
    $("#confirm").on('click', function() {
        <?php if($usernameLoggedIn) {
            echo 'location.href = \'channel.php?channel='.$_GET['channel'].'\';';
        }else{
            echo 'location.href = \'index.php\'';
        }?>

    });

    // Videos sorting button
    //$('#sortingList a').on('click', function(){
    //    var sortingName = ($(this).text());
    //    // alert(category);
    //    var channel = '<?php //echo $_GET['channel'];?>//';
    //    var href = "channelprocess.php?sorting="+sortingName+"&channel="+channel;
    //    window.location.assign(href);
    //});


//            page plugin    $(function () is jQuery short-hand for $(document).ready(function() { ... });
         $(function () {
             var user='<?php echo $_GET['channel']; ?>';
         //channel tab + page function
         $.ajax({
             type:'POST',
             // url:'includes/class/channelProcessor.php',
              url:'channelprocess.php',
             data:{
                 pagefunction:"1",
                 user:user
             },
             datatype:'json',
             success:function(result){
                 final1 = JSON.parse(result);

                  datalength = final1.length;
                  if (datalength != 0){

                 window.pagObj = $('#pagination').twbsPagination({

                     totalPages: (datalength % 4) ?  (datalength /4) + 1: datalength /4,
                     visiblePages: 5,
                     onPageClick: function (event, page) {

                         document.getElementById("show").innerHTML="";


                         // console.log(page + ' (from options)');
                         for ($i = 4; $i >0 ; $i--) {
                             if ( final1[page * 4 - $i]){
                                 // console.log('channelresult=',final1[page * 4 - $i]);
                                 document.getElementById("show").innerHTML += final1[page * 4 - $i] ;
                             }
                         }

                     }
                 }).on('page', function (event, page) {
                     // console.info(page + ' (from event listening)');
                 });

                  }
                       else{
                      document.getElementById("show").innerHTML ="";
                  }
             }
         });
     //    mysubscriptions
             $('#myTab1 a[href="#mySubscriptions"]').on("click", function () {
                 $.ajax({
                     type: 'POST',
                     url: 'channelprocess.php',
                     data: {
                         mysubscribe: "1",
                         user: user
                     },
                     datatype: 'json',
                     success: function (result) {
                         final = JSON.parse(result);
                         document.getElementById("showSubscriptions").innerHTML="";
                         // console.log(final);
                         // https://www.w3schools.com/js/js_array_iteration.asp
                         final.forEach(arrayfunction);

                         function arrayfunction(value) {
                             document.getElementById("showSubscriptions").innerHTML += value;
                         }

                     }
                 });
             });
     //      Myplaylist
     //         $('#myTab1 a[href="#myPlayList2"]').on("click", function () {
              $.ajax({
                  type:'POST',
                  url:'channelprocess.php',
                  data:{
                      myplaylist:"1",
                      user:user
                  },
                  datatype:'json',
                  success:function(result){
                      final2 = JSON.parse(result);
                      // console.log(final);
                      document.getElementById("showMyPlayList").innerHTML += final2;
                  }
              });
             // });
             //my FavoriteList
             // $('#myTab1 a[href="#myFavoriteList2"]').on("click", function () {
                 $.ajax({
                     type:'POST',
                     url:'channelprocess.php',
                     data:{
                         myFavoritelist:"1",
                         user:user
                     },
                     datatype:'json',
                     success:function(result){

                         final3 = JSON.parse(result);
                         final3.forEach(arrayfunction);
                         function arrayfunction(value) {
                             document.getElementById("showMyFavoriteList").innerHTML += value;
                         }


                     }
                 });
             // });

             //Videos sorting tab
             var sortingName ='';
             $('#sortingList a').on('click', function() {
                 sortingName = ($(this).text());
                 $("#sortingvideos").text(sortingName);
                 $.ajax({
                     type:'POST',
                     // url:'includes/class/channelProcessor.php',
                     url:'channelprocess.php',
                     data:{
                         sortingVideos:"1",
                         user:user,
                         sorting:sortingName
                     },
                     datatype:'json',
                     success:function(result){

                         final5 = JSON.parse(result);

                         datalength = final5.length;

                         if (datalength != 0){
                             //when change different sorting item, must destroy first
                             $('#pagination-sorting').twbsPagination('destroy');
                             window.pagObj = $('#pagination-sorting').twbsPagination({
                                 totalPages: (datalength % 4) ?  (datalength /4) + 1: datalength /4,
                                 visiblePages: 5,
                                 onPageClick: function (event, page) {
                                     document.getElementById("showSortingVideos").innerHTML="";
                                     for ($i = 4; $i >0 ; $i--) {
                                         if ( final5[page * 4 - $i]){

                                             document.getElementById("showSortingVideos").innerHTML += final5[page * 4 - $i] ;
                                         }
                                     }

                                 }
                             }).on('page', function (event, page) {
                                 // console.info(page + ' (from event listening)');
                             });

                         }else{
                             document.getElementById("showSortingVideos").innerHTML ="";
                         }
                      }
                  });

             });


     });



 </script>


     <?php
    // when $_GET['tab'] has value  auto jump to myplaylist tab
        if(isset($_GET['tab'])){
         echo '<script> $(function () {$(\'#myTab1 a[href="#'.$_GET['tab'].'"]\').tab(\'show\')});</script>';
     }
        ?>

    </div>
</main>
</body>
</html>

