<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<?php require_once("includes/class/searchProcessor.php"); ?>
<?php require_once('./includes/class/showAllVideo.php'); ?>
<?php $search = new searchProcessor($conn); ?>
<?php $showAllVideo = new showAllVideo($conn); ?>
<link rel="stylesheet" href="assets/css/search.css">
<main class="main-section-container" id="main">
    <div id="hot_keyword"></div>
    <button class="search-show-hide btn btn-outline-dark">Advanced Search</button>
    <div id="advanced-search-container" style="display:none;">
        <!--        <div id="advanced-search-container" >-->
        <div id="first_row">
         <form>
            <label for="videoTitle">Video Title</label>
            <input type="text" id="videoTitle" placeholder="keyword" size="10">
            <label for="upload_by">Upload By:</label>
            <input type="text" id="upload_by" placeholder="User Name" size="10">
            <label for="description">Description:</label>
            <input type="text" id="description" placeholder="keyword" size="10">
            <div class="btn-group">
                <button type="button" id="category" class="btn btn-outline-dark dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Category
                </button>
                <div class="dropdown-menu" id="categoryList">
                    <a class='dropdown-item' href='#'>All</a>
                    <?php
                    echo $showAllVideo->getCategoryListWithBlock($usernameLoggedIn);
                    ?>
                </div>
            </div>
        </div>
        <P>
            <label for="file_size_min">File_size (between 0 MB and 500 MB):</label>
            Min:<input type="number" id="file_size_min" name="file_size_min" placeholder="0" min="0" max="499"
                       SIZE="10">
            Max:<input type="number" id="file_size_max" name="file_size_max" placeholder="500" min="1" max="500"
                       SIZE="10">
        </P>
        <P>
            <label for="duration_min">Duration (between 0 and 120 Minute):</label>
            Min:<input type="number" id="duration_min" name="duration_min" placeholder="0" min="0" max="119" SIZE="10">
            Max:<input type="number" id="duration_max" name="duration_max" placeholder="120" min="1" max="120"
                       SIZE="10">
        </P>
        <P>
            <label for="views_min">Views (between 0 and 500 Views):</label>
            Min:<input type="number" id="views_min" name="views_min" placeholder="0" min="0" max="499" SIZE="10">
            Max:<input type="number" id="views_max" name="views_max" placeholder="500" min="1" max="500" SIZE="10">
        </P>
        <P>
            <label for="uplodate">Upload Date:</label>
            <!--            <input type="datetime-local" id="uplodate_start" name="uplodate_start" >-->
            <!--            <input type="datetime-local" id="uplodate_end" name="uplodate_end" >-->
            <input type="datetime-local" id="uplodate_start" name="uplodate_start" value='2020-04-20T08:00'>
            <input type="datetime-local" id="uplodate_end" name="uplodate_end" value='2020-04-25T08:00'>
        </P>
        <input type="submit" class="btn btn-outline-info" id="advancedSearch" name="advancedSearch">
        <input type="reset" class="btn btn-outline-dark">
        </form>
    </div>
    <!--    <div id="main-content-container" class="paddingTop" >-->
    <div id="main-content-container">
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
        var keyword = '<?php echo $_GET['search_input']?>';
        var category = '';
        document.getElementById("search_input").value = keyword;
        // document.getElementById("search_input").setAttribute("value",keyword);
        // real time catch keyword in input box
        $("#search_input").on("input", function () {
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
        //category list dropdown button
        $('#categoryList a').on('click', function () {
            category = ($(this).text());
            document.getElementById("category").innerHTML = category;
        });
        //advancedsearch button click
        $('#advancedSearch').on("click", function (event) {
            event.preventDefault();
            var file_size_min = document.getElementById("file_size_min").value;
            var file_size_max = document.getElementById("file_size_max").value;
            var duration_min = document.getElementById("duration_min").value;
            var duration_max = document.getElementById("duration_max").value;
            var views_min = document.getElementById("views_min").value;
            var views_max = document.getElementById("views_max").value;
            var uplodate_start = document.getElementById("uplodate_start").value;
            var uplodate_end = document.getElementById("uplodate_end").value;
            var videoTitle = document.getElementById("videoTitle").value;
            var upload_by = document.getElementById("upload_by").value;
            var description = document.getElementById("description").value;
            if ((parseInt(file_size_min) > parseInt(file_size_max)) || (parseInt(duration_min) > parseInt(duration_max))) {
                alert('The min value cannot larger than max value');
            } else if ((parseInt(file_size_min) == parseInt(file_size_max)) || (parseInt(duration_min) == parseInt(duration_max))) {
                alert('The min value cannot equal to max value');
            } else {
                document.getElementById("search_result").innerHTML = "";
                $.ajax({
                    type: 'POST',
                    url: 'searchprocess.php',
                    data: {
                        advancedSearch: "1",
                        search_input: keyword,
                        file_size_min: file_size_min,
                        file_size_max: file_size_max,
                        duration_min: duration_min,
                        duration_max: duration_max,
                        views_min: views_min,
                        views_max: views_max,
                        uplodate_start: uplodate_start,
                        uplodate_end: uplodate_end,
                        category: category,
                        videoTitle: videoTitle,
                        upload_by: upload_by,
                        description: description
                    },
                    datatype: 'json',
                    success: function (result) {
                        final2 = JSON.parse(result);
                        datalength = final2.length;
                        if (datalength != 0) {
                            $('#pagination').twbsPagination('destroy');
                            window.pagObj = $('#pagination').twbsPagination({
                                totalPages: (datalength % 4) ? (datalength / 4) + 1 : datalength / 4,
                                visiblePages: 5,
                                onPageClick: function (event, page) {
                                    document.getElementById("search_result").innerHTML = "";
                                    for ($i = 4; $i > 0; $i--) {
                                        if (final2[page * 4 - $i]) {
                                            document.getElementById("search_result").innerHTML += final2[page * 4 - $i];
                                        }
                                    }
                                }
                            }).on('page', function (event, page) {
                            });
                        } else {
                            $('#pagination').twbsPagination('destroy');
                            document.getElementById("search_result").innerText = "No matched Videos";
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
            type: 'POST',
            url: 'searchprocess.php',
            data: {
                normalSearch: "1",
                search_input: keyword
            },
            datatype: 'json',
            success: function (result) {
                final1 = JSON.parse(result);
                datalength = final1.length;
                if (datalength != 0) {
                    $('#pagination').twbsPagination('destroy');
                    window.pagObj = $('#pagination').twbsPagination({
                        totalPages: (datalength % 4) ? (datalength / 4) + 1 : datalength / 4,
                        visiblePages: 5,
                        onPageClick: function (event, page) {
                            document.getElementById("search_result").innerHTML = "";
                            for ($i = 4; $i > 0; $i--) {
                                if (final1[page * 4 - $i]) {
                                    document.getElementById("search_result").innerHTML += final1[page * 4 - $i];
                                }
                            }
                        }
                    }).on('page', function (event, page) {
                    });
                } else {
                    $('#pagination').twbsPagination('destroy');
                    document.getElementById("search_result").innerHTML = "No matched Videos";
                }
            }
        });
        // }   normal search result based on keyword empty or not end
        //    fetch hot keyword data
        $.ajax({
            type: 'POST',
            url: 'searchprocess.php',
            data: {
                hotkeyword: "1",
            },
            datatype: 'json',
            success: function (result) {
                final3 = JSON.parse(result);
                document.getElementById("hot_keyword").innerHTML = "Popular Keyword:&nbsp";
                $.each(final3, function (index, value) {
                    document.getElementById("hot_keyword").innerHTML += value;
                });
            }
        });
        //set search date range one week before to today
         var currentDate = new Date();
         var oneWeekAgo="", now="";
         var stampbefore = currentDate.setDate(currentDate.getDate() - 7);
         var stamp= new Date().getTime();
         var date = new Date(stampbefore);
         var today = new Date(stamp);
         //+1 means seconds to miliseconds '2020-04-20T08:00'  date=new Date(stampbefore*1000);
         //get one week ago date
         var month =date.getMonth() +1;
         var day = date.getDate();
         var hours = date.getHours()+1;
         if (month <10) month = "0" + month;      
         if (day < 10) day = "0" + day;
         if (hours < 10) hours = "0" + hours;
         //get today's date
         var todaymonth =today.getMonth() +1;
         var todaydate = today.getDate();
         var todayhours = today.getHours()+1;
         if (todaymonth <10) todaymonth = "0" + todaymonth;      
         if (todaydate < 10) todaydate = "0" + todaydate;
         if (todayhours < 10) todayhours = "0" + todayhours;
         oneWeekAgo += date.getFullYear()+'-'+month+'-'+day;
         now += today.getFullYear()+'-'+todaymonth+'-'+todaydate;
         document.getElementById("uplodate_start").value = oneWeekAgo+"T"+hours+":00";
         document.getElementById("uplodate_end").value = now+"T"+todayhours+":00";
    });
</script>
</body>
</html>
