<?php
header('Content-type: text/json;charset=utf-8');

require_once('database.php');
require_once('utils.php');

// 业务逻辑
$type = $_GET['action'];
switch($type)
{
    case "getAvPageNum":echo getPageNum($db,'movie');break;
    case "getMvPageNum":echo getPageNum($db,'libmov');break;
    case "getTodoPageNum":echo getPageNum($db,'todos');break;
    // 任务中心功能
    case "addAv":echo addAv($db);break;
    case "listAvTodo":echo listAv($db,$isTodo=true);break;
    case "finishAvTodo":echo finishAvTodo($db);break;
    // 未完成，已完成的共用更新方法
    case "updateAv":echo updateAv($db);break;
    case "listAvHistory":echo listAv($db,$isTodo=false);break;
    case "deleteAv":echo deleteAv($db);break;
    case "findAv":echo findAv($db);break;
    //case "listAvDetail":echo listAvDetail($db);break;
    
    case "addMv":echo addMv($db);break;
    case "listMvTodo":echo listMv($db,$isTodo=true);break;
    case "deleteMv":echo deleteMv($db);break;
    case "finishMvTodo":echo finishMvTodo($db);break;
    case "updateMv":echo updateMv($db);break;
    case "listMvHistory":echo listMv($db,$isTodo=false);break;
    case "findMv":echo findMv($db);break;
    
    // 展示影片详情,更新影评
    //case "listMvDetail":echo listMvDetail($db);break;

    // 如果查阅日期小于当月第一天，就推送该主题，否则不推送
    // 左下角显示最热门的n个话题 TODO:引入统计图
    // 主题分计算公式 = (aver(A主题对应每部评分)*A出现次数/总现有次数)*5
    // date --- aver_rank 组成一条记录，最后画成折线图，就可以知道该话题的趋势
    case "getAvSerieNum":echo getSerieNum($db);break;
    case "addAvSerie":echo addAvSerie($db);break;
    case "listAvSerieTodo":echo listAvSerie($db,$isTodo=true);break;
    case "listAvSerieHist":echo listAvSerie($db,$isTodo=false);break;
    case "updateAvSerie":echo updateAvSerie($db);break;
    case "finishAvSerieTodo":echo finishAvSerieTodo($db);break;
    case "deleteAvSerie":echo deleteAvSerie($db);break;

    case "listTodo":echo listTodo($db,$isTodo=true);break;
    case "addTodo":echo addTodo($db);break;
    case "updateTodo":echo updateTodo($db);break;
    case "finishTodo":echo finishTodo($db);break;
}
// 结束
$db->close();

/**
 *  电影类操作
 */
// 区分是否todo
function getPageNum($db,$table)
{
    $itemNum = 16; // 每页展示条数
    $sql = 'SELECT COUNT(*) as ct FROM '.$table.' where finishDate';
    if($table == 'todos'){
        $sql = 'SELECT count(*) as ct FROM todos where finish_at is';
        if($_GET['isTodo']==1){
            $sql.=' null';
        }else{
            $sql.=' not null';
        }
    }else {
        // 默认是显示todo
        if ($_GET['isTodo'] == 1)//参数是字符串，需要转换
        {
            $sql .= '==0';
        } else {
            $sql .= '!=0';
        }
    }
    $ret = $db->query($sql);
    $total = 0;
    if($row=$ret->fetchArray(SQLITE3_ASSOC))
    {
        $total = $row['ct'];
    }
    
    $res=['status'=>'ok'];
    $res['total'] = $total;
    $res['itemNum'] = $itemNum;
    return json_encode($res);
    
}
// 添加影片
function addAv($db)
{
    $pressDate = strtotime($_POST["pressDate"]);

    $finishDate = isset($_POST['finishDate'])?strtotime($_POST["finishDate"]):'';
    $res = ['status'=>'ok'];
    $sql = "insert into movie(topic,actor,rank,pressDate,finishDate) values(:topic,:actor,:rank,:pressDate,:finishDate)";
    
    $stmt = $db->prepare($sql);
    
    $stmt->bindValue(':topic',$_POST['topic'],SQLITE3_TEXT);
    $stmt->bindValue(':actor',$_POST['actor'],SQLITE3_TEXT);
    $stmt->bindValue(":rank",$_POST['rank'],SQLITE3_INTEGER);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    $stmt->bindValue(':finishDate',$finishDate,SQLITE3_INTEGER);
    $ret=$stmt->execute();
    
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}

