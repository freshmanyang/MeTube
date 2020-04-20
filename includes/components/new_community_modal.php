<div class="modal fade" id="new_community_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">New Community</h5>
            </div>
            <div class="modal-body">
                <input id="text_area" type="text" maxlength="70" style="width:100%;" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="submit_btn">Submit</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#new_community_modal").on("hidden.bs.modal", function () {
        $("#text_area").val("");
    });

    $("#submit_btn").on("click", function () {
        let user_id = $('.header-popup-wrapper img').attr('user-id');
        let name = $("#text_area").val();
        if (!name){
            $("#new_community_modal").modal('hide');
            $("#warning_message").text('Empty community name.');
            $("#alert_modal").modal('show');
            return;
        }
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {create_community: true, community_name: name, user_id: user_id},
            dataType: 'json',
            beforeSend: function () {
                $("#new_community_modal").modal('hide');
            },
            success: function (res) {
                if (res.status && res.data.length !== 0) {
                    let body = $('.community-wrapper');
                    res.data.forEach(function (val, index) {
                        body.append(val);
                    });
                    $('.no-data').hide();
                }else{
                    $("#warning_message").text('Community already exist.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    });
</script>