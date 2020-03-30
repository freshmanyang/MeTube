<div class="modal fade" id="update_gender_modal" tabindex="-1" role="dialog"
     aria-labelledby="modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_title">Gender</h5>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" class="needs-validation" id="update_gender_form" novalidate>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="select_gender" value="Male" checked>
                            <label class="form-check-label" for="exampleRadios1">Male</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="select_gender" value="Female">
                            <label class="form-check-label" for="exampleRadios2">Female</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="select_gender" value="Rather not say">
                            <label class="form-check-label" for="exampleRadios2">Rather not say</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="update_gender_submit" name="update_gender_submit"
                        form="update_gender_form">Submit
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>