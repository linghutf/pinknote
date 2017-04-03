<?php include_once('header.php');?>
   <form class="form-horizontal" role="form" action="<?php print $_SERVER['PHP_SELF']?>" method="post">
  <div class="form-group">
    <label for="firstname" class="col-sm-2 control-label">中文名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="firstname" name="chn_name" placeholder="请输入电影中文名" />
    </div>
  </div>

  <div class="form-group">
    <label for="lastname" class="col-sm-2 control-label">英文名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="lastname" name="eng_name" placeholder="请输入英文名" />
    </div>
  </div>

  <div class="form-group">
    <label for="pressDate" class="col-sm-2 control-label">上映日</label>
    <div class="col-sm-10">
      <input type="date" class="form-control" id="pressDate" name="pressDate" />
    </div>
  </div>

  <div class="form-group">
    <label for="rank" class="col-sm-2 control-label">评分</label>
    <div class="col-sm-10">
      <input type="number" class="form-control" id="rank" name="rank" min="0" max="5" value="1" />
    </div>
  </div>

  <div class="form-group">
    <label for="comment" class="col-sm-2 control-label">观后感</label>
    <div class="col-sm-10 row-sm-20">
    <textarea class="form-control" id="comment" rows="comment"></textarea>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" class="btn btn-success form-control" name="add_mov" value="添加" />
    </div>
  </div>
</form>

<?php
require_once('connect.php');

// 提交到数据库
if(isset( $_POST['add_mov']))
{

    $chn_name = trim($_POST['chn_name']);
    $eng_name = trim($_POST['eng_name']);
    $rank = $_POST['rank'];
    $pressDate = strtotime($_POST["pressDate"]);
    $comment = trim($_POST['comment']);

    $sql ='INSERT INTO libmov(chn_name,eng_name,rank,pressDate,comment)
    values(:chn_name,:eng_name,:rank,:pressDate,:comment);';


    $stmt = $db->prepare($sql);
    $stmt->bindValue(':chn_name',$chn_name,SQLITE3_TEXT);
    $stmt->bindValue(':eng_name',$eng_name,SQLITE3_TEXT);
    $stmt->bindValue(':rank',$rank,SQLITE3_INTEGER);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    $stmt->bindValue(':comment',$comment,SQLITE3_TEXT);

    $ret = $stmt->execute();//返回true/false
    if(!$ret){
        echo $db->lastErrorMsg()."<br/>";
    }
    $db->close();
}

include_once('footer.php');
?>
