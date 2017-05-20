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
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
       <h1>答えの設定</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./index.php">＜＜戻る</a><br><br>
<?php
    while($row = $stt->fetch()){
        
        printf("順番：".$row["quiz_id"]." ");
        
        if($row["answer"]==0){
            printf("<font color=red>答え未設定</font>　");
        }else{
            printf("設定済み　");
        }
        printf("<a href='./set_answer_form.php?quiz_id=".$row["quiz_id"]."'>".$row["quiz_title"]." ".mb_substr($row["question"], 0, 15)."...</a>");    
        printf("<br><br>");
    }
?>

    <a href="./index.php">＜＜戻る</a>
    </div>
</div>
</body>
</html>
