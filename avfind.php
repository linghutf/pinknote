<?php $title="影片检索";include_once('header.php');?>
    <script type="text/javascript" src="js/avhist.js"></script>
 <div class="leaderboard">

    <div id = "search" class="form-horizontal">
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
        <button id="search" class="btn btn-info form-control" name="search" value="" />搜索</button>
    </div>
  </div>
</div>
</div>

<div class="row">
    <table id="data" class="table table-striped table-condensed">
        <thead><tr>
            <th>主题</th>
            <th>艺人</th>
            <th>评分</th>
            <th>上映日</th>
            <th>完成日</th>
            <th>操作</th>
        </tr></thead>
        <tbody></tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var g_tab = $('table#data');

        $('button#search').click(searchHandler);
        function searchHandler() {
            var topic = $.trim($('div#search input#topic').val());
            var actor = $.trim($('div#search input#actor').val());
            var pressDate_start = $('div#search input#pressDate_start').val();
            var pressDate_end = $('div#search input#pressDate_end').val();
            var finishDate_start = $('div#search input#finishDate_start').val();
            var finishDate_end = $('div#search input#finishDate_end').val();

            var data = {};
            if (topic != '') data['topic'] = topic;
            if (actor != '') data['actor'] = actor;
            if (pressDate_start != '') data['pressDate_start'] = pressDate_start;
            if (pressDate_end != '') data['pressDate_end'] = pressDate_end;
            if (finishDate_start != '') data['finishDate_start'] = finishDate_start;
            if (finishDate_end != '') data['finishDate_end'] = finishDate_end;

            //if(topic==''&&actor==''&&pressDate_start==''&&pressDate_end==''&&finishDate_start==''&&finishDate_end==''){
            if (JSON.stringify(data) == '{}') {
                alert('输入参数为空!');
                return;
            }
            if (pressDate_start < pressDate_end || finishDate_end < finishDate_start) {
                alert("时间不对!");
                return;
            }

            $.post('api.php?action=findAv', data, function (res) {
                if (res['status'] !== 'ok') {
                    //显示错误
                    $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                    // 回退状态
                    return;
                }

                //移除旧数据
                $(g_tab.find('tr:gt(0)')).remove();
                var nums = res["data"].length;//data作为key
                for (var i = 0; i < nums; ++i) {
                    var row = createRowDOM(res["data"][i]);
                    g_tab.append(row);
                }

            });

        }
    });

</script>
<?php include_once('footer.php');?>