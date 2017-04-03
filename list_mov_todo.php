<?php include_once('header.php');?>
   <div class="leaderboard">
    <form action="<?php print $_SERVER['PHP_SELF']?>" method="post">

    <table class="table table-striped table-condensed">
        <thead>
       <tr>
           <th>完成</th>
           <th>中文名</th>
           <th>英文名</th>
           <th>上映日</th>

       </tr>
       </thead>
       <tbody>
<?php
require_once('connect.php');

$sql='SELECT chn_name,eng_name,pressDate FROM libmov where finishDate==0 ORDER BY pressDate asc,rank desc';

$ret = $db->query($sql);
while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    $pressDate = $row["pressDate"];
    $pressDate = date('Y-m-d',$pressDate);
    echo '<tr><td><input type="checkbox" name="finish_flag[]" value="'.$row['chn_name'].'" /></td><td>'.$row['chn_name'].'</td><td>'.
    $row['eng_name'].'</td><td>'.$pressDate.'</td>
    </tr>';
}

// 标记完成的电影
if(isset( $_POST['finish_flag']))
{
    $sql ='UPDATE libmov set finishDate = '.strtotime(date('Y-m-d')).' where chn_name=\'';
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

<?php include_once('footer.php');?>
