<?php include_once('header.php');?>
 <div class="leaderboard">

    <form class="form-horizontal" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="topic" class="col-sm-2 control-label">片名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="topic" name="topic" placeholder="请输片名" />
    </div>
  </div>

  <div class="form-group">
    <label for="actor" class="col-sm-2 control-label">艺人</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="actor" name="actor" placeholder="请输入艺人" />
    </div>
  </div>

  <div class="form-group">
    <label for="pressDate" class="col-sm-2 control-label">上映</label>
    <div class="col-sm-10">
      <input type="date" class="form-control" id="pressDate_start" name="pressDate_start" />
      <input type="date" class="form-control" id="pressDate_end" name="pressDate_end"/>
    </div>
  </div>

  <div class="form-group">
    <label for="pressDate" class="col-sm-2 control-label">观看</label>
    <div class="col-sm-10">
      <input type="date" class="form-control" id="finishDate_start" name="finishDate_start" />
      <input type="date" class="form-control" id="finishDate_end" name="finishDate_end"/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" class="btn btn-info form-control" name="search" value="搜索" />
    </div>
  </div>
</form>
</div>


<?php
/**
 * Created by PhpStorm.
 * User: xdc
 * Date: 2017/3/28
 * Time: 21:15
 */
require_once('connect.php');
require_once('utils.php');

if(isset($_POST['search']))
{
    //button无法收到post
    # 查询符合条件的主题
    $sql='SELECT topic,actor,pressDate,finishDate,rank from movie where 1==1';
    $topic = trim($_POST['topic']);
    $actor = trim($_POST['actor']);
    $pressDate_start = dt_to_unix($_POST['pressDate_start']);
    $pressDate_end = dt_to_unix($_POST['pressDate_end']);
    $finishDate_start = dt_to_unix($_POST['finishDate_start']);
    $finishDate_end = dt_to_unix($_POST['finishDate_end']);
    // 判断区间是否合法
    if($pressDate_start>$pressDate_end || $finishDate_start>$finishDate_end)
    {
        echo <<< EOF
        <script type="text/javascript">alert('日期不合法');</script>
EOF;
        exit;
    }

    if(!empty($topic))
    {
        $sql.=' AND topic like "%'.$topic.'%" COLLATE NOCASE';
    }
    if(!empty($actor))
    {
        $sql.=' AND actor like "%'.$actor.'%" COLLATE NOCASE';
    }
    if($pressDate_start!=0)
    {
        $sql.=' AND pressDate > '.$pressDate_start.' AND pressDate <='.$pressDate_end;
    }
    if($finishDate_start!=0)
    {
        $sql.=' AND finishDate > '.$finishDate_start.' AND finishDate <='.$finishDate_end;
    }
    $sql.=' order by pressDate desc;';

    $res = $db->query($sql);
    $first=true;
    while($row=$res->fetchArray(SQLITE3_ASSOC))
    {
        if($first){
            //查询结果
            echo '<div class="row">';

            echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
            echo '<table class="table table-striped table-condensed">';
            echo '<tr>
                    <th>影片</th>
                    <th>主演</th>
                    <th>上映日</th>
                    <th>完成日</th>
                    <th>评分</th>
                    <th>修改</th>
                </tr>';
            $first=!$first;
        }
        $pressDate = unix_to_dt($row['pressDate']);
        $finishDate = unix_to_dt($row['finishDate']);

        echo '<tr><td>'.$row['topic'].'</td><td>'.$row['actor'].'</td><td>'.$pressDate.'</td><td>'.$finishDate.'</td>';
        // radio保存topic传递
        echo '<td>'.$row['rank'].'</td>';
        echo '<td><input type="radio" name="ismodify" value="'.$row['topic'].'"/></td></tr>';
    }
    if(!$first){
       echo '</table>
            <input type="submit" name ="modify" class="btn btn-success form-control" value="修改"/><br/>
        </form>
        </div>';
    }
}
// 添加修改对话,radio决定只能修改一个
if(isset($_POST['modify'])) //"修改"
{

    $sql = 'SELECT topic,actor,pressDate,finishDate,rank from movie where topic="'.$_POST['ismodify'].'";';
    $res=$db->query($sql);
    if($row=$res->fetchArray(SQLITE3_ASSOC))
    {
        //首先显示修改框
        $topic = $row['topic'];
        $actor = $row['actor'];
        $dt_pressDate = unix_to_dt($row['pressDate']);
        $dt_finishDate = unix_to_dt($row['finishDate']);
        $rank = $row['rank'];
        echo <<< HTML
        <div class = "row">
        <form action="$_SERVER[PHP_SELF]" method="post">
        <table class="table table table-striped table-condensed">
        <tr><td>主题:</td><td><input type="text" name="topic" value="$topic"/></td></tr>
        <tr><td>主演:</td><td><input type="text" name="actor" value="$actor"/></td></tr>
        <tr><td>评分:</td><td><input type="number" name="rank" min="1" max="5" value="$rank"/></td></tr>
        <tr><td>上映日:</td><td><input type="date" name="pressDate" value="$dt_pressDate"/></td></tr>
HTML;

        if (!empty($dt_finishDate[0])) {
            echo '<tr><td>完成日:</td><td><input type="date" name="finishDate" value="' .$dt_finishDate. '"/></td></tr>';
        }

        echo '</table><input type="submit" name ="update" class="btn btn-success" value="更新"/><br/>';
        echo '</form></div>';
        setcookie('old_topic',time()-3000);
        setcookie('old_topic',$topic,time()+31536000,'/');//设置cookie在页面间传递
    }
}
// 修改数据
if(isset($_POST['update']))
{
    $old_topic=$_COOKIE['old_topic'];
    $sql = "";
    if (!isset($_POST['finishDate'])) {
        $sql = 'UPDATE movie SET topic=:topic,actor=:actor,pressDate=:pressDate,rank=:rank WHERE topic="'.$old_topic.'";';
    }else{
        $sql = 'UPDATE movie SET topic=:topic,actor=:actor,pressDate=:pressDate,finishDate=:finishDate,rank=:rank WHERE topic="'.$old_topic.'";';
    }

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':topic',$_POST['topic'],SQLITE3_TEXT);
    $stmt->bindValue(':actor',$_POST['actor'],SQLITE3_TEXT);
    $pressDate = dt_to_unix($_POST['pressDate']);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    $stmt->bindValue(':rank',$_POST['rank'],SQLITE3_INTEGER);
    if (isset($_POST['finishDate'])) {
        $finishDate = dt_to_unix($_POST['finishDate']);
        $stmt->bindValue(':finishDate',$finishDate,SQLITE3_INTEGER);
    }
    $ret = $stmt->execute();
    if(!$ret){
        echo '<p>'.$db->lastErrorMsg()."</p>";
        exit;
    }
}

$db->close();

?>
<?php include_once('footer.php');?>
