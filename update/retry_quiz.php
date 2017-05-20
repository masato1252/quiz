<?php
    
    require_once("../core/connect_db.inc");
    require_once("../core/setting.php");
    
    $quiz_id = $_GET['quiz_id'];
    if($quiz_id == 0){
        printf("Invalid Access!");
        exit();
    }

    try{
        $db = getDB();
        
        $db->beginTransaction();
        
        $stt = $db->prepare('DELETE FROM ta_condition WHERE quiz=:quiz;');
        $stt->bindValue(':quiz', $quiz_id);
        $stt->execute();
        
        $stt = $db->prepare('DELETE FROM ta_tap WHERE quiz_id=:quiz_id;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->execute();
        
        $stt = $db->prepare('INSERT INTO ta_condition(state, quiz, date) VALUES (:state, :quiz, now());');
        $stt->bindValue(':state', 0);
        $stt->bindValue(':quiz', $quiz_id);
        $stt->execute();
        
        $db->commit();
        
        $array = array("result" => 1);
        printf(json_encode($array));

    }catch(PDOException $e){
        $db->rollback();
        die("接続エラー:".$e);
    }

?>
