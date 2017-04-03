<html>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <title>浏览未完成</title>
        <scripy src="http://lib.sinaapp.com/js/jquery/3.1.0/jquery-3.1.0.min.js"></scripy>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js"></script>
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
    <div class="leaderboard">
    <form action="<?php print $_SERVER['PHP_SELF']?>" method="post">
        
    <table class="table table-striped table-condensed">
        <thead>
       <tr>
           <th>完成</th>
           <th>中文名</th>
           <th>英文名</th>
           <th>上映日</th>
           
       </tr>
       </thead>
       <tbody>
<?php
require_once('connect.php');

$sql='SELECT chn_name,eng_name,pressDate FROM libmov where finishDate==0 ORDER BY pressDate asc,rank desc';

$ret = $db->query($sql);
while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    $pressDate = $row["pressDate"];
    $pressDate = date('Y-m-d',$pressDate);
    echo '<tr><td><input type="checkbox" name="finish_flag[]" value="'.$row['chn_name'].'" /></td><td>'.$row['chn_name'].'</td><td>'.
    $row['eng_name'].'</td><td>'.$pressDate.'</td>
    </tr>';
}

// 标记完成的电影
if(!empty( $_POST['finish_flag']))
{
    $sql ='UPDATE libmov set finishDate = '.strtotime(date('Y-m-d')).' where chn_name=\'';
    // 设置完成
    $finish_arr = $_POST['finish_flag'];
    for($i=0;$i<count($finish_arr);$i++)
    {
        $ret = $db->exec($sql.$finish_arr[$i].'\';');
        if(!$ret)
        {
            echo $db->lastErrorMsg()."<br/>";
        }
       
    }
    //刷新当前页面
    echo "<script type='text/javascript'>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
}

$db->close();

?>
</tbody>
</table>

<input type="submit" class="btn btn-success" value="完成"/>
    </form>
    </div>
</div>
</body>
</html>