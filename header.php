<html>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="js/jquery.min.js" type="text/javascript"></script>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!--自己的js-->
        <script src="js/page.js" type="text/javascript"></script>
        <script src="js/utils.js" type="text/javascript"></script>
        <title><?php print $title ?></title>
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
                    电影<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <!--li><a href="add_mov.php">添加</a></li-->
                    <li><a href="mvtodo.php">TODO</a></li>
                    <li class="divider"></li>
                    <li><a href="mvfind.php">查询</a></li>
                    <li class="divider"></li>
                    <li><a href="mvhist.php">历史</a></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    娱乐<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <!--li><a href="add_movie.php">添加</a></li-->
                    <li><a href="avtodo.php">TODO</a></li>
                    <li class="divider"></li>
                    <li><a href="avfind.php">检索</a></li>
                    <li class="divider"></li>
                    <li><a href="avhist.php">历史</a></li>
                    <li class="divider"></li>
                    <li><a href="series.php">主题</a></li>
                    <li class="divider"></li>
                    <li><a href="actress.php">艺人</a></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    记事<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="todos.php">TODO</a></li>
                    <li class="divider"></li>
                    <li><a href="todohist.php">历史</a></li>

                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    文件共享<b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="fileup.php">上传</a></li>
                    <li class="divider"></li>
                    <li><a href="filedown.php">下载</a></li>
                </ul>
            </li>
        </ul>
    </div>
    </div>
</nav>
<div class="container">
