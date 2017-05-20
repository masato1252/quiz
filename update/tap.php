<?php

require_once("../core/connect_db.inc");

$user_id = $_POST["user_id"];
$quiz_id = $_POST["quiz_id"];
$answer = $_POST["answer"];

if($answer>0){

try{
        $db = getDB();
        
        $stt = $db->prepare('SELECT * FROM ta_condition ORDER BY date DESC;');
        $stt->execute();
        $row = $stt->fetch();
        
        if($row["state"]!=1){
            $array = array("result"=>-1);
            printf(json_encode($array));
        }else{

            $stt = $db->prepare('INSERT INTO ta_tap(user_id, quiz_id, answer, date) VALUES (:user_id, :quiz_id, :answer, now(6));');
            $stt->bindValue(':user_id', $user_id);
            $stt->bindValue(':quiz_id', $quiz_id);
            $stt->bindValue(':answer', $answer);
            $stt->execute();
            
            $array = array("result"=>1);
            printf(json_encode($array));
        }
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

}
    
?>
