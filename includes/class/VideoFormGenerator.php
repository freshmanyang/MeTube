<?php

class VideoFormGenerator
{

    private $conn; // db connection descriptor

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createUploadForm()
    {
        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput();
        $descriptionInput = $this->createDescriptionInput();
        $privacyInput = $this->createPrivacyInput();
        $categoryInput = $this->createCategoryInput();
        $uploadButton = $this->createUploadButton();
        return "
            <form action='./media_upload_process.php' method='post' enctype='multipart/form-data' id='upload_video_form'>
                $fileInput
                $titleInput
                $descriptionInput
                $privacyInput
                $categoryInput
                $uploadButton
            </form>
        ";
    }

    private function createFileInput()
    {
        return "
            <div class='form-group'>
                <div class='custom-file'>
                    <input type='file' class='custom-file-input' name='file' required>
                    <label class='custom-file-label' for='customFile'>Choose file</label>
                </div>
            </div>
        ";
    }

    private function createTitleInput()
    {
        return "
            <div class='form-group'>
                <input type='text' class='form-control' placeholder='title' name='title' maxlength='30' required>
            </div>
        ";
    }

    private function createDescriptionInput()
    {
        return "
            <div class='form-group'>
                <textarea class='form-control' placeholder='description' name='description' maxlength='800' rows='5'></textarea>
            </div>
        ";
    }

    private function createPrivacyInput()
    {
        return "
            <div class='form-group'>
                <select class='selectpicker' data-width='100%' title='Choose a privacy Option...' name='privacy' required>
                    <option value='1'>public</option>
                    <option value='2'>friends</option>
                    <option value='0'>private</option>
                </select>
            </div>
            
        ";
    }

    private function createCategoryInput()
    {
        $query = $this->conn->prepare("SELECT * FROM category");
        $query->execute(); // run execute
        $html = "<div class='form-group'><select class='selectpicker' data-width='100%' title='Choose a category...' name='category'>";
        for ($i = 0; $row = $query->fetch(PDO::FETCH_ASSOC); $i++) {  // get the next line as associated array
            $html .= "<option value='$i'>" . $row["name"] . "</option>";
        }
        $html .= "</select></div>";
        return $html;
    }

    private function createUploadButton(){
        return "<button type='submit' class='btn btn-primary' name='submit'>Upload</button>";
    }
}
