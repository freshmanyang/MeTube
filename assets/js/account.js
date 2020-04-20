// warning message for invalid inputs
let warningMsg = {
    "input_first_name": {
        "data-value-missing": "First name is required.",
        "data-pattern-mismatch": "Name should not have any number or special character."
    },
    "input_last_name": {
        "data-value-missing": "Last name is required.",
        "data-pattern-mismatch": "Name should not have any number or special character."
    },
    "input_username": {
        "data-value-missing": "Username is required.",
        "data-value-used": "Username already used.",
    },
    "input_email": {
        "data-value-missing": "Email address is required.",
        "data-value-used": "Email address already used.",
        "data-pattern-mismatch": "Invalid email address.",
        "data-not-found": "Email does not exist."
    },
    "confirm_email": {
        "data-value-missing": "Confirmed email address is required.",
        "data-pattern-mismatch": "Invalid email address.",
        "data-value-mismatch": "Email addresses not match."
    },
    "input_password": {
        "data-value-missing": "Password is required.",
        "data-pattern-mismatch": "Your password must be 8-20 characters long, " +
            "contain letters and numbers, and must not " +
            "contain spaces, special characters, or emoji.",
        "data-value-error": "Password error."
    },
    "confirm_password": {
        "data-value-missing": "Confirmed password is required.",
        "data-value-mismatch": "Passwords not match."
    },
    "update_avatar": {
        "data-value-missing": "Upload file is required.",
        "data-type-mismatch": "Only accept jpeg and png images.",
        "data-size-overflow": "The maximum image size is 2M"
    },
    "update_birthday": {
        "data-value-missing": "Birthday is required."
    },
    "old_password": {
        "data-value-missing": "Password is required.",
        "data-value-error": "Failed to check password."
    }
};

// show error message of a input element by name
function showError(el) {
    let msg = "";
    let name = $(el).attr("name");
    let reason = el.validity;
    if (!reason.valid) {
        if (reason.valueMissing) {
            msg = warningMsg[name]["data-value-missing"];
        } else if (reason.patternMismatch) {
            msg = warningMsg[name]["data-pattern-mismatch"];
        } else if (reason.typeMismatch) {
            msg = warningMsg[name]["data-type-mismatch"];
        } else if (reason.customError) {
            msg = warningMsg[name][el.validationMessage];
        }
        $(el).next().next().text(msg); // insert error message
    }
}

// bind showError function for input boxes
$("#sign_in_form input, " +
    "#sign_up_form input, " +
    "#update_name_form input, " +
    "#update_username_form input, " +
    "#update_birthday_form input, " +
    "#update_password_form input[name='input_password'], " +
    "#update_password_form input[name='confirm_password']")
    .on("input", function () {
        let input = $(this)[0];
        setTimeout(function () {
            // give a 100ms timeout, let custom validation function run first
            showError(input);
        }, 100);
    });

// test input email in sign in form
function emailExist() {
    let result = "";
    let input_email = $("#sign_in_form input[name='input_email']");
    let email = input_email.val();
    if (email) {
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {untested_email: email},
            dataType: 'json',
            async: false, // synchronized ajax call to get return value
            success: function (res) {
                result = res.exist;
            }
        });
    }
    return result;
}

// test password in sign in form
function passwordCorrect() {
    let result = "";
    let email = $("#sign_in_form input[name='input_email']").val();
    let password = $("#sign_in_form input[name='input_password']").val();
    if (email) {
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {email: email, untested_password: password},
            dataType: 'json',
            async: false, // synchronized ajax call to get return value
            success: function (res) {
                result = res.correct;
            }
        });
    }
    return result;
}

// In sign in form, clear custom error when input, only show custom error when submit form
$("#sign_in_form input").on("input", function clearCustomError() {
    $(this)[0].setCustomValidity('');
});

