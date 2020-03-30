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
        return "<video class='video-player' controls $autoPlay>
                    <source src='$filePath' type='video/mp4'>
                </video>";
    }
}