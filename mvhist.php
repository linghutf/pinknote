<?php include_once('header.php');?>
   <div class="row">
      <div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">观影记录</h3>
    </div>
    <div class="panel-body">
    <table id="data" class="table table-striped table-condensed">
    <thead>
       <tr>
           <th>中文名</th>
           <th>英文名</th>
           <th>评分</th>
           <th>上映日</th>
           <th>完成日</th>
           <th><a href="#" id="add" class="btn btn-success">添加</a></th>
       </tr>
       </thead>
            <tbody>
       </tbody>
    </table>
   </div>
  </div>
 </div>
<div class="row">
    <div class="span12">
        <p>本页<span id="itemnum" class="badge badge-inverse"></span>条&nbsp;
        总共<span id="totalnum" class="badge badge-inverse"></span>条</p>
    </div>
    
    <div class="col-sm-6 panel panel-success">
        <div class="panel-heading">
            <h3 id="title" class="panel-title">我的影评</h3>
        </div>
        <div class="panel-body">
            <!-- 浏览时显示为p,编辑时显示为textarea-->
            <p id="comment" class="form-control"></p>
        </div>
    </div>
    
    <div class="col-sm-6 pnael panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">删除记录</h3>
        </div>
        <div class="panel-body">
            <input type="text" id="delete_chn_name" name="delete_chn_name" placeholder="请输入影片中文名"/>
            <button type="button" id="delete" class="btn btn-danger btn-sm"/>删除</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        // 添加 
       var g_tab = $("table#data");
        /**
         * 影评功能 p模式和textarea模式
         * 1. 添加时 P->T,内容清空,标题改成新建
         *    11. 确认时，上传T中内容，T->P,内容不变 ，标题改成对应新加片名
         *    12. 取消时，T->P，丢弃内容，标题改变我的影评
         * 2. 修改时，P->T，内容保留,标题保留
         *    21. 确认时，比对T与隐藏字段input的内容，不同就上传，标题更新,T->P
         *    22. 取消时，T->P,内容重新获取input，标题复原
         */
       // 仅在文档载入时执行了一次
       // 获取总条数和每页显示条数
       $.get('api.php?action=getMvPageNum',{isTodo:0},function(res){
           if(res['status']!=='ok'){
               $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
               return;
           }
           var pageDiv = createPages(res['total'],res['itemNum'],'api.php?action=listMvHistory',g_tab,createRowDOM);
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
           // 点击名字显示影评
           var td = $('<td class="col-sm-3"></td>');
           var showA = $('<a href="#" id="show_info"></a>');
           showA.text(rowData["chn_name"]);
           // 点击显示影评
           showA.click(function(){
              $('h3#title').html('<strong>'+rowData['chn_name']+'</strong>的影评'); 
              $('p#comment').text(rowData['comment']); 
           });
           td.append(showA);
           tr.append(td);
           // 设置隐藏字段
           td.append($('<input id="old_comment" type="hidden"></input>').val(rowData['comment']));
           tr.append($('<td class="col-sm-3"></td>').text(rowData["eng_name"]));
           tr.append($('<td class="col-sm-1"></td>').text(rowData["rank"]));
           tr.append($('<td class="col-sm-2"></td>').text(rowData["pressDate"]));
           tr.append($('<td class="col-sm-2"></td>').text(rowData["finishDate"]));
           
           var td = $('<td></td>');
           
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
           var chn_name = rowData['chn_name'];
           var eng_name = rowData['eng_name'];
           var rank = rowData['rank'];
           var pressDate = rowData['pressDate'];
           var finishDate = rowData['finishDate'];
           // 写入影评
           var comment = rowData['comment'];
           var texta = $('<textarea id="create_comment" class="form-control" rows="5"></textarea>');
           texta.val(comment);
           var cpanel =$('p#comment');
           //替换可编辑
           cpanel.replaceWith(texta);
           
           // 创建新行
           var newRow = $('<tr></tr>');
           
           newRow.append($('<td></td>').append($('<input type="text" id="chn_name" name="chn_name" required="true"/>').val(chn_name)));
           newRow.append($('<td></td>').append($('<input type="text" id="eng_name" name="eng_name" />').val(eng_name)));
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
           var chn_name = $.trim($(curRow.find("input#chn_name")).val());
           var eng_name = $.trim($(curRow.find("input#eng_name")).val());
           var rank = $(curRow.find("input#rank")).val();
           var pressDate = $(curRow.find("input#pressDate")).val();
           var finishDate = $(curRow.find("input#finishDate")).val();
           var data = {};
           data['chn_name']=chn_name;
           data['eng_name']=eng_name;
           data['rank']=rank;
           data['pressDate']=pressDate;
           data['finishDate']=finishDate;
           return data;
       }
       
       function isNotModify(data)
       {
           return data['chn_name'] === undefined && data['eng_name'] === undefined && data['rank'] === undefined
           && data['pressDate'] === undefined && data['finishDate'] == undefined && data['comment'] === undefined;
       }
       // 更新事件处理
       function updateHandler()
       {
           // 获取当前影评
           var comment = $('td:eq(0) input#old_comment').text();
           // 获取当前行的信息
           var curRow = $(this).parent().parent();
           var chn_name = $(curRow.find('td:eq(0) a')).text();
           var eng_name = $(curRow.find('td:eq(1)')).text();
           var rank = $(curRow.find('td:eq(2)')).text();
           var pressDate = $(curRow.find('td:eq(3)')).text();
           var finishDate = $(curRow.find('td:eq(4)')).text();
           var comment = $(curRow.find('td:eq(0) input#old_comment')).val();
           var oldData = {chn_name:chn_name,eng_name:eng_name,rank:rank,pressDate:pressDate,finishDate:finishDate,comment:comment};
           // 创建编辑行
           var newRow = createNewEditRow(oldData);
           // 保存旧消息
           newRow.append($('<td></td>').append($('<input type="hidden" id="old_chn_name"/>').val(chn_name)));
           
           // 更新事件绑定
           // 更新操作，相比保存操作多了一步检查
           // 与旧数据相同则不发送
           $(newRow.find("a#save")).click(function(){
               // 获取编辑信息
               var curEditRow = $(this).parent().parent();
               var data = getEditRowData(curEditRow);
               // 获取影评
               var comment = $.trim($('textarea#create_comment').val());
               data['comment'] = comment;
                //得到原始旧消息
               var old_chn_name = $.trim($(curEditRow.find("input#old_chn_name")).val());
               data['old_chn_name']=old_chn_name;
               var cloneData = clone(data);
               
               for(k in oldData){
                   if(oldData[k]===data[k]){
                       delete data[k];
                       
                   }
               }

               if(!isNotModify(data)){
                   // 发送数据
                   $.post("api.php?action=updateMv",data,function(res){
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
                       //cpanel.text(texta.val());
                       // 变回p模式
                       $('textarea#create_comment').replaceWith($('<p id="comment"></p>').text($.trim($('textarea#create_comment').val())));
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
               // 切换回p状态
               
               $('h3#title').html('<strong>'+oldData['chn_name']+'</strong>的影评');
               var cpanel = $('<p id="comment"></p>');
               var texta = $('textarea#create_comment');
               texta.replaceWith(cpanel);
               // 重建内容
               cpanel.text(oldData['comment']);
           });
              
           // 改成编辑状态
           curRow.replaceWith(newRow);
           // 改成textarea模式
           var texta = $('<textarea id="create_comment" class="form-control" rows="5"></textarea>');
           $('p#comment').replaceWith(texta);
           
       }
       
       // 点击添加按钮
       $("a#add").click(function(){
           // 影评部分更改
           $('h3#title').html('<strong>新建影评</strong>');
           var texta = $('<textarea id="create_comment" class="form-control" rows="5"></textarea>');
           var cpanel =$('p#comment');
           cpanel.text('');//清空
           //替换可编辑
           cpanel.replaceWith(texta);
           // 创建新行
           var newRow = createNewEditRow({});
           // 添加到table
           g_tab.prepend(newRow);
           // 保存按钮重新绑定事件
           $(newRow.find("a#save")).click(addHandler);
           $(newRow.find('a#cancel')).click(function(){
               newRow.remove();
               //换回
               $('h3#title').text('我的影评');
               texta.replaceWith(cpanel);
           });
       });
       
       // 添加影片事件
       function addHandler()
       {
           var curRow = $(this).parent().parent();
           var data = getEditRowData(curRow);
           //获取评论
           data['comment'] = $.trim($('textarea#create_comment').val());
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
           //替换为p
           var cm = $.trim($('textarea#create_comment').val());
           var cpanel = $('<p id="comment" class="form-control"></p>');
           $(cpanel).text(cm);
           $('textarea#create_comment').replaceWith(cpanel);
           $('h3#title').text('<strong>'+data['chn_name']+'</strong>的影评');
       }
       
       // 删除记录
       $('button#delete').click(function(){
          // 设置此时不能添加
          $('a#save').each(function(){
             $(this).attr('active','false');
          });
          var chn_name = $('input#delete_chn_name').val(); 
          $.post('api.php?action=deleteMv',{chn_name:chn_name},function(res) {
              if(res['status']!=='ok'){
                   //显示错误
                   $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                   return;
               }
               // 如果在当前记录中，去掉当前行
               $('table tr:gt(0)').each(function(){
                   var name = $($(this).find('td:eq(0)')).text();
                  
                   if(name === chn_name){
                       $(this).remove();
                       // 更新统计
                       $('span#itemnum').text(parseInt($('span#itemnum').text())-1);
                       // 删除影评
                       $('h3#title').text('我的影评');
                       $('p#comment').text('');
                    
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
