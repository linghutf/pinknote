<?php $title="观影历史记录";
include_once('header.php');?>
<script type="text/javascript" src="js/mvhist.js"></script>
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
           $.post("api.php?action=addMv",data,function(res){
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
           $('h3#title').html('<strong>'+data['chn_name']+'</strong>的影评');
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
