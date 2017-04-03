<?php include_once('header.php');?>
<div class="row">
    <div class="center-block">
    <form action="<?php print $_SERVER['PHP_SELF']?>" method="post">


    <table class="table table-striped table-condensed">
        <thead>
       <tr>
           <th>完成</th>
           <th>片名</th>
           <th>主演</th>
           <th>上映日</th>

       </tr>
       </thead>
       <tbody>
<?php
require_once('connect.php');

$sql='SELECT topic,actor,pressDate FROM movie where finishDate==0 ORDER BY pressDate asc';

$ret = $db->query($sql);
while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    $pressDate = $row["pressDate"];
    $pressDate = date('Y-m-d',$pressDate);
    echo '<tr><td><input type="checkbox" name="finish_flag[]" value="'.$row['topic'].'" /></td><td>'.$row['topic'].'</td><td>'.
    $row['actor'].'</td><td>'.$pressDate.'</td></tr>';
}

// 标记完成的电影
if(!empty( $_POST['finish_flag']))
{
    $sql ='UPDATE movie set finishDate = '.strtotime(date('Y-m-d')).' where topic=\'';
    // 设置完成
    $finish_arr = $_POST['finish_flag'];
    for($i=0;$i<count($finish_arr);$i++)
    {
        $ret = $db->exec($sql.$finish_arr[$i].'\';');
        if(!$ret)
        {
            echo $db->lastErrorMsg()."<br/>";
        }

    }
    //刷新当前页面
    echo "<script type='text/javascript'>location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
}

$db->close();

?>
</tbody>
</table>
<input type="submit" class="btn btn-success form-control" value="完成"/>
    </form>
     </div>
</div>
<?php include_once('footer.php');?>
