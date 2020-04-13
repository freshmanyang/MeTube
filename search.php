<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once("./includes/class/searchProcessor.php"); ?>
<?php $search = new searchProcessor($conn);?>

<link rel="stylesheet" href="./assets/css/search.css">
<main class="main-section-container" id="main">
    <button class="search-show-hide btn btn-light" >Advanced Search</button>
<!--    <div id="advanced-search-container" style="display:none;">-->
        <div id="advanced-search-container" >
<!--        <form action="searchprocess.php" method="post">-->
            <label for="file_size_min">File_size (between 0 MB and 500 MB):</label>
            Min:<input type="number" id="file_size_min" name="file_size_min" min="0" max="499">
            Max:<input type="number" id="file_size_max" name="file_size_max" min="1" max="500">
            <input type="submit" class="btn btn-outline-info" id="advancedSearch" name = "advancedSearch">
<!--        </form>-->
    </div>
<!--    <div id="main-content-container" class="paddingTop" >-->
    <div id="main-content-container" >
        <hr style="width: 100%; height: 1px; border: none; background-color: #282828">
        <div id="search_result"></div>


        <div id="page-nav">
            <nav aria-label="Page navigation">
            <ul class="pagination" id="pagination"></ul>
            </nav>
        </div>
    </div>
</main>
<script>

    $(document).ready(function () {
        //    change search textbox text
        var keyword= '<?php echo $_GET['search_input']?>';
        document.getElementById("search_input").value= keyword;
        // document.getElementById("search_input").setAttribute("value",keyword);
        // real time catch keyword in input box
        $("#search_input").on("input", function(){
            keyword = $(this).val();
        });

        //advanced search div
        $(".search-show-hide").on("click", function () {
            let main = $("#main-content-container");
            let search = $("#advanced-search-container");
            if (main.hasClass("paddingTop")) {
                search.hide();
            } else {
                search.show();
            }
            // Add or remove one or more class from each element in the set of matched elements,
            main.toggleClass("paddingTop");
        });
        //advancedsearch button click
        $('#advancedSearch').on("click", function () {

            var file_size_min = document.getElementById("file_size_min").value;
            var file_size_max = document.getElementById("file_size_max").value;
            if (parseInt(file_size_min) > parseInt(file_size_max)){
                alert('The File size min value cannot larger than max value');
            }
            else if (parseInt(file_size_min) ==parseInt(file_size_max)){
                alert('The File size min value cannot equal to max value');
            }
            else {
            document.getElementById("search_result").innerHTML="";
            $.ajax({
                type:'POST',
                url:'searchprocess.php',
                data:{
                    advancedSearch:"1",
                    search_input:keyword,
                    file_size_min:file_size_min,
                    file_size_max:file_size_max
                },
                datatype:'json',
                success:function(result){
                    final2 = JSON.parse(result);
                    console.info(final2);
                    datalength = final2.length;
                    if (datalength != 0){
                        $('#pagination').twbsPagination('destroy');
                        window.pagObj = $('#pagination').twbsPagination({
                            totalPages: (datalength % 4) ?  (datalength /4) + 1: datalength /4,
                            visiblePages: 5,
                            onPageClick: function (event, page) {

                                document.getElementById("search_result").innerHTML="";
                                for ($i = 4; $i >0 ; $i--) {
                                    if ( final2[page * 4 - $i]){

                                        document.getElementById("search_result").innerHTML += final2[page * 4 - $i] ;
                                    }
                                }

                            }
                        }).on('page', function (event, page) {

                        });
                    }
                    else{
                        document.getElementById("search_result").innerText ="No matched Videos";
                    }
                }
            })
            }
        });

    //normal search result based on keyword empty or not
    //     if( document.getElementById("search_input").value ==''){
    //         document.getElementById("search_result").innerText='You didn\'t input any keyword';
    //     }
    //     else{

    // normal search page plugin
        $.ajax({
            type:'POST',
            url:'searchprocess.php',
            data:{
                normalSearch:"1",
                search_input:keyword

            },
            datatype:'json',
            success:function(result){
                final1 = JSON.parse(result);
                console.info(final1);
                datalength = final1.length;
                if (datalength != 0){
                    $('#pagination').twbsPagination('destroy');
                    window.pagObj = $('#pagination').twbsPagination({
                        totalPages: (datalength % 4) ?  (datalength /4) + 1: datalength /4,
                        visiblePages: 5,
                        onPageClick: function (event, page) {
                            document.getElementById("search_result").innerHTML="";

                            for ($i = 4; $i >0 ; $i--) {
                                if ( final1[page * 4 - $i]){

                                    document.getElementById("search_result").innerHTML += final1[page * 4 - $i] ;
                                }
                            }

                        }
                    }).on('page', function (event, page) {

                    });
                }
                else{
                    document.getElementById("search_result").innerHTML ="No matched Videos";
                }
            }
        });
        // }   normal search result based on keyword empty or not end
    });
</script>
</body>
</html>
