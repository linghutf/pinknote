/**
 * Created by xdc on 2017/4/14.
 */
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
