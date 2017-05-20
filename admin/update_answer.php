<html>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="main" class="ui-content">
<?php
require_once("../core/connect_db.inc");

$quiz_id = $_POST["quiz_id"];
$answer = $_POST["answer"];


    try{
        
        $db = getDB();

        $stt = $db->prepare('UPDATE ta_quiz_data SET answer=:answer WHERE quiz_id=:quiz_id;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->bindValue(':answer', $answer);
        $stt->execute();

        
        printf("登録完了！<br><br>答え：".$answer." で設定しました。<br><br><a href='./set_answer.php'>＜＜戻る</a>");
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }
    
    
?>
</div>
</div>
</body>
</html>
