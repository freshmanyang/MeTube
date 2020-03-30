<div class="modal fade" id="update_username_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Username</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="needs-validation" id="update_username_form" novalidate>
                    <div class="form-group">
                        <label for="input_username">Username</label>
                        <input type="text" class="form-control form-control-sm" name="input_username"
                               placeholder="Username" maxlength="20" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="update_username_submit" name="update_username_submit"
                        form="update_username_form">Submit
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>