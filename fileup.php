<?php include_once('header.php');?>
   <form class="form-inline" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="filename" class="col-sm-2 control-label">文件名</label>
    <div class="col-sm-10">
      <input type="file" class="form-control" id="filename" name="upload_file" enctype="multipart/form-data" />
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" class="btn form-control" name="upload" value="上传" />
    </div>
  </div>
</form>

<?php
if(isset($_POST['upload']))
{
    if(isset($_FILES['upload_file']))
    {
        if($_FILES['upload_file']['error']>0)
        {
            switch ($error){
            case 1:
                echo '<div class="row"><p>超过了上传文件的最大值，请上传2M以下文件<p></div>';
                break;
            case 2:
                echo '<div class="row"><p>上传文件过多，请一次上传20个及以下文件！<p></div>';
                break;
            case 3:
                echo '<div class="row"><p>文件并未完全上传，请再次尝试！<p></div>';
                break;
            case 4:
                echo '<div class="row"><p>未选择上传文件！<p></div>';
                break;
            case 5:
                echo '<div class="row"><p>上传文件大小为0!<p></div>';
                break;
            }
        }else{
            $filename = $_FILES['upload_file']['name'];
            $type = $_FILES['upload_file']['type'];
            $tmp_name=$_FILES['upload_file']['tmp_name'];
            $size=$_FILES['upload_file']['size'];
            if(!move_uploaded_file($tmp_name, "uploads/".$filename))
            {
                echo 'error';
            }
        }
    }
}
?>

<?php include_once('footer.php');?>
