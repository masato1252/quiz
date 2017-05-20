<?php
require_once('../../core/admin_auth.inc');
require_once("../../core/connect_db.inc");

$disp_num = 9;  //表示件数
$line = 3;  //列

$dir = "../../uploaded/";

//pages
if(empty($_GET['page'])){
    $page = 1;
    $query= "SELECT * FROM img_table ORDER BY regist_date DESC LIMIT ".$disp_num;
}else{
    $page = $_GET['page'];
    $offset = ($_GET['page']-1) * $disp_num;
    $query= "SELECT * FROM img_table ORDER BY regist_date DESC LIMIT ".$disp_num." OFFSET ".$offset;
}

try{
    $db = getDB();

    //全件数取得
    $stt = $db->prepare("SELECT img_id FROM img_table");
    $stt->execute();
    $all_count = $stt->rowCount();

    $stt = $db->prepare($query);
    $stt->execute();
}catch(PDOException $e){
    die("接続エラー:".$e);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/XHTML1/DTD/xhtml1-transitional.dtd">
<html xmls="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../admin_style.css" />
<script type="text/javascript">
<!--
function disp(img_id){
    if(window.confirm('本当に削除しますか？')){
        location.href = "./edit_imgup.php?img_id="+img_id+"&mode=-1";
    }
    else{
    }
}
// -->
</script>
</head>
<body>
<div id="wrapper">
<h1>画像マネージャー</h1>

<a href=# onClick="window.close(); return false;">閉じる</a>
<br><br>
<a href=./form_imgup.php>新しくアップロード</a><br><br>
<?php
    dispNavi($all_count, $page, $disp_num);
?>
<table border=1 cellpadding=5>
<tr>
  <?php
    $c=0;
    while($row = $stt->fetch()){
        $c++;
        print("<td>[[IMG=".$row['img_name']." WIDTH=MAX]]<br><img src='".$dir.$row['file_name']."' width='150' height='150'></td>");
        if($c % $line==0){
            print("</tr><tr>");
        }
  ?>
  <?php
    }
  ?>
</tr>
</table>
<?php
    dispNavi($all_count, $page, $disp_num);
?>
<br><br><a href=# onClick="window.close(); return false;">閉じる</a>
<div id="fotter">
    <address>&copy; Copyright 2015 BigFaceWorks</address>

<!--fotter--></div>
<!--wrapper--></div>
</body>
</html>

<?php

function dispNavi($all_count, $page, $disp_num){
    $navi="";
    //$all_count=31;
    for($i=1; $i<=(ceil($all_count/$disp_num)); $i++){
        if($page==$i){
            $nav=$nav.$i."／";
        }else{
            $nav=$nav."<a href=./show_window.php?page=".$i.">".$i."</a>／";
        }

    }
    if($page==($i-1)){
        print("全".$all_count."件中 ".((($page-1)*$disp_num)+1)."〜".$all_count."件目を表示中<br>");
    }else{
        print("全".$all_count."件中 ".((($page-1)*$disp_num)+1)."〜".$page*$disp_num."件目を表示中<br>");
    }
    print(mb_substr($nav,0,-3));
}
?>