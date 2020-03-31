<?php
require_once('./includes/class/channelProcessor.php');
require_once('./includes/config.php');
$usernameLoggedIn = isset($_SESSION['userLoggedIn']) ? $_SESSION['userLoggedIn'] : "";

if(isset($_POST['pagefunction'])){
    $channel = new channelProcessor($conn,$_POST['user'],$usernameLoggedIn);
    if(!strcmp($usernameLoggedIn,$_POST['user'])) {
        echo json_encode($channel->createSignIn());
    }
    else{
        echo json_encode($channel->create());
    }
}
if(isset($_POST['subscribe'])){
    $channel = new channelProcessor($conn,$_POST['user'],$usernameLoggedIn);
    if($_POST['button']==1){
      echo $channel->addsubscribe();

    }
    else{
        echo $channel->unsubscribe();
    }
}

if(isset($_POST['mysubscribe'])){
    $channel = new channelProcessor($conn,$_POST['user'],$usernameLoggedIn);
    echo json_encode($channel->createMySubscriptions());

}

if (isset($_POST['Delete'])) {
    if(isset($_POST['videoList'])) {
//    var_dump($_POST['videoList']);
        $channel = new channelProcessor($conn,$_GET['channel'],$usernameLoggedIn);
        $channel->deleteVideo($_POST['videoList']);
        $reroute = 'channel.php?channel='.$_GET['channel'];
    header("Location:$reroute");
    }
    else{
        $reroute = 'channel.php?channel='.$_GET['channel'];
        header("Location:$reroute");
    }
}


if(isset($_POST['PlayList'])){
    $channel = new channelProcessor($conn,$_GET['channel'],$usernameLoggedIn);
    $message = $channel->createPlayList($_POST['PlayList']);
    $reroute = 'channel.php?channel='.$_GET['channel'].'&tab=myPlayList';
   echo "<script>alert('$message'); location.href = '$reroute';</script>";

//    header("Location:$reroute");
}
if(isset($_POST['deletePlayList'])) {
    $channel = new channelProcessor($conn,$_GET['channel'],$usernameLoggedIn);
    $message = $channel->deletePlayList($_POST['deletePlayList']);
    $reroute = 'channel.php?channel='.$_GET['channel'].'&tab=myPlayList';
    echo "<script>alert('$message'); location.href = '$reroute';</script>";
}

if(isset($_POST['myplaylist'])){
    $channel = new channelProcessor($conn,$_POST['user'],$usernameLoggedIn);
    echo json_encode($channel->showPlayList());

}
if(isset($_POST['showVideoFromPlaylist'])){
    $channel = new channelProcessor($conn,$_POST['user'],$usernameLoggedIn);
    echo json_encode($channel->showVideoFromPlaylist($_POST['playlist']));

}
if (isset($_POST['deletevideoinplaylist'])) {
    if(isset($_POST['videoinplayList'])) {
    print_r($_POST['videoinplayList']);
        $channel = new channelProcessor($conn,$_GET['channel'],$usernameLoggedIn);
        $channel->deleteVideoinplaylist($_POST['videoinplayList'],$_GET['channel']);
//        $reroute = 'channel.php?channel='.$_GET['channel'];
//        header("Location:$reroute");
    }
    else{
//        $reroute = 'channel.php?channel='.$_GET['channel'];
//        header("Location:$reroute");
    }
}

?>