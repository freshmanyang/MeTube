<div class="modal fade" id="update_avatar_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Select Profile Photo</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="needs-validation" id="update_avatar_form" novalidate>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name='update_avatar'
                               accept="image/png,image/jpeg" required>
                        <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="update_avatar_submit" name="update_avatar_submit"
                        form="update_avatar_form">Submit
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>