// 更新信息
function updateAv($db)
{
    $old_topic = $_POST['old_topic'];
    $sql = "UPDATE movie SET ";
    if(isset($_POST['topic']))
    {
        $sql.='topic="'.$_POST['topic'].'",';
    }
    if(isset($_POST['actor']))
    {
        $sql.='actor="'.$_POST['actor'].'",';
    }
    if(isset($_POST['rank']))
    {
        $sql.='rank='.$_POST['rank'].',';
    }
    if(isset($_POST['pressDate']))
    {
        $sql.='pressDate='.dt_to_unix($_POST['pressDate']).',';
    }
    if(isset($_POST['finishDate']))
    {
        $sql.=' finishDate='.dt_to_unix($_POST['finishDate']).',';
    }
    $sql = rtrim($sql,',');
    $sql.=' WHERE topic="'.$old_topic.'"';
    
    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}

// 显示列表
function listAv($db,$isTodo)
{
    $offset = $_POST['offset'];//起始页
    $num = $_POST['num'];//条数
    $sql = '';
    if($isTodo){
        $sql='SELECT topic,actor,rank,pressDate FROM movie where finishDate=0 ORDER BY pressDate asc limit '
    .$offset.','.$num;
    }
    else{
        $sql='SELECT topic,actor,rank,pressDate,finishDate FROM movie where finishDate!=0 ORDER BY finishDate desc limit '
    .$offset.','.$num;
    }
    $ret = $db->query($sql);
    $arr = ["status"=>"ok"];
    $arr['data'] = [];
    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        $pressDate = $row["pressDate"];
        $pressDate = date('Y-m-d',$pressDate);
        $row['pressDate'] = $pressDate;
        if(!$isTodo){
            $finishDate = $row["finishDate"];
            $finishDate = date('Y-m-d',$finishDate);
            $row['finishDate'] = $finishDate;
        }
        array_push($arr['data'],$row);
    }
    if(count($arr['data'])==0) $arr['status']='todos empty!';
    return json_encode($arr);
}

function findAv($db)
{
    $res=['status'=>'ok'];
    $sql='SELECT topic,actor,pressDate,finishDate,rank from movie where 1==1';

    $topic = isset($_POST['topic'])?$_POST['topic']:'';
    $actor = isset($_POST['actor'])?$_POST['actor']:'';
    $pressDate_start = isset($_POST['pressDate_start'])?dt_to_unix($_POST['pressDate_start']):'';
    $pressDate_end = isset($_POST['pressDate_end'])?dt_to_unix($_POST['pressDate_end']):'';
    $finishDate_start = isset($_POST['finishDate_start'])?dt_to_unix($_POST['finishDate_start']):'';
    $finishDate_end = isset($_POST['finishDate_end'])?dt_to_unix($_POST['finishDate_end']):'';

    if(!empty($topic))
    {
        $sql.=' AND topic like "%'.$topic.'%" COLLATE NOCASE';
    }
    if(!empty($actor))
    {
        $sql.=' AND actor like "%'.$actor.'%" COLLATE NOCASE';
    }
    if($pressDate_start!=0)
    {
        $sql.=' AND pressDate > '.$pressDate_start.' AND pressDate <='.$pressDate_end;
    }
    if($finishDate_start!=0)
    {
        $sql.=' AND finishDate > '.$finishDate_start.' AND finishDate <='.$finishDate_end;
    }
    $sql.=' order by pressDate desc;';

    $ret = $db->query($sql);
    $res['data']=[];
    while($row=$ret->fetchArray(SQLITE3_ASSOC))
    {
        $row['pressDate'] = unix_to_dt($row['pressDate']);
        $row['finishDate'] = unix_to_dt($row['finishDate']);
        array_push($res['data'],$row);
    }
    if(count($res['data'])==0) $res['status']='empty result.';
    return json_encode($res);
}

