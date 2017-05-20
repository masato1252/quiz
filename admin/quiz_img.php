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
       <h1>選択肢の画像管理</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./index.php">＜＜戻る</a><br><br>
        <a href="./show_img.php">画像管理</a><br><br>

<?php
    while($row = $stt->fetch()){
        
        if($row["img_type"]==0){
            continue;
        }else if($row["img_type"]==1){

        }
        
        if($row["quiz_type"]==0){
            printf("例題　");
        }else if($row["quiz_type"]==1){
            printf("早押し　");
        }else if($row["quiz_type"]==2){
            printf("ﾐﾆｹﾞｰﾑ　");
        }
        
        
        printf("<a href='./quiz_img_form.php?quiz_id=".$row["quiz_id"]."'>".$row["quiz_title"]." ".mb_substr($row["question"], 0, 15)."...</a><br><br>");    

    }
?>

    <a href="./index.php">＜＜戻る</a>
    </div>
</div>
</body>
</html>   
