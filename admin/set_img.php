<?php
require_once("../core/connect_db.inc");


$quiz_id = $_POST["quiz_id"];

$select[0] = $_POST["select1"];
$select[1] = $_POST["select2"];
$select[2] = $_POST["select3"];
$select[3] = $_POST["select4"];
    
$explain[0] = $_POST["explain1"];
$explain[1] = $_POST["explain2"];
$explain[2] = $_POST["explain3"];
$explain[3] = $_POST["explain4"];

    try{
        
        $db = getDB();

        $stt = $db->prepare('DELETE FROM ta_quiz_img WHERE quiz_id=:quiz_id;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->execute();
        

        for($i=0; $i<4; $i++){
                $stt = $db->prepare('INSERT INTO ta_quiz_img(quiz_id, num, img_name, exp) VALUES (:quiz_id, :num, :img_name, :exp);');
                $stt->bindValue(':quiz_id', $quiz_id);
                $stt->bindValue(':num', strval($i+1));
                $stt->bindValue(':img_name', $select[$i]);
                $stt->bindValue(':exp', $explain[$i]);
                $stt->execute();
        }


        $array = array("result" => 1);
        //printf(json_encode($array));
        printf('更新完了！<br><br><a href="./quiz_img.php">＜＜戻る</a>');
        
    }catch(PDOException $e){
        $array = array("result" => 0);
        //printf(json_encode($array));
        
        die("接続エラー:".$e);
    }
    
    
?>
