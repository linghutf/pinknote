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

function mod_path($path)
{
    $path = str_replace('\\','/',$path);
    if(substr($path,-1)!='/')
    {
        $path = $path.'/';
    }
    return $path;
}

function dir_list($path,$exts='',$list=array())
{
    $path=mod_path($path);
    $files=glob($path.'*');
    foreach($files as $v)
    {
        if(!$exts || preg_match("/\.($exts)/i",$v))
        {
            $list[]=$v;
            if(is_dir($v))
            {
                $list = dir_list($v,$exts,$list);
            }
        }
    }
    return $list;
}

function array_remove($data, $key){
    if(!array_key_exists($key, $data)){
        return $data;
    }
    $keys = array_keys($data);
    $index = array_search($key, $keys);
    if($index !== FALSE){
        array_splice($data, $index, 1);
    }
    return $data;

}

// UTF-8 字符串转成数组
function mbstr_to_arr($tempaddtext)
{
    $cind = 0;
    $arr_cont = array();
    for ($i = 0; $i < strlen($tempaddtext); $i++) {
            if (strlen(substr($tempaddtext, $cind, 1)) > 0) {
                if (ord(substr($tempaddtext, $cind, 1)) < 192) { //如果为英文则取1个字节
                    if (substr($tempaddtext, $cind, 1) != " ") {
                        array_push($arr_cont, substr($tempaddtext, $cind, 1));
                    }
                    $cind++;
                } elseif(ord(substr($tempaddtext, $cind, 1)) < 224) {
                    array_push($arr_cont, substr($tempaddtext, $cind, 2));
                    $cind+=2;
                } else {
                    array_push($arr_cont, substr($tempaddtext, $cind, 3));
                    $cind+=3;
                }
            }
        }
    return $arr_cont;
}
// 在文中字符串',"上加上转义\
// 由于字符串是由双引号括起来的，只针对双引号转义 --- 不起作用
// 双引号全部换成单引号
function replace_quote($str)
{
    $arr = mbstr_to_arr($str);
    $result = '';
    for($i=0;$i<count($arr);++$i){
        /*if($arr[$i]=='\''){//转义，一个字符
            $result.="\'";//不会转义，两个字符
        }else*/ if($arr[$i]=="\""){//转义
            $result.= '\'';//不转义
        }else{
            $result.=$arr[$i];
        }
    }
    return $result;
}
?>