<?php require_once("includes/header.php"); ?>
<?php require_once("includes/nav.php"); ?>
<main class="main-section-container" id="main">
    <div class="main-content-container" style="display: block;">
        <div class="form-wrapper">
            <div class="column">
                <?php
                require_once("includes/class/VideoFormGenerator.php");
                $formGenerator = new VideoFormGenerator($conn);
                echo $formGenerator->createUploadForm();
                ?>
            </div>
        </div>

        <!-- Modal -->
        <script class="javascript">
            $("#upload_video_form").on("submit",function () {
                $("#loading_modal").modal("show");
            });
        </script>
        <!-- set data-backdrop and data-keyboard attribute to prevent modal active close -->
        <div class="modal fade disable-modal-shifting" id="loading_modal" tabindex="-1" role="dialog"
             aria-labelledby="loading-modal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <img src="assets/imgs/loading.gif" alt="">
                        <p>Uploading....</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>