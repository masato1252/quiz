<?php

require_once("../core/connect_db.inc");

$state = $_POST["condition"];
$quiz = $_POST["quiz_id"];
$date = $_POST["date"];

try{
        $db = getDB();
    
        $stt = $db->prepare('INSERT INTO ta_condition(state, quiz, date6, date) VALUES (:state, :quiz, now(6), :date);');
        $stt->bindValue(':state', $state);
        $stt->bindValue(':quiz', $quiz);
        $stt->bindValue(':date', $date);
        $stt->execute();
    
        $array = array("date" => $date);
        printf(json_encode($array));
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
