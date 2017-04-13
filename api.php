<?php
header('Content-type: text/json;charset=utf-8');

require_once('connect.php');
require_once('utils.php');

// 业务逻辑
$type = $_GET['action'];
switch($type)
{
    case "getAvPageNum":echo getPageNum($db,'movie');break;
    case "getMvPageNum":echo getPageNum($db,'libmov');break;
    // 任务中心功能
    case "addAv":echo addAv($db);break;
    case "listAvTodo":echo listAv($db,$isTodo=true);break;
    case "finishAvTodo":echo finishAvTodo($db);break;
    // 未完成，已完成的共用更新方法
    case "updateAv":echo updateAv($db);break;
    case "listAvHistory":echo listAv($db,$isTodo=false);break;
    case "deleteAv":echo deleteAv($db);break;
    //case "listAvDetail":echo listAvDetail($db);break;
    
    case "addMv":echo addMv($db);break;
    case "listMvTodo":echo listMv($db,$isTodo=true);break;
    case "deleteMv":echo deleteMv($db);break;
    case "finishMvTodo":echo finishMvTodo($db);break;
    case "updateMv":echo updateMv($db);break;
    case "listMvHistory":echo listMv($db,$isTodo=false);break;
    
    // 展示影片详情,更新影评
    case "listMvDetail":echo listMvDetail($db);break;
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
    // 默认是显示todo
    if($_GET['isTodo']==1)//参数是字符串，需要转换
    {
        $sql.='==0';
    }else{
        $sql.='!=0';
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
    $finishDate = strtotime($_POST["finishDate"]);
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
    $sql = 'DELETE FROM movie WHERE topic="'.$_POST['topic'].'"';
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

function addSerie()
{
    
}

function updateSerie()
{
    
}
function listSerie()
{
    
}
/**
 *  电影类操作
 */
 
function addMv($db)
{
    $pressDate = strtotime($_POST["pressDate"]);
    $finishDate = strtotime($_POST['finishDate']);
    $res = ['status'=>'ok'];
    $sql = "insert into libmov(chn_name,eng_name,pressDate,finishDate,rank,comment) values(:chn_name,:eng_name,:rank,:pressDate,:finishDate,:rank,:comment)";
    
    $stmt = $db->prepare($sql);
    
    $stmt->bindValue(':chn_name',$_POST['chn_name'],SQLITE3_TEXT);
    $stmt->bindValue(':eng_name',$_POST['eng_name'],SQLITE3_TEXT);
    $stmt->bindValue(":rank",$_POST['rank'],SQLITE3_INTEGER);
    $stmt->bindValue(':pressDate',$pressDate,SQLITE3_INTEGER);
    $stmt->bindValue(':finishDate',$finishDate,SQLITE3_INTEGER);
    $stmt->bindValue(':comment',$_POST['comment'],SQLITE3_TEXT);
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

function updateMv($db)
{
    $old_chn_name = $_POST['old_chn_name'];
    $comment = $_POST['comment'];
    // 转义单引号，双引号
    $old_chn_name = replace_quote($old_chn_name);
    $comment = replace_quote($comment);
    
    $sql = "UPDATE libmov SET ";
    if(isset($_POST['chn_name']))
    {
        $sql.='chn_name="'.$_POST['chn_name'].'",';
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
        $sql.='comment="'.$_POST['comment'].'",';
    }
    $sql = rtrim($sql,',');
    $sql.=' WHERE chn_name="'.$old_chn_name.'"';
    
    $ret = $db->exec($sql);
    $res['status'] = 'ok';
    if(!$ret){
        $res['status'] = $db->lastErrorMsg();
    }
    return json_encode($res);
}

function deleteMv($db)
{
    $sql = 'DELETE FROM libmov WHERE chn_name="'.$_POST['chn_name'].'"';
    $ret = $db->exec($sql);
    $res=['status'=>'ok'];
    if(!$ret){
        $res['status']=$db->lastErrorMsg();
    }
    return json_encode($res);
}