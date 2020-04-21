<?php
require_once('./includes/header.php');
require_once('./includes/class/channelProcessor.php');
require_once("includes/nav.php");
if ((!isset($_GET['channel'])) || $_GET['channel'] == "") {
    echo "<script>alert('You are not choose any channel, redirect to Home page after click'); location.href = 'index.php';</script>";
}
$channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
?>
<main class="main-section-container" id="main">
    <div class="main-content-container">
        <div id="channelPage" channel-user-id="<?php echo $channel->getUserIdFromUsername($_GET['channel'])?>">
            <div class="container">
                <h2>Welcome to <?php echo $_GET['channel'] ?>'s Channel</h2>
                <?php
                if (!strcmp($usernameLoggedIn, $_GET['channel'])) {
                    echo $channel->showall();
                } else {
                    echo $channel->showchannelonly($_GET['channel']);
                }
                ?>
            </div>
        </div>
        <!--  create playlist button modal-->
        <div class="modal" id="createPlaylistModal" tabindex="-1" role="dialog"
        ">
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
                    <form id="playList_Form" action="channelprocess.php?channel=<?php echo $_GET['channel'] ?>"
                          enctype="multipart/form-data" method="POST">
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
    <div class="modal" id="myModal" tabindex="-1" role="dialog" data-backdrop='static' data-keyboard="false">
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
    <!--set videos privacy modal-->
    <div class="modal" id="privacyModal" tabindex="-1" role="dialog" data-backdrop='static' data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Privacy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="btn-group">
                        <button type="button" id="privacyList" class="btn btn-success dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Privacy
                        </button>
                        <div class="dropdown-menu" id="Privacy">
                            <a class='dropdown-item' href='#'>Public</a>
                            <a class='dropdown-item' href='#'>Private</a>
                            <a class='dropdown-item' href='#'>Friends</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmSetPrivacy" class="btn btn-primary">Set</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--End set videos privacy modal-->
    <script type="text/javascript">
        //handler create playlist button
        $("#createPlayList").on('click', function () {
            $('#createPlaylistModal').modal("show");
        });
        //submit add playlist form
        $("#addPlayListToDB").on('click', function () {
            $("#playList_Form").submit();
        });
        //modal set videos privacy button
        $("#setPrivacy").on('click', function () {
            $('#privacyModal').modal("show");
        });
        // channel tab select all btn or unselect all
        var selectallbtn = document.getElementById("selectallbtn");
        var selectedVideos = document.getElementsByName('videoList[]');
        if (selectallbtn) {
            $('#selectallbtn').click(function () {
                if (this.checked) {
                    // Iterate each checkbox
                    $(selectedVideos).each(function () {
                        this.checked = true;
                    });
                } else {
                    $(selectedVideos).each(function () {
                        this.checked = false;
                    });
                }
            });
        }
        // favoritelist tab select all btn or unselect all
        var selectfavoritelistbtn = document.getElementById("selectfavoritelistbtn");
        var favoriteList = document.getElementsByName('favoriteList[]');
        if (selectfavoritelistbtn) {
            $('#selectfavoritelistbtn').click(function () {
                if (this.checked) {
                    // Iterate each checkbox
                    $(favoriteList).each(function () {
                        this.checked = true;
                    });
                } else {
                    $(favoriteList).each(function () {
                        this.checked = false;
                    });
                }
            });
        }
        // my playlist tab select all btn or unselect all
        var selectplaylistbtn = document.getElementById("selectplaylistbtn");
        var selectedPlayList = document.getElementsByName('selectedPlayList[]');
        if (selectplaylistbtn) {
            $('#selectplaylistbtn').click(function () {
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
        // Add friend button handler
        var addfriendbtn = document.getElementById("addFriend");
        var removefriendbtn = document.getElementById("removeFriend");
        if (addfriendbtn) {
            addfriendbtn.addEventListener("click", addFriend);
        }
        if (removefriendbtn) {
            removefriendbtn.addEventListener("click", removeFriend);
        }

        function addFriend() {
            var user = '<?php echo $_GET['channel']; ?>';
            $.ajax({
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    addFriend: "1",
                    user: user
                },
                success: function (result) {
                    if (result == '') {
                        $("#sign_in_modal").modal('show');
                    } else {
                        alert(result);
                        location.reload()
                    }
                }
            })
        };

        function removeFriend() {
            var user = '<?php echo $_GET['channel']; ?>';
            $.ajax({
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    removeFriend: "1",
                    user: user
                },
                success: function (result) {
                    if (result == '') {
                        $("#sign_in_modal").modal('show');
                    } else {
                        alert(result);
                        location.reload();
                    }
                }
            })
        };
        //    subscribe button handler
        var subscribebtn = document.getElementById("subscribe");
        var unsubscribebtn = document.getElementById("unsubscribe");
        var whichbutton;
        if (subscribebtn) {
            whichbutton = 1;
            subscribebtn.addEventListener("click", popup);
        }
        if (unsubscribebtn) {
            whichbutton = 2;
            unsubscribebtn.addEventListener("click", popup);
        }

        function popup() {
            var user = '<?php echo $_GET['channel']; ?>';
            $.ajax({
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    subscribe: "1",
                    button: whichbutton,
                    user: user
                },
                success: function (result) {
                    if (result == '') {
                        $("#sign_in_modal").modal('show');
                    } else {
                        $('#myModal').modal("show");
                        document.getElementById("subscribeResult").innerText = result;
                    }
                }
            })
        };
        //subscribe modal confirm button
        $("#confirm").on('click', function () {
            <?php if ($usernameLoggedIn) {
            echo 'location.href = \'channel.php?channel=' . $_GET['channel'] . '\';';
        } else {
            echo 'location.href = \'index.php\'';
        }?>
        });
        //          $(function () is jQuery short-hand for $(document).ready(function() { ... });
        $(function () {
            var user = '<?php echo $_GET['channel']; ?>';
            // setup how many records in one page for channel and videos sorting tab
            var recordsPerPage = 6;
//set privacy modal confirm button
            var privacy = '';
            $('#Privacy a').on('click', function () {
                privacy = ($(this).text());
                $("#privacyList").text(privacy);
            });
            //handle set privacy
            $('#confirmSetPrivacy').on("click", function () {
                var selectedVideos = document.getElementsByName('videoList[]');
                var videolist = new Array();
                for (var i = 0; i < selectedVideos.length; i++) {
                    if (selectedVideos[i].checked) {
                        videolist.push(selectedVideos[i].value);
                    }
                }
                $.ajax({
                    type: 'POST',
                    url: 'channelprocess.php',
                    data: {
                        setPrivacy: "1",
                        user: user,
                        privacy: privacy,
                        videolist: videolist
                    },
                    success: function (result) {
                        $('#privacyModal').modal("hide");
                        alert(result);
                    }
                });
            });
//channel tab + page function
            $.ajax({
                type: 'POST',
                // url:'includes/class/channelProcessor.php',
                url: 'channelprocess.php',
                data: {
                    pagefunction: "1",
                    user: user
                },
                datatype: 'json',
                success: function (result) {
                    final1 = JSON.parse(result);
                    datalength = final1.length;
                    if (datalength != 0) {
                        window.pagObj = $('#pagination').twbsPagination({
                            totalPages: (datalength % recordsPerPage) ? (datalength / recordsPerPage) + 1 : datalength / recordsPerPage,
                            visiblePages: 5,
                            onPageClick: function (event, page) {
                                document.getElementById("show").innerHTML = "";
// console.log(page + ' (from options)');
                                for ($i = recordsPerPage; $i > 0; $i--) {
                                    if (final1[page * recordsPerPage - $i]) {
                                        // console.log('channelresult=',final1[page * 4 - $i]);
                                        document.getElementById("show").innerHTML += final1[page * recordsPerPage - $i];
                                    }
                                }
                            }
                        }).on('page', function (event, page) {
                            // console.info(page + ' (from event listening)');
                        });
                    } else {
                        document.getElementById("show").innerHTML = "This channel doesn't have any videos yet";
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
                        document.getElementById("showSubscriptions").innerHTML = "";
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
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    myplaylist: "1",
                    user: user
                },
                datatype: 'json',
                success: function (result) {
                    final2 = JSON.parse(result);
                    var showMyPlayList = document.getElementById("showMyPlayList");
                    // console.log(final);
                    if (showMyPlayList) {
                        showMyPlayList.innerHTML += final2;
                    }
                }
            });
// });
            //my FavoriteList
            // $('#myTab1 a[href="#myFavoriteList2"]').on("click", function () {
            $.ajax({
                type: 'POST',
                url: 'channelprocess.php',
                data: {
                    myFavoritelist: "1",
                    user: user
                },
                datatype: 'json',
                success: function (result) {
                    final3 = JSON.parse(result);
                    final3.forEach(arrayfunction);

                    function arrayfunction(value) {
                        if (document.getElementById("showMyFavoriteList")) {
                            document.getElementById("showMyFavoriteList").innerHTML += value;
                        }
                    }
                }
            });
            // });
//Videos sorting tab
            var sortingName = '';
            $('#sortingList a').on('click', function () {
                sortingName = ($(this).text());
                $("#sortingvideos").text(sortingName);
                $.ajax({
                    type: 'POST',
                    // url:'includes/class/channelProcessor.php',
                    url: 'channelprocess.php',
                    data: {
                        sortingVideos: "1",
                        user: user,
                        sorting: sortingName
                    },
                    datatype: 'json',
                    success: function (result) {
                        final5 = JSON.parse(result);
                        datalength = final5.length;
                        if (datalength != 0) {
                            //when change different sorting item, must destroy first
                            $('#pagination-sorting').twbsPagination('destroy');
                            window.pagObj = $('#pagination-sorting').twbsPagination({
                                totalPages: (datalength % recordsPerPage) ? (datalength / recordsPerPage) + 1 : datalength / recordsPerPage,
                                visiblePages: 5,
                                onPageClick: function (event, page) {
                                    document.getElementById("showSortingVideos").innerHTML = "";
                                    for ($i = recordsPerPage; $i > 0; $i--) {
                                        if (final5[page * recordsPerPage - $i]) {
                                            document.getElementById("showSortingVideos").innerHTML += final5[page * recordsPerPage - $i];
                                        }
                                    }
                                }
                            }).on('page', function (event, page) {
                                // console.info(page + ' (from event listening)');
                            });
                        } else {
                            document.getElementById("showSortingVideos").innerHTML = "";
                        }
                    }
                });
            });
            //       Downloaded Videos record
            $('#myTab1 a[href="#downloadedVideos2"]').on("click", function () {
                $.ajax({
                    type: 'POST',
                    // url:'includes/class/channelProcessor.php',
                    url: 'channelprocess.php',
                    data: {
                        downloadvideo: "1",
                        user: user,
                    },
                    datatype: 'json',
                    success: function (result) {
                        var tablecontent = '';
                        final6 = JSON.parse(result);
                        //clear table data first
                        $('#showdownload').empty('');
                        if (!Array.isArray(final6)) {
                            document.getElementById("ShowDownloadedVideos").innerText = 'No Downloaded Videos records';
                        } else {
                            $('#showdownload').append($("<thead>" +
                                "<tr>" +
                                "<th scope=\"col\">#</th>" +
                                "<th scope=\"col\">Title</th>" +
                                "<th scope=\"col\">Upload_By</th>" +
                                "<th scope=\"col\">Upload_Date</th>" +
                                "<th scope=\"col\">Duration</th>" +
                                "<th scope=\"col\">File_Size</th>" +
                                "</tr>" +
                                "</thead>" +
                                "<tbody>"));
                            $.each(final6, function (index, item) {
                                tablecontent += item;
                            })
                            tablecontent += "</tbody>";
                            $('#showdownload').append(tablecontent);
                        }
                    }
                });
            });
        });
    </script>
    <?php
    // when $_GET['tab'] has value  auto jump to myplaylist tab
    if (isset($_GET['tab'])) {
        echo '<script> $(function () {$(\'#myTab1 a[href="#' . $_GET['tab'] . '"]\').tab(\'show\')});</script>';
    }
    ?>
    </div>
</main>
</body>
</html>
