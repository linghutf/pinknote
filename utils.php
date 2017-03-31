<?php
// date转unix时间戳
function dt_to_unix($dt)
{
    $ret=0;
    if($dt!=''){
        $ret=strtotime($dt);
    }
    return $ret;
}
//时间戳转date
function unix_to_dt($unix)
{
    $t="";
    if($unix!=0){
        $t=date('Y-m-d',$unix);
    }
    return $t;
}

// 获取当月第一天
function firstday_from_dt_to_dt($date)
{
    return date('Y-m-01',strtotime($date));
}

function lastday_from_dt_to_dt($date)
{
    $firstday = firstday_from_dt_to_dt($date);
    return date('Y-m-d',strtotime("$firstday +1 month -1 day"));
}

// unix版本
function firstday_from_unix_to_dt($t)
{
    return date('Y-m-01',$t);
}

function lastday_from_unix_to_dt($t)
{
    $date=firstday_from_unix_to_dt($t);
    return lastday_from_dt_to_dt($date);
}

function is_between_cur_mon_from_dt($date)
{
    $t=strtotime($date);
    $cur=date('Y-m-d');
    return $t>=strtotime(firstday_from_dt_to_dt($cur)) and $t<=strtotime(lastday_from_dt_to_dt($cur));
}

function is_between_cur_mon_from_unix($t)
{
    return ($t>=$_COOKIE['firstday']) and ($t<=$_COOKIE['lastday']);
}

?>