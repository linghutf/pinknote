<?php include_once('header.php');?>
<table class="table">

<?php
require_once('utils.php');
$list = dir_list("uploads/");

echo <<< EOF

<thead><tr>
<td>文件名</td>
<td>大小</td>
</tr></thead>
<tbody>
EOF;
foreach($list as $v)
{
    echo <<< EOF
    <tr><td><a href="$_SERVER[PHP_SELF]}?filename=$v">basename($v)</a></td><td>filesize($v)</td></td>
EOF;
}
echo '/<tbody>';

if(isset($_GET['filename']))
{
    $filename = $_GET['filename'];
    header('content-disposition:attachment;filename='.basename($filename));
    header('content-length:'.filesize($filename));
    readfile($filename);
}
?>
</table>
<?php include_once('footer.php');?>
