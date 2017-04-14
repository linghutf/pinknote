<?php $title="电影检索";include_once('header.php');?>
<?php $title="日本历史记录";include_once('header.php');?>
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
<div class="row">
    <table id="data" class="table table-striped table-condensed">
        <thead>
        <tr>
            <th>中文名</th>
            <th>英文名</th>
            <th>评分</th>
            <th>上映日</th>
            <th>完成日</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="row">

</div>
<script type="text/javascript">
    $(document).ready(function(){

    })
</script>

<?php include_once('footer.php');?>
