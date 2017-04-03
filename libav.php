<?php include_once('header.php');?>
       <form action="<?php print $_SERVER['PHP_SELF']?>" method="post">
            <table class="table table-striped table-condensed">
            <thead><tr>
                <th>系列</th>
                <th>上次日期</th>
                <th>更新</th>
            </tr>
            </thead>
            <tbody>

<?php
require_once("connect.php");
require_once("utils.php");

// 主题库

$sql = "SELECT topic,curDate FROM libs order by curDate asc,degree desc;";
$res=$db->query($sql);
while($row=$res->fetchArray(SQLITE3_ASSOC))
{
    $lastDate=$row['curDate'];

    echo '<tr><td>'.$row['topic'].'</td><td>'.date('Y-m-d',$lastDate).'</td>';
    // 如果上次观影日期不在当月，则显示更新按钮
    if(!is_between_cur_mon_from_unix($lastDate))
    {
        echo '<td><input type="checkbox" name="update_watchs[]" value="'.$row['topic'].'" /></td></tr>';
    }
}

// 更新观看
if(isset($_POST['mark']))
{

    $cur = strtotime(date('Y-m-d'));
    foreach($_POST['update_watchs'] as $topic)
    {
        $res=$db->exec('UPDATE libs SET curDate='.$cur.' where topic="'.$topic.'";');
        if(!$res)//更新判断方式
        {
            echo '<p>'.$db->lastErrorMsg().'</p>';
            exit;
        }
    }
    //刷新当前页面
    echo "<script type='text/javascript'>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
}

//添加系列
if(isset($_POST['add_series']))
{
    // curDate默认0,没有看过
    $sql = "INSERT INTO libs(topic,curDate,degree) values('".$_POST['topic_add']."',0,".$_POST['degree_add'].");";
    $res=$db->exec($sql);
    if(!$res)
    {
        echo '<p>'.$db->lastErrorMsg().'</p>';
        exit;
    }
    //刷新当前页面
    echo "<script type='text/javascript'>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
}
?>
            <tr><td colspan="3"><input type="submit" name="mark" value="已看"/></td></tr>
            </tbody>
            </table>
        </form>
        <!--添加系列-->
        <form class="form-inline" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="topic_add">系列</label>
    <input type="text" class="form-control" id="topic_add" name="topic_add" placeholder="请输入系列" />
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label" for="degree_add">热度</label>
    <input type="number" class="form-control" id="degree_add" name="degree_add" min="1" max="5" value="1" />
  </div>

  <input type="submit" class="btn btn-success form-control" name="add_series" value="添加主题" />
</form>
<?php include_once('footer.php');?>
