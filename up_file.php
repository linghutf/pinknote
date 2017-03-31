<html>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <scripy src="http://lib.sinaapp.com/js/jquery/3.1.0/jquery-3.1.0.min.js"></scripy>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js"></script>
        <title>上传文件</title>
    </head>
<body>
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container-fluid">
    <div>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    观影<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add_mov.php">添加</a></li> 
                    <li><a href="list_mov_todo.php">提醒</a></li> 
                    <li class="divider"></li>
                    <li><a href="search_mov.php">修改</a></li> 
                    <li><a href="list_mov_his.php?page_id=0">历史</a></li> 
                </ul>
            </li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    娱乐<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add_movie.php">添加</a></li> 
                    <li><a href="list_todo.php">任务</a></li>
                    <li class="divider"></li>
                    <li><a href="search.php">修改</a></li> 
                    <li><a href="list_history.php?page_id=0">历史</a></li>
                    <li class="divider"></li>
                    <li><a href="libav.php">主题</a></li>
                    <li><a href="libact.php">艺人</a></li>
                </ul>
            </li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    文件共享<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="up_file.php">上传</a></li> 
                    <li><a href="download_file.php">下载</a></li>
                    <li class="divider"></li>
                    <li><a href="list_file.php">浏览</a></li> 
                    
                </ul>
            </li>
        </ul>
    </div>
    </div>
</nav>
<div class=“container”>
    <form class="form-inline" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="filename" class="col-sm-2 control-label">文件名</label>
    <div class="col-sm-10">
      <input type="file" class="form-control" id="filename" name="myFile" enctype="multipart/form-data"/>
    </div>
  </div>
  
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" class="btn btn-default" name="upload" value="上传" />
    </div>
  </div>
</form>
</div>

<?php
if($_FILES['myFile']['error']>0)
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
    $filename = $_FILES['myFile']['name'];
    $type = $_FILES['myFile']['type'];
    $tmp_name=$_FILES['myFile']['tmp_name'];
    $size=$_FILES['myFile']['size'];
    echo move_uploaded_file($tmp_name, "uploads/".$filename);
}
?>
</body>
</html>