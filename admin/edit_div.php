<?php
    
    require_once("../core/connect_db.inc");
    
    
    try{
        $db = getDB();
        $stt = $db->prepare('select * from ta_div_table order by div_num asc;');
        $stt->execute();
        
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }
    
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
<?php
    require_once './admin_header.php';
?>

<script type="text/javascript">
<!--

function delConfirm(div_num){
    
    if(window.confirm('本当に削除しますか？')){
        
        location.href = "./del_div.php?div_num=" + div_num;
        
    }else{
        
    }
    
    
}
// -->
</script>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
<div data-role="header">
<h1>チーム名編集</h1>
</div>
<div data-role="main" class="ui-content">
<a href="./index.php">＜＜戻る</a><br><br>
<a href="./regist_div_form.php">新規登録</a><br><br>
<?php
    while($row = $stt->fetch()){
        
        printf("順番:".($row["div_num"]+1)."  ");
        printf("<a href='./edit_div_form.php?div_num=".$row["div_num"]."'>チーム名:".$row["div_name"]." ﾗﾝｷﾝｸﾞ表示名:".$row["div_s"]."</a>");
        printf("　　<a href='#' onClick='delConfirm(".$row["div_num"].");'>削除</a><br><br>");
    }
?>

<a href="./index.php">＜＜戻る</a>
</div>
</div>
</body>
</html>