function finishAvTodo($db)
{
    $sql ='UPDATE movie set finishDate = '.strtotime(date('Y-m-d')).' where topic="'.$_POST['topic'].'"';
    $ret = $db->exec($sql);
    $res = ['status'=>'ok'];
    if(!$ret)
    {
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
    
}



function deleteAv($db)
{
    $sql = 'DELETE FROM movie WHERE topic';
    if($_POST['topic']==''){
        $sql.=' is null';
    }else{
        $sql.='="'.$_POST['topic'].'"';
    }

    $ret = $db->exec($sql);
    $res=['status'=>'ok'];
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}

function addActress()
{
    
}

function updateActress()
{
    
}

function listActress()
{
    
}

function addAvSerie($db)
{
    $today = strtotime(date('Y-m-d'));
    $res = ['status'=>'ok'];
    $sql = "insert into libs(topic,curDate,degree) values(:topic,:curDate,:degree)";

    $stmt = $db->prepare($sql);

    $stmt->bindValue(':topic',$_POST['topic'],SQLITE3_TEXT);
    $stmt->bindValue(":degree",0.0,SQLITE3_INTEGER);//默认为0
    $stmt->bindValue(':curDate',$today,SQLITE3_INTEGER);

    $ret=$stmt->execute();

    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}

// 获取未完成的系列
function getSerieNum($db)
{
    $itemNum = 16; // 每页展示条数
    $sql = '';
    // 默认是显示todo
    // 否则是显示全部
    if($_GET['isTodo']==1)//参数是字符串，需要转换
    {
        // 上次浏览日期小于当月1日
        $firstday = strtotime(date('Y-m-01'));
        $sql="SELECT COUNT(*) as ct FROM libs WHERE curDate <$firstday";

    }else{
        $sql.= 'SELECT COUNT(*) as ct FROM libs';
    }
    $ret = $db->query($sql);
    $total = 0;
    if($row=$ret->fetchArray(SQLITE3_ASSOC))
    {
        $total = $row['ct'];
    }

    $res=['status'=>'ok'];
    $res['total'] = $total;
    $res['itemNum'] = $itemNum;
    return json_encode($res);
}

function updateAvSerie($db)
{
    $topic = $_POST['topic'];
    $old_topic = $_POST['old_topic'];
    $sql = "UPDATE libs SET topic=\"$topic\" WHERE topic=\"$old_topic\"";

    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}

function finishAvSerieTodo($db)
{
    $topic = $_POST['topic'];
    $today = strtotime(date('Y-m-d'));
    $sql = "UPDATE libs SET curDate=$today WHERE topic=\"$topic\"";

    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}

function listAvSerie($db,$isTodo)
{
    $offset = $_POST['offset'];//起始页
    $num = $_POST['num'];//条数
    $sql = '';
    if($isTodo){
        $firstday = strtotime(date('Y-m-01'));
        $sql="SELECT topic FROM libs where curDate<$firstday ORDER BY degree desc limit $offset,$num";
    }
    else{
        $sql="SELECT topic FROM libs ORDER BY degree desc limit $offset,$num";
    }
    $ret = $db->query($sql);
    $arr = ["status"=>"ok"];
    $arr['data'] = [];
    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        array_push($arr['data'],$row);
    }
    if(count($arr['data'])==0) $arr['status']='todos empty!';
    return json_encode($arr);
}

function deleteAvSerie($db)
{
    $topic = $_POST['topic'];

    $sql = "DELETE FROM libs WHERE topic=\"$topic\"";

    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}
/**
 *  电影类操作
 */
 
function addMv($db)
{
    $pressDate = isset($_POST['pressDate'])?strtotime($_POST["pressDate"]):'';
    $finishDate = isset($_POST['finishDate'])?strtotime($_POST['finishDate']):'';
    $res = ['status'=>'ok'];
    $sql = "insert into libmov(chn_name,eng_name,pressDate,finishDate,rank,comment) values(:chn_name,:eng_name,:pressDate,:finishDate,:rank,:comment)";
    
    $stmt = $db->prepare($sql);

    $chn_name = isset($_POST['chn_name'])?$_POST['chn_name']:'';
    $eng_name = isset($_POST['eng_name'])?$_POST['eng_name']:'';
    $rank = isset($_POST['rank'])?$_POST['rank']:'';
    $comment = isset($_POST['comment'])?$_POST['comment']:'';
    
    $stmt->bindValue(':chn_name',$chn_name,SQLITE3_TEXT);
    $stmt->bindValue(':eng_name',$eng_name,SQLITE3_TEXT);
    $stmt->bindValue(":rank",$rank,SQLITE3_INTEGER);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    $stmt->bindValue(':finishDate',$finishDate,SQLITE3_INTEGER);
    $stmt->bindValue(':comment',$comment,SQLITE3_TEXT);
    $ret=$stmt->execute();
    
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}


