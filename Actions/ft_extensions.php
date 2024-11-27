<?php


function getAndVerify($extension, $application)
{
    $extensions = array(
        ".jpg" => "image/jpeg",
        ".jpeg" => "image/jpeg",
        ".png" => "image/png",
        ".gif" => "image/gif",
        ".bmp" => "image/bmp",
        ".tiff" => "image/tiff"
    );

    return isset($extensions[$extension]) && $extensions[$extension] === $application;
}

?>
