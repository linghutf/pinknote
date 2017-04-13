<?php
// A simple web site in Cloud9 that runs through Apache
// Press the 'Run' button on the top to start the web server,
// then click the URL that is emitted to the Output tab of the console

include_once('header.php');

//setcookie('firstday',strtotime(date('Y-m-01')),time()+31536000,'/');
//$first=date('Y-m-01');
//setcookie('lastday',strtotime(date('Y-m-d',strtotime("$first +1 month -1 day"))),time()+31536000,'/');
//echo '<p>'.$_COOKIE['firstday'].',  '.$_COOKIE['lastday'].'</p>';

// TODO 艺人热度计算式:d=每部影片评分*总片数百分比
// TODO 查找艺人对应的收藏库电影
// SELECT topic,actor,pressDate,finishDate from movie left join actress where actor == name;
//
//
include_once('footer.php')
?>
