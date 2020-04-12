<?php require_once("./includes/header.php"); ?>
<?php require_once("./includes/nav.php"); ?>
<?php require_once("./includes/class/searchProcessor.php"); ?>
<?php $search = new searchProcessor($conn,$usernameLoggedIn);?>
<link rel="stylesheet" href="./assets/css/search.css">
<main class="main-section-container" id="main">
    <button class="search-show-hide btn btn-light" >Advanced Search</button>
<!--    <div id="advanced-search-container" style="display:none;">-->
        <div id="advanced-search-container" >
        <form action="search.php" method="post">
            <label for="file_size_min">File_size (between 0 MB and 500 MB):</label>
            Min:<input type="number" id="file_size_min" name="file_size_min" min="0" max="499">
            Max:<input type="number" id="file_size_max" name="file_size_max" min="1" max="500">
            <input type="submit" class="btn btn-outline-info" id="advancedSearch" name = "advancedSearch">
        </form>
    </div>
    <div id="main-content-container" class="paddingTop" >
        <div id="search_result">
            search_result
            <?php if(isset($_POST['advancedSearch'])){
                $searchResult = $search->showAdvancedSearch($_POST['file_size_min'],$_POST['file_size_max']);
                foreach($searchResult as )
                //                var_dump($_POST['file_size_min']);
//                echo  $_POST['file_size_min'];
//                echo  $_POST['file_size_max'];
            }
            ?>
        </div>
    </div>
</main>
<script>
    $(document).ready(function () {

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
        })
    });
</script>
</body>
</html>
