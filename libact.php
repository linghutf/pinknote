<?php include_once('header.php');?>
   <div class="row">
        <form action="<?php print $_SERVER['PHP_SELF']?>" method="post">
            <table class="table table-striped table-condensed">
            <thead><tr>
                <th>艺人</th>
                <th>热度</th>
                <th>修改</th>
            </tr>
            </thead>
            <tbody>

<?php
require_once("connect.php");
require_once("utils.php");

// 主题库
$sql = "SELECT name,rank FROM actress order by name asc,rank desc;";
$res=$db->query($sql);
while($row=$res->fetchArray(SQLITE3_ASSOC))
{
    // TODO 有数据更改时写回数据库
    // 怎么通过name拿到这些改变的值，需要一个缓存层，监控变化
    //echo '<tr><td><input type="text" name="'.$row['name'].'" value="'.$row['name'].'" /></td>
    //<td><input type="number" max="5" min="1" name="'.$row['rank'].'" value="'.$row['rank'].'" /></td>';
    //没有解决中间层，使用两次查询的落后方式
    echo '<tr><td>'.$row['name'].'</td><td>'.$row['rank'].'</td>
    <td><input type="radio" name="actor" value="'.$row['name'].'" /></td></tr>';
}

// 更新艺人
if(isset($_POST['mark']))
{
    $name=$_POST['actor'];
    //想办法保存rank值，如post json串，则不需要再查询一遍
    $sql = 'SELECT rank from actress where name="'.$name.'";';

    if($row=$db->query($sql)->fetchArray(SQLITE3_ASSOC))
    {
        setcookie('actress',$name);
        $rank=$row['rank'];
        print <<< HTML

        <form action="$_SERVER[PHP_SELF]" method="post">
        <table>
            <tr><td>艺人:</td><td><input type="text" name="m_name" value="$name" required="true"/></td>
            <td>热度:</td><td><input type="number" name="m_rank" min="1" max="5" value="$rank" /></td></tr>
            <tr><td colspan="2"><input type="submit" name="update" value="更新" /></td></tr></table></form>
HTML;
    }
}

if(isset($_POST['update']))
{
    $name = $_COOKIE['actress'];
    $sql = 'UPDATE actress SET name="'.$_POST['m_name'].'",rank='.$_POST['m_rank'].' where name="'.$name.'";';
    if(!$db->exec($sql))
    {
        echo '<p>'.$db->lastErrorMsg().'</p>';
        exit;
    }
}

//添加艺人
if(isset($_POST['add_actress']))
{
    // curDate默认0,没有看过
    $sql = "INSERT INTO actress(name,rank) values('".$_POST['name_add']."',".$_POST['rank_add'].");";
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
            <tr><td colspan="2"><input type="submit" name="mark" value="确认"/></td></tr>
            </tbody>
            </table>
        </form>
        </div>
        <!--添加艺人-->
        <div class="row">
<form class="form-inline" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label" for="name_add">艺人</label>
    <input type="text" class="form-control" id="name_add" name="name_add" placeholder="请输入艺人" />
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label" for="rank_add">热度</label>
    <input type="number" class="form-control" id="rank_add" name="rank_add" min="1" max="5" value="1" />
  </div>

  <input type="submit" class="btn btn-success form-control" name="add_actress" value="添加艺人" />
</form>
</div>
<?php include_once('footer.php');?>