// check all inputs before submit the sign in form
$("#sign_in_form").on("submit", function (e) {
    if (!emailExist()) {
        $("#sign_in_form input[name='input_email']")[0].setCustomValidity('data-not-found');
        // showError($("#sign_in_form input[name='input_email']")[0]);
    } else if (!passwordCorrect()) {
        $("#sign_in_form input[name='input_password']")[0].setCustomValidity('data-value-error');
        // showError($("#sign_in_form input[name='input_password']")[0]);
    } else {
        $("#sign_in_form input[name='input_email']")[0].setCustomValidity('');
        $("#sign_in_form input[name='input_password']")[0].setCustomValidity('');
    }
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.preventDefault();
        e.stopPropagation();
    }
    showError($("#sign_in_form input[name='input_email']")[0]);
    showError($("#sign_in_form input[name='input_password']")[0]);
    $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
});

// custom validation function to check username exist, use validity.customError to report error
$("#sign_up_form input[name='input_username'], #update_username_form input[name='input_username']")
    .on("input", function checkUsernameExist() {
        let input_username = $(this);
        let username = input_username.val();
        if (username) {
            $.ajax({
                type: 'post',
                url: './submit.php',
                data: {untested_username: username},
                dataType: 'json',
                success: function (res) {
                    if (res.exist) {
                        // username exist, set validation message, validity.customError = 1
                        input_username[0].setCustomValidity('data-value-used');
                    } else {
                        // username not exist, clear validation message, validity.customError = 0
                        input_username[0].setCustomValidity('');
                    }
                }
            });
        }
    });

// custom validation function to check email exist, use validity.customError to report error
$("#sign_up_form input[name='input_email']").on("input", function checkEmailExist() {
    let input_email = $(this);
    let email = input_email.val();
    if (email) {
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {untested_email: email},
            dataType: 'json',
            success: function (res) {
                if (res.exist) {
                    input_email[0].setCustomValidity('data-value-used');
                } else {
                    input_email[0].setCustomValidity('');
                }
            }
        });
    }
});


// custom validation function to check password mismatch, use validity.customError to report error
$("#sign_up_modal input[type=email]").on("input", function checkEmailMismatch() {
    let input_email = $("input[name='input_email']");
    let confirm_email = $("input[name='confirm_email']");
    if (input_email.val() !== confirm_email.val()) {
        confirm_email[0].setCustomValidity('data-value-mismatch');
    } else {
        confirm_email[0].setCustomValidity('');
    }
});

// custom validation function to check password mismatch, use validity.customError to report error
$("#sign_up_modal input[type=password]").on('input', function checkPasswordMismatch() {
    let input_password = $("#sign_up_modal input[name='input_password']");
    let confirm_password = $("#sign_up_modal input[name='confirm_password']");
    if (input_password.val() !== confirm_password.val()) {
        confirm_password[0].setCustomValidity('data-value-mismatch');
    } else {
        confirm_password[0].setCustomValidity('');
    }
});

// check all inputs before submit the sign up form
$("#sign_up_form").on("submit", function (e) {
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.preventDefault();
        e.stopPropagation();
    }
    let els = $("#sign_up_form input");
    for (let i = 0; i < els.length; i++) { // show errors when hit submit button
        showError(els[i]);
    }
    $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
});

function checkFileSizeOverflow(input_el, max_size) {
    if (input_el[0].files.length !== 0) {
        let fileSize = input_el[0].files[0].size;
        if (fileSize > max_size) {
            input_el[0].setCustomValidity('data-size-overflow');
        } else {
            input_el[0].setCustomValidity('');
        }
    }
}

