<html>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <title>历史记录</title>
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
    <div class="row">
        <div class="center-block">
            
        
    <table class="table table-striped table-condensed">
    <thead>
       <tr>
           <th>中文名</th>
           <th>英文名</th>
           <th>上映日</th>
           <th>完成日</th>
       </tr>
       </thead>
       <tbody>
<?php
// TODO 改成分页模式
require_once('connect.php');

if($row=$db->query('SELECT COUNT(chn_name) as tot FROM libmov where finishDate!=0;')->fetchArray(SQLITE3_ASSOC))
{
    $tot = $row['tot'];
}
$item_num = 10;
$page_num = ceil($tot/$item_num);
$page_id=$_GET['page_id'];

$page_start = $page_id*$item_num;

$sql= 'SELECT chn_name,eng_name,pressDate,finishDate FROM libmov where finishDate!=0 ORDER BY finishDate desc limit '.$page_id.','.$item_num.';';

$ret = $db->query($sql);
$num = 0;
while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    $pressDate = $row["pressDate"];
    $pressDate = date('Y-m-d',$pressDate);
    $finishDate = $row['finishDate'];
    $finishDate = date('Y-m-d',$finishDate);
    $chn_name = $row['chn_name'];
    $eng_name = $row['eng_name'];
    $num++;
    echo '<tr><td>'.$chn_name.'</td><td>'.
    $eng_name.'</td><td>'.$pressDate.'</td><td>'.$finishDate.'</td></tr>';
}
$db->close();
echo '<tr><td>总计:</td><td colspan="3">'.$num.'</td></tr>';


// 页码显示
$page_prev=$page_id-1;
if($page_prev<0) $page_prev=0;
echo <<< EOF
<ul class="pagination">
    <li><a href="{$_SERVER['PHP_SELF']}?page_id=$page_prev">&laquo;</a></li>
EOF;

if($page_num>1)
{
    for($i=0;$i<$page_num;++$i)
    {
        $j=$i+1;
        if($i==$page_id){
            echo <<< HTML
    <li class="active"><a href="{$_SERVER['PHP_SELF']}?page_id=$i">$j</a></li>
HTML;
        }else{
            echo <<< HTML
    <li><a href="{$_SERVER['PHP_SELF']}?page_id=$i">$j</a></li>
HTML;
        }
    }
}

$page_next=$page_id+1;
if($page_next>=$page_num) $page_next=$page_num-1;
echo <<< EOF
 <li><a href="{$_SERVER['PHP_SELF']}?page_id=$page_next">&raquo;</a></li>
</ul>
EOF;

?>
</tbody></table>
</div>
</div>
</div>
</body>
</html>