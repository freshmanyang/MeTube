<?php

// Handle AJAX request (start)
if( isset($_POST['ajax']) && isset($_POST['checked']) ){
    $checked_arr = $_POST['checked'];
    echo $checked_arr;
//    echo json_encode($checked_arr);
    exit;
}
// Handle AJAX request (end)
?>

<!doctype html>
<html>

<body >

<form method='post' action>
    <input type='checkbox' value='JavaScript' > JavaScript <br/>
    <input type='checkbox' value='PHP' > PHP <br/>
    <input type='checkbox' value='jQuery' > jQuery <br/>
    <input type='checkbox' value='AJAX' > AJAX <br/>
    <input type='button' value='click' id='but'>
    <div id='response'></div>
</form>

<!-- Script -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#but').click(function(){

            var checkarr = [];
            $("input[type=checkbox]:checked").each(function(index,element){
                checkarr.push($(element).val());
            });

            if(checkarr.length > 0){
                $.ajax({
                    type: 'post',
                    data: {ajax: 1,checked: "abc"},
                    dataType: 'text',
                    success: function(response){
                        // $('#response').text('response : ' + JSON.stringify(response) );
                        console.log(response);
                    }
                });
            }

        });
    });
</script>
</body>
</html>