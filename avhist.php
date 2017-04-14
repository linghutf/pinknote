<?php $title="日本历史记录";include_once('header.php');?>
<script type="text/javascript" src="js/avhist.js"></script>

<div class="row">
    <div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">历史记录</h3>
    </div>
    <div class="panel-body">
        <table id="data" class="table table-striped table-condensed">
           <thead><tr>
           <th>主题</th>
           <th>艺人</th>
           <th>评分</th>
           <th>上映日</th>
           <th>完成日</th>
           <th><a href="#" id="add" class="btn btn-success">添加</a></th>
           </tr></thead>
       <tbody></tbody>
       </table>
    </div>
    </div>
</div>
<div class="row">
    <div class="span12">
        <p>本页<span id="itemnum" class="badge badge-inverse"></span>条&nbsp;
        总共<span id="totalnum" class="badge badge-inverse"></span>条</p>
    </div>
        <!-- 删除错误记录接口 -->
       <div class="col-sm-6 pnael panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">删除主题</h3>
        </div>
        <div class="panel-body">
            <input type="text" id="delete_topic" name="delete_topic" placeholder="请输入影片主题"/>
            <button type="button" id="delete" class="btn btn-danger btn-sm"/>删除</button>
        </div>
       </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
       // 添加 
       var g_tab = $("table#data");
       // 仅在文档载入时执行了一次
       // 获取总条数和每页显示条数
       $.get('api.php?action=getAvPageNum',{isTodo:0},function(res){
           if(res['status']!=='ok'){
               $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
               return;
           }
           var pageDiv = createPages(res['total'],res['itemNum'],'api.php?action=listAvHistory',g_tab,createRowDOM);
           // 默认第一页active
           $(pageDiv.find('li:eq(0)')).addClass('active');
           $('div.container:eq(1)').prepend(pageDiv);
           //自动触发第一页
           $('a#pageId:eq(0)').trigger("click");
          
       });

       // 点击添加按钮
       $("a#add").click(function(){
           //$(this).attr("disables",true);//不可再点击
           //$("div#add_row").show();
           // 创建新行
           var newRow = createNewEditRow({});
           // 添加到table
           g_tab.prepend(newRow);
           // 保存按钮重新绑定事件
           $(newRow.find("a#save")).click(addHandler);
           $(newRow.find('a#cancel')).click(function(){
               newRow.remove();
           });
       });
       
       // 添加影片事件
       function addHandler()
       {
           var curRow = $(this).parent().parent();
           var data = getEditRowData(curRow);
           // 发送数据
           $.post("api.php?action=addAv",data,function(res){
               if(res['status']!=='ok'){
                   //显示错误
                   $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                   return;
               }
               // 添加到table
               g_tab.prepend(createRowDOM(data));
               // 移除当前编辑行
               curRow.remove();
               // 更新统计
               $('span#itemnum').text(parseInt($('span#itemnum').text())+1);
               $('span#totalnum').text(parseInt($('span#totalnum').text())+1);
           });
       }
       
       // 删除记录
       $('button#delete').click(function(){
          // 设置此时不能添加
          $('a#save').each(function(){
             $(this).attr('active','false');
          });
          var topic = $.trim($('input#delete_topic').val()); 
          $.post('api.php?action=deleteAv',{topic:topic},function(res) {
              if(res['status']!=='ok'){
                   //显示错误
                   $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                   return;
               }
               // 如果在当前记录中，去掉当前行
               $('table tr:gr(0)').each(function(){
                   var name = $($(this).find('td:eq(0)')).text();
                  
                   if(name === topic){
                       $(this).remove();
                       // 更新统计
                       $('span#itemnum').text(parseInt($('span#itemnum').text())-1);
                   }
               });
               $('span#totalnum').text(parseInt($('span#totalnum').text())-1);
          });
          // 恢复
          $('a#save').each(function(){
             $(this).attr('active','true');
          });
       });
       
    });
</script>

<?php include_once('footer.php');?>
