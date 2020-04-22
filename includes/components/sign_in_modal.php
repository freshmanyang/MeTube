<div class="modal fade" id="sign_in_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">SIGN IN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="submit.php" method="post" enctype="multipart/form-data" class="needs-validation"
                      id="sign_in_form" novalidate>
                    <div class="form-group mb-2">
                        <label for="input_email">Email address</label>
                        <input type="email" class="form-control form-control-sm" name="input_email"
                               placeholder="Email: hello@example.com"
                               pattern=(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])
                               required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group mb-2">
                        <label for="input_password">Password</label>
                        <input type="password" class="form-control form-control-sm" name="input_password"
                               placeholder="Password" minlength="8" maxlength="20"
                               pattern="^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,20}$"
                               required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="sign_in_btn" name="sign_in_submit"
                        form="sign_in_form">SIGN IN
                </button>
                <button type="button" class="btn btn-secondary" id="to_sign_up_modal" data-toggle="modal" data-target="#sign_up_modal"
                        data-dismiss="modal">SIGN UP
                </button>
            </div>
        </div>
    </div>
</div>