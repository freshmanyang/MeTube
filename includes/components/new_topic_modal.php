<div class="modal fade" id="new_topic_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">New Topic</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" id="new_topic_form">
                    <div class="form-group">
                        <label for="input_title">Title</label>
                        <input type="text" class="form-control form-control-sm" name="title" maxlength="150" required>
                    </div>
                    <div class="form-group">
                        <label for="input_content">Content</label>
                        <textarea id="topic_content" name="text" rows="5" maxlength="1000" style="width: 100%;" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="submit_btn" form="new_topic_form" >Submit</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#new_community_modal").on("hidden.bs.modal", function () {
        $(this).find('input').val("");
        $(this).find('textarea').val("");
    });
</script>
