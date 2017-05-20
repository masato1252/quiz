<?php

require_once("../core/connect_db.inc");


    try{
        $db = getDB();
        $stt = $db->prepare('select * from ta_quiz_data order by quiz_id asc;');
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

function delConfirm(quiz_id){
    
    if(window.confirm('本当に削除しますか？')){
        
        location.href = "./del_quiz.php?quiz_id=" + quiz_id;
        
    }else{

    }

    
}
// -->
</script>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
       <h1>問題編集</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./index.php">＜＜戻る</a><br><br>
        <a href="./regist_quiz_form.php">新規登録</a><br><br>
<?php
    while($row = $stt->fetch()){
        
        printf("順番：".$row["quiz_id"]." ");
        
        if($row["quiz_type"]==0){
            printf("例題　");
        }else if($row["quiz_type"]==1){
            printf("早押し　");
        }else if($row["quiz_type"]==2){
            printf("ﾐﾆｹﾞｰﾑ　");
        }
        printf("<a href='./edit_quiz_form.php?quiz_id=".$row["quiz_id"]."'>".$row["quiz_title"]." ".mb_substr($row["question"], 0, 15)."...</a>");    
        printf("　　<a href='#' onClick='delConfirm(".$row["quiz_id"].");'>削除</a><br><br>");
    }
?>

    <a href="./index.php">＜＜戻る</a>
    </div>
</div>
</body>
</html>   
