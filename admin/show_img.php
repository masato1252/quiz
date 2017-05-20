<?php

require_once("../core/connect_db.inc");

$disp_num = 16;  //表示件数
$line = 4;  //列

$dir = "../img/";

//pages
if(empty($_GET['page'])){
    $page = 1;
    $query= "SELECT * FROM ta_img_table ORDER BY regist_date DESC LIMIT ".$disp_num;
}else{
    $page = $_GET['page'];
    $offset = ($_GET['page']-1) * $disp_num;
    $query= "SELECT * FROM ta_img_table ORDER BY regist_date DESC LIMIT ".$disp_num." OFFSET ".$offset;
}

try{
    $db = getDB();

    //全件数取得
    $stt = $db->prepare("SELECT img_id FROM ta_img_table");
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
<?php
    require_once './admin_header.php';
?>
<script type="text/javascript">
<!--
function disp(img_id){
    if(window.confirm('本当に削除しますか？')){
        location.href = "./edit_imgup.php?img_id="+img_id+"&mode=-1";
    }
    else{
    }
}

$(document).bind("mobileinit", function(){
    $.mobile.ajaxLinksEnabled = false; // Ajax を使用したページ遷移を無効にする
    $.mobile.ajaxFormsEnabled = false; // Ajax を使用したフォーム遷移を無効にする
});
// -->
</script>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
        <h1>画像管理</h1>
    </div>
    <div data-role="main" class="ui-content">
<a href="./quiz_img.php">＜＜戻る</a>
<br><br>
<a href="./form_imgup.php" data-ajax:"false" rel:"external">新しくアップロード</a><br><br>
<?php
    dispNavi($all_count, $page, $disp_num);
?>
<table border=1 cellpadding=5>
<tr>
  <?php
    $c=0;
    while($row = $stt->fetch()){
        $c++;
        print("<td>画像ID:".$row['img_name']."<br><a href=# onclick='disp(".$row['img_id'].")'>削除</a><br><img src='".$dir.$row['file_name']."' width='200'></td>");
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


</div>
</div>
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
            $nav=$nav."<a href=./show_img.php?page=".$i.">".$i."</a>／";
        }

    }
    if($page==($i-1)){
        print("全".$all_count."件中 ".((($page-1)*$disp_num)+1)."〜".$all_count."件目を表示中<br>");
    }else{
        print("全".$all_count."件中 ".((($page-1)*$disp_num)+1)."〜".$page*$disp_num."件目を表示中<br>");
    }
    print(mb_substr($nav,0,-1));
}
?>
