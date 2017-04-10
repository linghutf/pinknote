<html>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="js/jquery.min.js" type="text/javascript"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <title>电影热点记录</title>
        <style>
        body{
            padding-top:70px;
        }
        </style>
    </head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
    <div>
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    观影<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add_mov.php">添加</a></li>
                    <li><a href="list_mov_todo.php">提醒</a></li>
                    <li class="divider"></li>
                    <li><a href="search_mov.php">修改</a></li>
                    <li><a href="list_mov_his.php?page_id=0">历史</a></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    娱乐<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="add_movie.php">添加</a></li>
                    <li><a href="list_todo.php">任务</a></li>
                    <li class="divider"></li>
                    <li><a href="search.php">修改</a></li>
                    <li><a href="list_history.php?page_id=0">历史</a></li>
                    <li class="divider"></li>
                    <li><a href="libav.php">主题</a></li>
                    <li><a href="libact.php">艺人</a></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    文件共享<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="up_file.php">上传</a></li>
                    <li><a href="download_file.php">下载</a></li>
                    <!--li class="divider"></li>
                    <li><a href="list_file.php">浏览</a></li-->

                </ul>
            </li>
        </ul>
    </div>
    </div>
</nav>
<div class="container">
