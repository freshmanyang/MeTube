<div class="modal" id="addtoPlaylistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose a playlist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="">
                    <!--playlist browse dropdownlist-->
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
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

<script type="text/javascript">
    <!-- handle add video to playlist button  -->
    let playlist = '';
    $("#save_btn").on('click', function () {
        $('#addtoPlaylistModal').modal("show");
    });
    $('#playlist a').on('click', function () {
        playlist = ($(this).text());
        $("#updateplaylistname").text(playlist);
    });
    $("#addVideotoPlaylist").on('click', function () {
        var href = "channelprocess.php?videoaddtoplaylist=" + playlist + "&vid=" + <?php echo $_GET['vid']?>;
        window.location.assign(href);
    });
</script>