<?php include_once('header.php');?>
   <div class="row">
        <div class="center-block">


    <table class="table table-striped table-condensed">
    <thead>
       <tr>
           <th>中文名</th>
           <th>英文名</th>
           <th>上映日</th>
           <th>完成日</th>
       </tr>
       </thead>
       <tbody>
<?php
// TODO 改成分页模式
require_once('connect.php');

if($row=$db->query('SELECT COUNT(chn_name) as tot FROM libmov where finishDate!=0;')->fetchArray(SQLITE3_ASSOC))
{
    $tot = $row['tot'];
}
$item_num = 10;
$page_num = ceil($tot/$item_num);
$page_id=$_GET['page_id'];

$page_start = $page_id*$item_num;

$sql= 'SELECT chn_name,eng_name,pressDate,finishDate FROM libmov where finishDate!=0 ORDER BY finishDate desc limit '.$page_id.','.$item_num.';';

$ret = $db->query($sql);
$num = 0;
while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    $pressDate = $row["pressDate"];
    $pressDate = date('Y-m-d',$pressDate);
    $finishDate = $row['finishDate'];
    $finishDate = date('Y-m-d',$finishDate);
    $chn_name = $row['chn_name'];
    $eng_name = $row['eng_name'];
    $num++;
    echo '<tr><td>'.$chn_name.'</td><td>'.
    $eng_name.'</td><td>'.$pressDate.'</td><td>'.$finishDate.'</td></tr>';
}
$db->close();
echo '<tr><td>总计:</td><td colspan="3">'.$num.'</td></tr>';


// 页码显示
$page_prev=$page_id-1;
if($page_prev<0) $page_prev=0;
echo <<< EOF
<ul class="pagination">
    <li><a href="{$_SERVER['PHP_SELF']}?page_id=$page_prev">&laquo;</a></li>
EOF;

if($page_num>1)
{
    for($i=0;$i<$page_num;++$i)
    {
        $j=$i+1;
        if($i==$page_id){
            echo <<< HTML
    <li class="active"><a href="{$_SERVER['PHP_SELF']}?page_id=$i">$j</a></li>
HTML;
        }else{
            echo <<< HTML
    <li><a href="{$_SERVER['PHP_SELF']}?page_id=$i">$j</a></li>
HTML;
        }
    }
}

$page_next=$page_id+1;
if($page_next>=$page_num) $page_next=$page_num-1;
echo <<< EOF
 <li><a href="{$_SERVER['PHP_SELF']}?page_id=$page_next">&raquo;</a></li>
</ul>
EOF;

?>
</tbody></table>
</div>
</div>
<?php include_once('footer.php');?>
