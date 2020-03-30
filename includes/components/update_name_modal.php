<div class="modal fade" id="update_name_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Name</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="needs-validation" id="update_name_form" novalidate>
                    <div class="form-group">
                        <label for="input_first_name">First name</label>
                        <input type="text" class="form-control form-control-sm" name="input_first_name"
                               placeholder="First name" pattern="^[a-zA-Z]+$" maxlength="20" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="input_last_name">Last name</label>
                        <input type="text" class="form-control form-control-sm" name="input_last_name"
                               placeholder="Last name" pattern="^[a-zA-Z]+$" maxlength="20" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="update_name_submit" name="update_name_submit"
                        form="update_name_form">Submit
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>