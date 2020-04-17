<?php
require_once('./includes/class/channelProcessor.php');
require_once('./includes/config.php');
$usernameLoggedIn = isset($_SESSION['userLoggedIn']) ? $_SESSION['userLoggedIn'] : "";
if (isset($_POST['pagefunction'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    if (!strcmp($usernameLoggedIn, $_POST['user'])) {
        echo json_encode($channel->createSignIn());
    } else {
        echo json_encode($channel->create());
    }
}
if (isset($_POST['subscribe'])) {
    if ($usernameLoggedIn == '') {
        echo '';
    } else {
        $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
        if ($_POST['button'] == 1) {
            echo $channel->addsubscribe();
        } else {
            echo $channel->unsubscribe();
        }
    }
}
if (isset($_POST['mysubscribe'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    echo json_encode($channel->createMySubscriptions());
}
if (isset($_POST['myFavoritelist'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    echo json_encode($channel->showFavoriteList());
}
if (isset($_POST['Delete'])) {
    if (isset($_POST['videoList'])) {
//    var_dump($_POST['videoList']);
        $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
        $channel->deleteVideo($_POST['videoList']);
        $reroute = 'channel.php?channel=' . $_GET['channel'];
        header("Location:$reroute");
    } else {
        $reroute = 'channel.php?channel=' . $_GET['channel'];
        header("Location:$reroute");
    }
}
if (isset($_POST['PlayList'])) {
    $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
    $message = $channel->createPlayList($_POST['PlayList']);
    $reroute = 'channel.php?channel=' . $_GET['channel'] . '&tab=myPlayList2';
    echo "<script>alert('$message'); location.href = '$reroute';</script>";
//    header("Location:$reroute");
}
if (isset($_POST['deletePlayList'])) {
    $reroute = 'channel.php?channel=' . $_GET['channel'] . '&tab=myPlayList2';
    if (isset($_POST['selectedPlayList'])) {
        $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
        $message = $channel->deletePlayList($_POST['selectedPlayList']);
        echo "<script>alert('$message'); location.href = '$reroute';</script>";
    } else {
        header("Location:$reroute");
    }
}
if (isset($_POST['addToFavoriteList'])) {
    $reroute = 'channel.php?channel=' . $_GET['channel'] . '&tab=myPlayList2';
    if (isset($_POST['selectedPlayList'])) {
        $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
        $message = $channel->addToFavoriteList($_POST['selectedPlayList']);
        echo "<script>alert('$message'); location.href = '$reroute';</script>";
    } else {
        header("Location:$reroute");
    }
}
if (isset($_POST['myplaylist'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    echo json_encode($channel->showPlayList());
}
if (isset($_POST['showVideoFromPlaylist'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    echo json_encode($channel->showVideoFromPlaylist($_POST['playlist']));
}
if (isset($_POST['showVideoFromPlaylistrecord'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    echo json_encode($channel->showVideoFromPlaylistRecord($_POST['playlist']));
}
if (isset($_POST['deletevideoinplaylist'])) {
    if (isset($_POST['videoinplayList'])) {
//    print_r($_POST['videoinplayList']);
        $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
        $channel->deleteVideoinplaylist($_POST['videoinplayList'], $_GET['playlist']);
        $reroute = 'Playlist.php?channel=' . $_GET['channel'] . '&playlist=' . $_GET['playlist'];
        header("Location:$reroute");
    } else {
        $reroute = 'Playlist.php?channel=' . $_GET['channel'] . '&playlist=' . $_GET['playlist'];
        header("Location:$reroute");
    }
}
// add video to playlist
if (isset($_GET['videoaddtoplaylist'])) {
    if (isset($_GET['vid'])) {
//    var_dump($_POST['videoList']);
        $channel = new channelProcessor($conn, '', $usernameLoggedIn);
        $message = $channel->addVideoTOPlaylist($_GET['videoaddtoplaylist'], $_GET['vid']);
        $reroute = 'watch.php?vid=' . $_GET['vid'];
        echo "<script>alert('$message'); location.href = '$reroute';</script>";
    }
}
if (isset($_POST['addSingleVideoToFavoriteList'])) {
    if (isset($_POST['videoinplayList'])) {
        $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
        $message = $channel->addSingleVideoToFavoriteList($_POST['videoinplayList'], $_GET['playlist']);
        $reroute = 'Playlist.php?channel=' . $_GET['channel'] . '&playlist=' . $_GET['playlist'];
        echo "<script>alert('$message'); location.href = '$reroute';</script>";
    } else {
        $reroute = 'Playlist.php?channel=' . $_GET['channel'] . '&playlist=' . $_GET['playlist'];
        header("Location:$reroute");
    }
}
if (isset($_POST['removeFromFavoriteList'])) {
    $reroute = 'channel.php?channel=' . $_GET['channel'] . '&tab=myFavoriteList2';
    if (isset($_POST['favoriteList'])) {
        $channel = new channelProcessor($conn, $_GET['channel'], $usernameLoggedIn);
        $message = $channel->removeVideoFromFavoriteList($_POST['favoriteList']);
        echo "<script>alert('$message'); location.href = '$reroute';</script>";
    } else {
        header("Location:$reroute");
    }
}
if (isset($_POST['sortingVideos'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    $category = $_POST['sorting'];
    if (!strcmp($_POST['sorting'], 'Duration')) {
        $category = 'video_duration';
    } elseif (!strcmp($_POST['sorting'], 'Most Viewed')) {
        $category = 'views';
    } elseif (!strcmp($_POST['sorting'], 'Most recently uploaded')) {
        $category = 'upload_date';
    } elseif (!strcmp($_POST['sorting'], 'Video_title')) {
        $category = 'title';
    } elseif (!strcmp($_POST['sorting'], 'File_size')) {
        $category = 'file_size';
    }
    echo json_encode($channel->sortingVideos($category));
}
if (isset($_POST['downloadvideo'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    echo json_encode($channel->showDownloadedVideos());
}
if (isset($_POST['addFriend'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    if ($usernameLoggedIn == '') {
        echo '';
    } else {
        echo $message = $channel->addFriend($_POST['user']);
    }
}
if (isset($_POST['removeFriend'])) {
    $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
    if ($usernameLoggedIn == '') {
        echo '';
    } else {
        echo $message = $channel->removeFriend($_POST['user']);
    }
}
if (isset($_POST['setPrivacy'])) {
//    $reroute = 'channel.php?channel='.$_POST['user'];
    if (isset($_POST['videolist'])) {
        if (!strcmp($_POST['privacy'], 'Public')) {
            $privacy = 1;
        } elseif (!strcmp($_POST['privacy'], 'Private')) {
            $privacy = 0;
        } elseif (!strcmp($_POST['privacy'], 'Friends')) {
            $privacy = 2;
        }
        $channel = new channelProcessor($conn, $_POST['user'], $usernameLoggedIn);
        echo $channel->setVideosPrivacy($_POST['videolist'], $privacy);
    } else {
        echo 'You didnot choose any videos';
    }
}
?>