// 显示Mv列表
function listMv($db,$isTodo)
{
    $offset = $_POST['offset'];//起始页
    $num = $_POST['num'];//条数
    $sql='';
    if($isTodo){
        $sql='SELECT chn_name,eng_name,rank,pressDate FROM libmov where finishDate==0 ORDER BY pressDate asc,rank desc limit '
    .$offset.','.$num;
    }else{
        $sql='SELECT chn_name,eng_name,pressDate,finishDate,rank,comment FROM libmov where finishDate!=0 ORDER BY finishDate desc limit '
    .$offset.','.$num;
    }
    $ret = $db->query($sql);
    $arr = ["status"=>"ok"];
    $arr['data'] = [];
    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        $pressDate = $row["pressDate"];
        $pressDate = date('Y-m-d',$pressDate);
        $row['pressDate'] = $pressDate;
        if(!$isTodo){
            $finishDate = $row["finishDate"];
            $finishDate = date('Y-m-d',$finishDate);
            $row['finishDate'] = $finishDate;
        }
        array_push($arr['data'],$row);
    }
    if(count($arr['data'])==0) $arr['status']='todos empty!';
    return json_encode($arr);
}

function finishMvTodo($db)
{
    $sql ='UPDATE libmov set finishDate = '.strtotime(date('Y-m-d')).' where chn_name="'.$_POST['chn_name'].'"';
    $ret = $db->exec($sql);
    $res = ['status'=>'ok'];
    if(!$ret)
    {
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}

function findMv($db)
{
    $res=['status'=>'ok'];
    $sql='SELECT chn_name,eng_name,pressDate,finishDate,rank from libmov where 1==1';

    $chn_name = isset($_POST['chn_name'])?$_POST['chn_name']:'';
    $eng_name = isset($_POST['eng_name'])?$_POST['eng_name']:'';
    $pressDate_start = isset($_POST['pressDate_start'])?dt_to_unix($_POST['pressDate_start']):'';
    $pressDate_end = isset($_POST['pressDate_end'])?dt_to_unix($_POST['pressDate_end']):'';
    $finishDate_start = isset($_POST['finishDate_start'])?dt_to_unix($_POST['finishDate_start']):'';
    $finishDate_end = isset($_POST['finishDate_end'])?dt_to_unix($_POST['finishDate_end']):'';

    if(!empty($chn_name))
    {
        $sql.=' AND chn_name like "%'.$chn_name.'%" COLLATE NOCASE';
    }
    if(!empty($eng_name))
    {
        $sql.=' AND eng_name like "%'.$eng_name.'%" COLLATE NOCASE';
    }
    if($pressDate_start!=0)
    {
        $sql.=' AND pressDate > '.$pressDate_start.' AND pressDate <='.$pressDate_end;
    }
    if($finishDate_start!=0)
    {
        $sql.=' AND finishDate > '.$finishDate_start.' AND finishDate <='.$finishDate_end;
    }
    $sql.=' order by pressDate desc;';

    $ret = $db->query($sql);
    $res['data']=[];
    while($row=$ret->fetchArray(SQLITE3_ASSOC))
    {
        $row['pressDate'] = unix_to_dt($row['pressDate']);
        $row['finishDate'] = unix_to_dt($row['finishDate']);
        array_push($res['data'],$row);
    }
    if(count($res['data'])==0) $res['status']='empty result.';
    return json_encode($res);
}

function updateMv($db)
{
    $old_chn_name = replace_quote($_POST['old_chn_name']);

    $sql = "UPDATE libmov SET ";
    // 解决名称有引号问题
    if(isset($_POST['chn_name']))
    {
        $chn_name = replace_quote($_POST['chn_name']);
        $sql.= <<< HTML
        chn_name = "$chn_name",
HTML;
    }
    if(isset($_POST['eng_name']))
    {
        $sql.='eng_name="'.$_POST['eng_name'].'",';
    }
    if(isset($_POST['pressDate']))
    {
        $sql.='pressDate='.dt_to_unix($_POST['pressDate']).',';
    }
    if(isset($_POST['finishDate']))
    {
        $sql.=' finishDate='.dt_to_unix($_POST['finishDate']).',';
    }
    if(isset($_POST['rank']))
    {
        $sql.='rank='.$_POST['rank'].',';
    }
    if(isset($_POST['comment']))
    {
        $comment = replace_quote($_POST['comment']);
        $sql.= <<< HTML
        comment = "$comment",
HTML;
    }
    $sql = rtrim($sql,',');
    $sql.=' ';
    $sql.= <<< HTML
    WHERE chn_name = "$old_chn_name"
HTML;
    //echo 'sql:'.$sql;
    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}

function deleteMv($db)
{
    $sql = 'DELETE FROM libmov WHERE chn_name="'.replace_quote($_POST['chn_name']).'"';
    $ret = $db->exec($sql);
    $res=['status'=>'ok'];
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}

/**
 * 备忘记事相关
 */
function listTodo($db,$isTodo)
{
    $offset = $_POST['offset'];//起始页
    $num = $_POST['num'];//条数
    $sql = '';
    if($isTodo){
        $sql.='SELECT tid,topic,create_at,plan_at FROM todos WHERE finish_at is null ORDER BY plan_at asc LIMIT';
    }else{
        $sql.='SELECT tid,topic,finish_at,plan_at FROM todos WHERE finish_at is not null ORDER BY finish_at desc,plan_at desc LIMIT';
    }
    $sql.=' '.$offset.','.$num;
    $ret = $db->query($sql);
    $arr = ["status"=>"ok"];
    $arr['data'] = [];
    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
        array_push($arr['data'],$row);
    }
    if(count($arr['data'])==0) $arr['status']='todos empty!';
    return json_encode($arr);
}

