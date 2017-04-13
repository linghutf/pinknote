//分页组件
       
       function createPages(totalItems,itemNum,postURL,table,create_func)
       {
           var pageDiv = $('<ul class="pagination"></ul>');
           var pages = Math.ceil(totalItems/itemNum);
           
           for(var i=0;i<pages;++i){
               var item = $('<a href="#" id="pageId"></a>');
               $(item).text(i+1);
               // 事件需要进一步分解
               $(item).click(function(){
                   // 取消其他页活动
                   $(this).parent().parent().find('li').removeClass("active");
                   // 设置当前页活动
                   $(this).parent().addClass("active");
                   
                   var pageId = parseInt($(this).text())-1;
                   // 请求数据
                   $.post(postURL,{offset:pageId*itemNum,num:itemNum},function(res){
                       if(res['status']!=='ok'){
                           $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                           return;
                       }
                       //移除旧数据
                       $(table.find('tr:gt(0)')).remove();
                       var nums = res["data"].length;//data作为key
                       for(var i=0;i<nums;++i){
                           var row = create_func(res["data"][i]);
                           table.append(row);
                       }
                       // 添加条数到统计信息
                       $('span#itemnum').text(nums);
                       $('span#totalnum').text(totalItems);
                   });
               });
               var li = $('<li></li>');
               li.append(item);
               pageDiv.append(li);
              
           }
           return pageDiv;
       }
       