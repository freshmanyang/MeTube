<?php


class VideoPlayer
{
    private $videoObj;

    public function __construct($videoObj)
    {
        $this->videoObj = $videoObj;
    }

    public function create($autoPlay)
    {
        $autoPlay = $autoPlay ? "autoPlay" : "";
        $filePath = $this->videoObj->getFilePath();
        $videoId = $this->videoObj->getVideoId();
        return "<video class='video-player' id='video_player' controls controlsList='nodownload' video-id='$videoId' $autoPlay>
                    <source src='$filePath' type='video/mp4'>
                </video>";
    }
}