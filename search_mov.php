<?php include_once('header.php');?>
  <div class="leaderboard">

    <form class="form-horizontal" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="topic" class="col-sm-2 control-label">中文名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="topic" name="chn_name" placeholder="请输入片名" />
    </div>
  </div>

  <div class="form-group">
    <label for="actor" class="col-sm-2 control-label">英文名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="actor" name="eng_name" />
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
      <input type="submit" class="btn btn-info form-control" name="search_mov" value="搜索" />
    </div>
  </div>
</form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("button#modify").each(function(){
            $(this).click(function(){
                $("form#molan_form").remove();
                var tr = $(this).parent().parent();
                var chn_name = $(tr.find("td:eq(0)")).text();
                var eng_name = $(tr.find("td:eq(1)")).text();
                var rank = $(tr.find("td:eq(2)")).text();
                var pressDate = $(tr.find("td:eq(3)")).text();
                var finishDate = $(tr.find("td:eq(4)")).text();
                var comment = $(tr.find("td:eq(5)")).text();
                // 动态生成表单
               var form = $('<form id="molan_form" action="<?php print $_SERVER['PHP_SELF']?>" method="POST"></form>');
               
               // 仅有两个平行借点label,input无法find
               // 需要有一个父节点，才能由外向内查找
               var p1 = $('<div class="form-group"><label for="chn_name" class="form-control">中文名:</label><input type="text" name="chn_name" /></div>');
               $(p1.find('input')).val(chn_name);
               
               var p2 = $('<div class="form-group"><label for="eng_name" class="form-control">英文名:</label><input type="text" name="eng_name" /></div>');
               $(p2.find('input')).val(eng_name);
               var p3 = $('<div class="form-group"><label for="topic" class="form-control">评分</label><input type="number" name="rank" min="1" max="5" /></div>');
               $(p3.find('input')).val(rank);
               var p4 = $('<div class="form-group"><label for="pressDate" class="form-control">上映:</label><input type="date" name="pressDate" /></div>');
               $(p4.find('input')).val(pressDate);
               var p5 = null;
               if(finishDate!==""){
                   p5 = $('<div class="form-group"><label for="finishDate" class="form-control">完成:</label><input type="date" name="finishDate" /></div>');
                   $(p5.find('input')).val(finishDate);
               }
               var p6 = $('<div class="form-group"><label for="comment" class="form-control">评价:</label><<textarea name="comment" rows="10" cols="60"></textarea></div>');
               $(p6.find('textarea')).text(comment);
               
               var old = $('<div class="form-group"><input type="hidden" name ="old_chn_name" /></div>');
               $(old.find('input')).val(chn_name);
               var ck = $('<div class="form-group"><input type="submit" name ="update_mov" class="btn btn-success" value="更新"/></div>');
               
               form.append(p1);
               form.append(p2);
               form.append(p3);
               form.append(p4);
               if(finishDate!==""){
                   form.append(p5);
               }
               form.append(p6);
               form.append(old);
               form.append(ck);
               $('div#molan').append(form);
            })
        })
    })
</script>

<?php
/**
 * Created by PhpStorm.
 * User: xdc
 * Date: 2017/3/28
 * Time: 21:15
 */
require_once('connect.php');
require_once('utils.php');

