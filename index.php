<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once('./includes/class/showAllVideo.php'); ?>

<main class="main-section-container" id="main">
    <div class="main-content-container">
        <?php
        $showAllVideo = new showAllVideo($conn);
        if(isset($usernameLoggedIn)){
            echo '<div id="main-video-container"> 
                  <div id ="welcomemessage">Welcome to MeTube,'
                .ucfirst($usernameLoggedIn).'<br>';

        }
        if(isset($_GET['category'])){
            echo 'You are under category -'.$_GET['category'];
        }

        ?>
    </div> <!--   welcomemessage div end-->
    <!--category browse button-->
    <div class="btn-group">
        <button type="button" id="category" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php
            if (isset($_GET['category'])){
            echo $_GET['category'];
            }
            else{
                echo 'Category';
            }
            ?>
        </button>
        <div class="dropdown-menu" id="categoryList">
            <a class='dropdown-item' href='#'>All</a>
            <?php
            echo $showAllVideo->getCategoryListWithBlock($usernameLoggedIn);
            ?>
        </div>
    </div>
    <!--category button event handler-->
    <script>
        $(document).ready(function () {
            $('#categoryList a').on('click', function(){
                var category = ($(this).text());
                var href = "index.php?category="+category;
                if(category === 'All'){
                     href = "index.php";

                }
                // alert(category);

                window.location.assign(href);
            });
        });
    </script>

    <div id="showAllVideo">
        <?php
        If(isset($_GET['category'])){
            echo '<div id="categoryvideopage">';
            if(is_array($showAllVideo->getCategoryVideoswithBlock($usernameLoggedIn,$_GET['category']))){
                $videoswithblock = $showAllVideo->getCategoryVideoswithBlock($usernameLoggedIn,$_GET['category']);
//                print_r($videoswithblock);
                foreach($videoswithblock as $value){
                    echo $value;
                }

            }
            else{
            echo $showAllVideo->getCategoryVideoswithBlock($usernameLoggedIn,$_GET['category']);
            }
            echo '</div>';
        }else{
            echo '<div id ="allvideopage"> </div>';
            echo '<div id="page-nav">
           <nav aria-label="Page navigation">
               <ul class="pagination" id="pagination"></ul>
           </nav>
       </div>';
        }
        ?>

    </div>
    </div>
    <!--   main-video-container div end-->

    <script type="text/javascript">
        $(function () {
            $.ajax({
                type:'POST',

                url:'showallvideoprocess.php',
                data:{
                    showallvideo:"1",
                    loginUser:'<?php echo $usernameLoggedIn ?>'

                },
                datatype:'json',
                success:function(result){
                    final = JSON.parse(result);
                    datalength = final.length;
                    window.pagObj = $('#pagination').twbsPagination({
                        // totalPages plugin if you want display 4 records at one page, total pages= total data /4
                        totalPages: (datalength % 4) ?  (datalength /4) + 1: datalength /4,
                        visiblePages: 5,
                        onPageClick: function (event, page) {
                            document.getElementById("allvideopage").innerHTML ="";
                            // console.info(page + ' (from options)');
                            for ($i = 4; $i >0 ; $i--) {
                                if ( !(final[page * 4 - $i] == null)){
                                    document.getElementById("allvideopage").innerHTML +=
                                        final[page * 4 - $i] ;
                                }
                            }
                        }
                    }).on('page', function (event, page) {
                        // console.info(page + ' (from event listening)');
                    });

                }
            });
        });
    </script>
    </div>
</main>
</body>
</html>