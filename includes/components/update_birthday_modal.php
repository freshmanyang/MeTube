<div class="modal fade" id="update_birthday_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Birthday</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="needs-validation" id="update_birthday_form" autocomplete='off' novalidate>
                    <div class="form-group">
                        <div class="input-group date" id="birthday_time_picker" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" name="update_birthday"
                                   data-toggle="datetimepicker" data-target="#birthday_time_picker" required>
                            <div class="input-group-append" data-target="#birthday_time_picker"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text" id="custom_input_group_text">
                                    <i class="iconfont icon-calender"></i>
                                </div>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="update_birthday_submit" name="update_birthday_submit"
                        form="update_birthday_form">Submit
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#birthday_time_picker').datetimepicker({
            icons:{
                previous: 'iconfont icon-arrow-left',
                next: 'iconfont icon-arrowright',
            },
            format: 'YYYY-MM-DD',
            defaultDate: $("#profile_birthday").text()
        })
    });
</script>