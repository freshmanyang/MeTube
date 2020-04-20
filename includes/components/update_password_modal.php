<div class="modal fade" id="update_password_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Password</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="needs-validation" id="update_password_form" novalidate>
                    <div class="form-group">
                        <label for="old_password">Old password</label>
                        <input type="password" class="form-control" name="old_password"
                               placeholder="Password" minlength="8" maxlength="20"
                               required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="input_password">New password</label>
                        <input type="password" class="form-control" name="input_password"
                               placeholder="Password" minlength="8" maxlength="20"
                               pattern="^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,20}$"
                               required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm new password</label>
                        <input type="password" class="form-control" name="confirm_password"
                               placeholder="Password" minlength="8" maxlength="20"
                               required>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="update_password_submit" name="update_password_submit"
                        form="update_password_form">Submit
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>