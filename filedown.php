<?php $title="文件下载";include_once('header.php');?>
<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">文件列表</h3>
        </div>
        <div class="panel-body">
            <table id="data" class="table table-striped table-condensed">
                <thead><tr>
                    <td>文件名</td>
                    <td>大小</td>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var g_tab = $('table#data');
        $.get('api.php?action=filelist',{},function(res){
            if (res['status'] !== 'ok') {
                //显示错误
                $('body').append($('<div class="alert alert-error"></div>').text(res['status']));
                // 回退状态
                return;
            }
            var filelist = res['data'];
            for(var i=0;i<filelist.length;++i){
                g_tab.append(createRow(filelist[i]));
            }
        });

        function createRow(rowData){
            var tr = $('<tr></td>');
            var a = $('<a></a>');
            $(a).attr('href',rowData['url']);
            $(a).text(rowData['name']);
            //tr.append($('<td></td>').append(rowData['name']));
            tr.append($('<td></td>').append(a));
            var size = parseInt(rowData['size'])>>10;
            tr.append($('<td></td>').text(size+'KB'));
            return tr;
        }
    });
</script>


<?php include_once('footer.php');?>
