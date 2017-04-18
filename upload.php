<?php
/**
 * Created by PhpStorm.
 * User: xdc
 * Date: 2017/4/18
 * Time: 14:21
 */
if(isset($_FILES['upload_file']))
{
    $filename = $_FILES['upload_file']['name'];
    $tmpname = $_FILES['upload_file']['tmp_name'];

    $destination = 'files'.DIRECTORY_SEPARATOR.$filename;
    $res=['status'=>'ok'];
    if(!move_uploaded_file($tmpname,$destination)){
        $res['status']='failed';
    }
    return json_encode($res);

}