// check input before submit the update avatar form
$("#update_avatar_form").on("submit", function updateAvatar(e) {
    e.preventDefault();
    let inputAvatarEl = $("#update_avatar_form input[name='update_avatar']");
    checkFileSizeOverflow(inputAvatarEl, 2000000);
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.stopPropagation();
        showError(inputAvatarEl[0]);
        $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
    } else {
        // validation success
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $("#update_avatar_modal").modal('hide');
                if (res.status) { // success
                    $("#profile_avatar, #header_avatar").attr("src", res.data);
                } else { // error
                    $("#warning_message").text('Something went wrong. File was not uploaded.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    }
});

// check input before submit the update name form
$("#update_name_form").on("submit", function updateFullName(e) {
    e.preventDefault();
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.stopPropagation();
        showError($("#update_name_form input[name='input_first_name']")[0]);
        showError($("#update_name_form input[name='input_last_name']")[0]);
        $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
    } else {
        // validation success
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $("#update_name_modal").modal('hide');
                if (res.status) { // success
                    $("#profile_full_name").text(res.data);
                } else { // error
                    $("#warning_message").text('Something went wrong. Name was not uploaded.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    }
});

// check input before submit the update username form
$("#update_username_form").on("submit", function updateUsername(e) {
    e.preventDefault();
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.stopPropagation();
        showError($("#update_username_form input[name='input_username']")[0]);
        $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
    } else {
        // validation success
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $("#update_username_modal").modal('hide');
                if (res.status) { // success
                    $("#header_username").text(res.data);
                    $("#profile_username").text(res.data);
                } else { // error
                    $("#warning_message").text('Something went wrong. Username was not uploaded.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    }
});

// check input before submit the update birthday form
$("#update_birthday_form").on("submit", function updateUsername(e) {
    e.preventDefault();
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.stopPropagation();
        showError($("#update_birthday_form input[name='update_birthday']")[0]);
        $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
    } else {
        // validation success
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $("#update_birthday_modal").modal('hide');
                if (res.status) { // success
                    $("#profile_birthday").text(res.data);
                } else { // error
                    $("#warning_message").text('Something went wrong. Birthday was not uploaded.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    }
});

// update gender form does not need validation
$("#update_gender_form").on("submit", function updateGender(e) {
    e.preventDefault();
    $.ajax({
        type: 'post',
        url: './submit.php',
        data: new FormData(this),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function (res) {
            $("#update_gender_modal").modal('hide');
            if (res.status) { // success
                $("#profile_gender").text(res.data);
            } else { // error
                $("#warning_message").text('Something went wrong. Gender was not uploaded.');
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

// checkOldPassword
function checkIdentity() {
    let result = "";
    let password = $("#update_password_modal input[name='old_password']").val();
    if (password) {
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: {check_identity: password},
            dataType: 'json',
            async: false, // synchronized ajax call to get return value
            success: function (res) {
                result = res.status;
            }
        });
    }
    return result;
}

// check new password mismatch
$("#update_password_modal input[type=password]").on('input', function checkNewPasswordMismatch() {
    let input_password = $("#update_password_modal input[name='input_password']");
    let confirm_password = $("#update_password_modal input[name='confirm_password']");
    if (input_password.val() !== confirm_password.val()) {
        confirm_password[0].setCustomValidity('data-value-mismatch');
    } else {
        confirm_password[0].setCustomValidity('');
    }
});


// check input before submit the update password form
$("#update_password_form").on("submit", function updatePassword(e) {
    e.preventDefault();
    if (!checkIdentity()) {
        $("#update_password_form input[name='old_password']")[0].setCustomValidity('data-value-error');
    } else {
        $("#update_password_form input[name='old_password']")[0].setCustomValidity('');
    }
    if ($(this)[0].checkValidity() === false) {
        // prevent form submit if validation is failed.
        e.stopPropagation();
        showError($("#update_password_form input[name='old_password']")[0]);
        showError($("#update_password_form input[name='input_password']")[0]);
        showError($("#update_password_form input[name='confirm_password']")[0]);
        $(this).addClass("was-validated"); // error only shows when "was-validated" class is set
    } else {
        $.ajax({
            type: 'post',
            url: './submit.php',
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $("#update_password_modal").modal('hide');
                if (!res.status) {
                    $("#warning_message").text('Something went wrong. Password was not uploaded.');
                    $("#alert_modal").modal('show');
                }
            },
            error: function (xhr, status, error) { // ajax error
                let errorMessage = xhr.status + ': ' + xhr.statusText;
                $("#warning_message").text(errorMessage);
                $("#alert_modal").modal('show');
            }
        });
    }
});