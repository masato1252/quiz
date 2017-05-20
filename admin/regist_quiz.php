<html>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="main" class="ui-content">
<?php
require_once("../core/connect_db.inc");

//$quiz_id = $_POST["quiz_id"];
$quiz_title = $_POST["quiz_title"];
$num = $_POST["num"];
$quiz_type = $_POST["quiz_type"];
$img_type = $_POST["img_type"];
$question = $_POST["question"];
$sel1 = $_POST["select1"];
$sel2 = $_POST["select2"];
$sel3 = $_POST["select3"];
$sel4 = $_POST["select4"];
$answer = $_POST["answer"];
$limit_time = $_POST["limit_time"];
$goods = $_POST["goods"];

    try{
        
        $db = getDB();
        
        $stt = $db->prepare('select * from ta_quiz_data where quiz_id=:quiz_id;');
        $stt->bindValue(':quiz_id', $num);
        $stt->execute();
        $count= $stt->rowCount();
        
        if($count>0){
            printf("指定した順番が他と重複しています。<br><br><a href='javascript:history.back();'>戻る</a>");
            exit();
        }
        
        $stt = $db->prepare('INSERT INTO ta_quiz_data(quiz_id, quiz_title, quiz_type, img_type, question, select1, select2,
        select3, select4, answer, limit_time, goods) VALUES
        (:quiz_id, :quiz_title, :quiz_type, :img_type, :question, :select1, :select2,
        :select3, :select4, :answer, :limit_time, :goods);');
        $stt->bindValue(':quiz_id', $num);
        $stt->bindValue(':quiz_title', $quiz_title);
        $stt->bindValue(':quiz_type', $quiz_type);
        $stt->bindValue(':img_type', $img_type);
        $stt->bindValue(':question', $question);
        $stt->bindValue(':select1', $sel1);
        $stt->bindValue(':select2', $sel2);
        $stt->bindValue(':select3', $sel3);
        $stt->bindValue(':select4', $sel4);
        $stt->bindValue(':answer', $answer);
        $stt->bindValue(':limit_time', $limit_time);
        $stt->bindValue(':goods', $goods);
        $stt->execute();

        $array = array("result" => 1);
        //printf(json_encode($array));
        
        printf("登録完了！<br><br><a href='./edit_quiz.php'>＜＜戻る</a>");
        
    }catch(PDOException $e){
        $array = array("result" => 0);
        //printf(json_encode($array));
        
        die("接続エラー:".$e);
    }
    
   
    
?>
</div>
</div>
</body>
</html>