if(isset($_POST['search_mov']))
{
    //var_dump($_POST);
    # 查询符合条件的主题
    $sql='SELECT chn_name,eng_name,rank,pressDate,finishDate,comment from libmov where 1==1';
    $chn_name = trim($_POST['chn_name']);
    $eng_name = trim($_POST['eng_name']);
    $pressDate_start = dt_to_unix($_POST['pressDate_start']);
    $pressDate_end = dt_to_unix($_POST['pressDate_end']);
    $finishDate_start = dt_to_unix($_POST['finishDate_start']);
    $finishDate_end = dt_to_unix($_POST['finishDate_end']);
    // 判断区间是否合法
    if($pressDate_start>$pressDate_end || $finishDate_start>$finishDate_end)
    {
        echo '<script>alert(\'日期不合法\');/*location.href=\'".$_SERVER["HTTP_REFERER"]."\';*/</script>';
        exit;
    }

    if(isset($chn_name))
    {
        $sql.=' AND chn_name like "%'.$chn_name.'%" COLLATE NOCASE';
    }
    if(isset($eng_name))
    {
        $sql.=' AND eng_name like "%'.$eng_name.'%" COLLATE NOCASE';
    }
    if($pressDate_start!=0)
    {
        $sql.=' AND pressDate > '.$pressDate_start.' AND pressDate <='.$pressDate_end;
    }
    if($finishDate_start!=0)
    {
        $sql.=' AND finishDate > '.$finishDate_start.' AND finishDate <='.$finishDate_end;
    }
    $sql.=' order by rank desc,pressDate desc;';
    //echo '<p>'.$sql.'</p>';

    $res = $db->query($sql);
    $first=true;
    while($row=$res->fetchArray(SQLITE3_ASSOC))
    {
        if($first){
            //查询结果
            echo <<< HTML
            <div class="row">
            <table class="table table-striped table-condensed">
            <tr>
                    <th>中文名</th>
                    <th>英文名</th>
                    <th>评分</th>
                    <th>上映日</th>
                    <th>完成日</th>
                    <th>评</th>
                    <th>修改</th>
            </tr>
HTML;
            $first=!$first;
        }
        $pressDate = unix_to_dt($row['pressDate']);
        $finishDate = unix_to_dt($row['finishDate']);

        echo '<tr><td>'.$row['chn_name'].'</td><td>'.$row['eng_name'].'</td><td>'.$row['rank'].'</td><td>'.$pressDate.'</td><td>'.$finishDate.'</td><td>'.$row['comment'].'</td>';
        // radio保存chn_name传递
        echo '<td><button type="button" id="modify" name="modify" class="btn btn-danger"/>搜索</button></td></tr>';
    }
    if(!$first){
       echo '</table>
        </div>';
       echo '<div id="molan" class="row"></div>';
    }
}

/*
// 添加修改对话,radio决定只能修改一个
if(isset($_POST['modify_mov'])) //"修改"
{

    $sql = 'SELECT chn_name,eng_name,pressDate,finishDate,rank,comment from libmov where chn_name="'.$_POST['ismodify'].'";';
    $res=$db->query($sql);
    if($row=$res->fetchArray(SQLITE3_ASSOC))
    {
        $dt_pressDate = unix_to_dt($row['pressDate']);
        $dt_finishDate = unix_to_dt($row['finishDate']);
        //首先显示修改框
        print <<< HTML
        <div class="row">
        <div class="center-block">
        <form action="{$_SERVER['PHP_SELF']}" method="post">
        <table class="table table-striped table-condensed">
        <tr><td>中文名:</td><td><input type="text" name="chn_name" value="{$row['chn_name']}"/></td></tr>
        <tr><td>英文名:</td><td><input type="text" name="eng_name" value="{$row['eng_name']}"/></td></tr>
        <tr><td>上映日:</td><td><input type="date" name="pressDate" value="{$dt_pressDate}"/></td></tr>
HTML;

        if (!empty($dt_finishDate)) {
            echo '<tr><td>完成日:</td><td><input type="date" name="finishDate" value="'.$dt_finishDate . '"/></td></tr>';
        }
        print <<< HTML
        <tr><td>评分:</td><td><input type="number" name="rank" min="0" max="5" value="{$row['rank']}"/></td></tr>
        </table>
        评价:<textarea name="comment" rows="10" cols="60">{$row['comment']}</textarea><br/>
        <input type="submit" name ="update_mov" value="更新"/><br/>
        </form>
        </div>
        </div>
HTML;
        setcookie('old_name',$row['chn_name']);//设置cookie在页面间传递
        //setcookie('old_mov_finishDate',$row['finishDate']);
    }
}
*/
// 修改数据
if(isset($_POST['update_mov']))
{
    //$old_name=$_COOKIE['old_name'];
    $old_name=$_POST['old_chn_name'];
    //$old_mov_finishDate = $_COOKIE['old_mov_finishDate'];
    $sql = "";
    if (empty($_POST['finishDate'])) {//没有此行
        $sql = 'UPDATE libmov SET chn_name=:topic,eng_name=:actor,pressDate=:pressDate,rank=:rank,comment=:comment WHERE chn_name="'.$old_name.'";';
    }else{
        $sql = 'UPDATE libmov SET chn_name=:topic,eng_name=:actor,pressDate=:pressDate,finishDate=:finishDate,rank=:rank,comment=:comment WHERE chn_name="'.$old_name.'";';
    }
    
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':topic',trim($_POST['chn_name']),SQLITE3_TEXT);
    $stmt->bindValue(':actor',trim($_POST['eng_name']),SQLITE3_TEXT);
    $pressDate = dt_to_unix($_POST['pressDate']);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    $stmt->bindValue(':rank',$_POST['rank'],SQLITE3_INTEGER);
    $stmt->bindValue(':comment',trim($_POST['comment']),SQLITE3_TEXT);
    if (!empty($_POST['finishDate'])) {
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
