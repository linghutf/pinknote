<html>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <scripy src="http://lib.sinaapp.com/js/jquery/3.1.0/jquery-3.1.0.min.js"></scripy>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js"></script>
        <title>添加影片</title>
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
<div class="container">
    <div class="panel panel-success">
	<div class="panel-heading">
		<h3 class="panel-title">输入信息</h3>
	</div>
	<div class="panel-body">
		<form class="form-horizontal" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="topic" class="col-sm-2 control-label">片名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="topic" name="topic" required="true" placeholder="请输片名" />
    </div>
  </div>
  
  <div class="form-group">
    <label for="actor" class="col-sm-2 control-label">艺人</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="actor" name="actor" placeholder="请输入姓" />
    </div>
  </div>
  
  <div class="form-group">
    <label for="pressDate" class="col-sm-2 control-label">上映日</label>
    <div class="col-sm-10">
      <input type="date" class="form-control" id="pressDate" name="pressDate" required="true" />
    </div>
  </div>
  
  <!--div class="form-group">
    <label for="rank" class="col-sm-2 control-label">评分</label>
    <div class="col-sm-10">
      <input type="number" class="form-control" id="rank" name="rank" min="0" max="5" value="1" />
    </div>
  </div-->
  
  <!--div class="form-group">
    <label for="comment" class="col-sm-2 control-label">观后感</label>
    <div class="col-sm-10 row-sm-20">
    <textarea class="form-control" id="comment" rows="comment"></textarea>
    </div>
  </div-->
 
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" class="btn btn-default" name="add_movie" value="添加" />
    </div>
  </div>
</form>
	
</div>
<div class="panel panel-info">
	<div class="panel-heading">最受欢迎的艺人</div>
	<div class="panel-body">
<?php
require_once('connect.php');
// 显示主演，按频次逆序排列
$sql = 'SELECT actor from movie where actor!="" group by actor order by count(actor) desc;';
$res=$db->query($sql);
while($row=$res->fetchArray(SQLITE3_ASSOC))
{
    echo '<div class="div-inline">'.$row['actor'].'&nbsp;&nbsp;&nbsp;</div>';
}
// 提交到数据库
if(!empty( $_POST['add_movie']))
{
    
    
    $topic = strtolower(trim($_POST['topic']));
    $actor = trim($_POST['actor']);
    $pressDate = strtotime($_POST["pressDate"]);
    
    $sql ='INSERT INTO movie(topic,actor,pressDate)
    values(:topic,:actor,:pressDate);';
    
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':topic',$topic,SQLITE3_TEXT);
    $stmt->bindValue(':actor',$actor,SQLITE3_TEXT);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    
    $ret = $stmt->execute();//返回true/false
    
    if(!$ret){
        echo $db->lastErrorMsg()."<br/>";
    }
    $db->close();
}
?>
</dib>
</div>
</div>
</body>
<br /><a href="index.php">返回主页面</a><br />
</html>