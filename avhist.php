<?php $title="日本历史记录";include_once('header.php');?>
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
        <div class="panel panel-danger">
        <!--div class="panel-heading">
        <h3 class="panel-title">历史记录</h3>
         </div-->
        <div class="form-inline">
            <label for="delete_topic">主题</label>
            <input type="text" id="delete_topic" name="delete_topic"/>
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
       
    
       
       
       // 由对应数据生成表格行DOM
       function createRowDOM(rowData)
       {
           var tr = $("<tr><</tr>");
           tr.append($('<td class="col-sm-3"></td>').text(rowData["topic"]));
           tr.append($('<td class="col-sm-3"></td>').text(rowData["actor"]));
           tr.append($('<td class="col-sm-1"></td>').text(rowData["rank"]));
           tr.append($('<td class="col-sm-2"></td>').text(rowData["pressDate"]));
           tr.append($('<td class="col-sm-2"></td>').text(rowData["finishDate"]));
           var td = $('<td></td>');
           /*
           var finishBtn = $('<a href="#" id="finish" class="btn btn-xs">完成</a>');
           // 绑定标记完成
           finishBtn.click(finishHandler);
           */
           var uptBtn = $('<a href="#" id="update" class="btn-primary btn-xs">更改</a>');
           uptBtn.click(updateHandler);
       
           //td.append(finishBtn);
           td.append(uptBtn);
           tr.append(td);
       
           return tr;
       }
       
       // 公共函数
       // 由数据创建编辑行DOM
       function createNewEditRow(rowData)
       {
           var topic = rowData['topic'];
           var actor = rowData['actor'];
           var rank = rowData['rank'];
           var pressDate = rowData['pressDate'];
           var finishDate = rowData['finishDate'];
           // 创建新行
           var newRow = $('<tr></tr>');
           
           newRow.append($('<td></td>').append($('<input type="text" id="topic" name="topic" required="true"/>').val(topic)));
           newRow.append($('<td></td>').append($('<input type="text" id="actor" name="actor" />').val(actor)));
           newRow.append($('<td></td>').append($('<input type="number" id="rank" name="rank" min="1" max="5" required="true"/>').val(rank)));
           newRow.append($('<td></td>').append($('<input type="date" id="pressDate" name="pressDate" required="true" />').val(pressDate)));
           newRow.append($('<td></td>').append($('<input type="date" id="finishDate" name="finishDate" required="true" />').val(finishDate)));
           // 事件处理待绑定
           newRow.append($('<td><a href="#" id="save" class="btn-success btn-xs">保存</a><a href="#" id="cancel" class="btn-info btn-xs">取消</a></td>'));
           
           return newRow;
       }
       // 公共函数
       // 获取编辑行数据
       function getEditRowData(curRow)
       {
           var topic = $.trim($(curRow.find("input#topic")).val());
           var actor = $.trim($(curRow.find("input#actor")).val());
           var rank = $(curRow.find("input#rank")).val();
           var pressDate = $(curRow.find("input#pressDate")).val();
           var finishDate = $(curRow.find("input#finishDate")).val();
           var data = {};
           data['topic']=topic;
           data['actor']=actor;
           data['rank']=rank;
           data['pressDate']=pressDate;
           data['finishDate']=finishDate;
           return data;
       }
       
       function isNotModify(data)
       {
           return data['topic'] === undefined && data['actor'] === undefined && data['rank'] === undefined
           && data['pressDate'] === undefined && data['finishDate'] == undefined;
       }
       // 更新事件处理
       function updateHandler()
       {
           
           // 获取当前行的信息
           var curRow = $(this).parent().parent();
           var topic = $(curRow.find('td:eq(0)')).text();
           var actor = $(curRow.find('td:eq(1)')).text();
           var rank = $(curRow.find('td:eq(2)')).text();
           var pressDate = $(curRow.find('td:eq(3)')).text();
           var finishDate = $(curRow.find('td:eq(4)')).text();
           var oldData = {topic:topic,actor:actor,rank:rank,pressDate:pressDate,finishDate:finishDate};
           // 创建编辑行
           var newRow = createNewEditRow(oldData);
           // 保存旧消息
           newRow.append($('<td></td>').append($('<input type="hidden" id="old_topic"/>').val(topic)));
           
           
           // 更新事件绑定
           // 更新操作，相比保存操作多了一步检查
           // 与旧数据相同则不发送
           $(newRow.find("a#save")).click(function(){
               // 获取编辑信息
               var curEditRow = $(this).parent().parent();
               var data = getEditRowData(curEditRow);
                //得到原始旧消息
               var old_topic = $.trim($(curEditRow.find("input#old_topic")).val());
               data['old_topic']=old_topic;
               var cloneData = clone(data);
               
               for(k in oldData){
                   if(oldData[k]===data[k]){
                       delete data[k];
                       
                   }
               }
               // 没有考虑没有艺人的情况
               if(!isNotModify(data)){
                   // 发送数据
                   $.post("api.php?action=updateAv",data,function(res){
                       if(res['status']!=='ok'){
                           //显示错误
                           $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                           // 回退状态
                           curEditRow.replaceWith(curRow);
                           return;
                       }
                       // 当前编辑行移除
                       var newRow = createRowDOM(cloneData);
                       curEditRow.replaceWith(newRow);
                   });
               }else{
                   // 回退状态
                   curEditRow.replaceWith(curRow);
               }
           });
           
           // 取消事件绑定
           $(newRow.find("a#cancel")).click(function(){
               // 撤销替换
               newRow.replaceWith(curRow);
               // 重新绑定事件
               $(curRow.find('a#update')).click(updateHandler);
           });
              
           // 改成编辑状态
           curRow.replaceWith(newRow);
           
       }
       
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
          var topic = $('input#delete_topic').val(); 
          $.post('api.php?action=deleteAv',{topic:topic},function(res) {
              if(res['status']!=='ok'){
                   //显示错误
                   $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                   return;
               }
               // 如果在当前记录中，去掉当前行
               $('table tr:gt(0)').each(function(){
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