function finishTodo($db)
{
    $tid = $_POST['tid'];
    $dt = new DateTime('now');

    $sql = 'UPDATE todos SET finish_at="'.$dt->format('Y-m-d H;i').'" WHERE tid='.$tid;
    $ret = $db->exec($sql);
    $res=['status'=>'ok'];
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}

function addTodo($db)
{

    // 拿到上次的tid
    $sql = 'SELECT MAX(tid) as mtid FROM todos';
    $ret = $db->query($sql);
    $res=[];
    $res['status'] = 'ok';
    $tid = 0;
    if($row=$ret->fetchArray(SQLITE3_ASSOC))
    {
        $tid = $row['mtid'] +1;

    }
    $now = new DateTime('now');
    $sql = 'INSERT INTO todos(tid,topic,create_at,plan_at) VALUES(:tid,:topic,:create_at,:plan_at)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':tid',$tid,SQLITE3_INTEGER);
    $stmt->bindValue(':topic',$_POST['topic'],SQLITE3_TEXT);
    $stmt->bindValue(':create_at',$now->format('Y-m-d H;i'),SQLITE3_TEXT);
    $stmt->bindValue(':plan_at',$_POST['plan_at'],SQLITE3_TEXT);
    $ret = $stmt->execute();
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    $res['tid']=$tid;
    $res['create_at'] = $now->format('Y-m-d H;i');
    return json_encode($res);
}

function updateTodo($db)
{
    $tid = $_POST['old_tid'];
    $sql='UPDATE todos SET';

    if(isset($_POST['topic'])){
        $sql.=' topic="'.$_POST['topic'].'",';
    }
    if(isset($_POST['create_at'])){
        $sql.=' create_at="'.$_POST['create_at'].'",';
    }
    if(isset($_POST['plan_at'])){
        $sql.=' plan_at="'.$_POST['plan_at'].'",';
    }
    if(isset($_POST['finish_at'])){
        $sql.=' finish_at="'.$_POST['finish_at'].'",';
    }
    $sql = rtrim($sql,',');
    $sql.=' WHERE tid='.$tid;
    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}