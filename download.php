<?php
function downfile($fileurl)
{
    ob_start();
    $filename=basename($fileurl);

    //header( "Content-type:  application/octet-stream ");
   // header( "Accept-Ranges:  bytes ");
    header( "content-disposition:attachment;filename= {$fileurl}");
    header( "content-length: " .filesize($fileurl));
    readfile($fileurl);

}

if(isset($_GET['url']))
{
    downfile($_GET['url']);
}