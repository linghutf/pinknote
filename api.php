<?php

if($_GET['type']==='search'))
{
    //button无法收到post
    # 查询符合条件的主题
    $sql='SELECT topic,actor,pressDate,finishDate,rank from movie where 1==1';
    obj = json_decode($_POST['data']);
    
    $topic = obj['topic'];
    $actor = obj['actor'];
    $pressDate_start = dt_to_unix(obj['pressDate_start']);
    $pressDate_end = dt_to_unix(obj['pressDate_end']);
    $finishDate_start = dt_to_unix(obj['finishDate_start']);
    $finishDate_end = dt_to_unix(obj['finishDate_end']);
    // 判断区间是否合法
    if($pressDate_start>$pressDate_end || $finishDate_start>$finishDate_end)
    {
        exit;
    }

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

    $res = $db->query($sql);
    $first=true;
    while($row=$res->fetchArray(SQLITE3_ASSOC))
    {
    }