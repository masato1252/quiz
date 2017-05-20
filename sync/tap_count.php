<?php

require_once("../core/connect_db.inc");

$quiz_id = $_POST["quiz_id"];


try{
        $db = getDB();

    for($i=1; $i<=4; $i++){
        $stt = $db->prepare('SELECT user_id FROM ta_tap WHERE quiz_id=:quiz_id AND answer=:answer;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->bindValue(':answer', $i);
        $stt->execute();
        $count[$i] = $stt->rowCount();
    }
    
    $array = array("1"=>$count[1], "2"=>$count[2], "3"=>$count[3], "4"=>$count[4]);
    
    printf(json_encode($array));
        
}catch(PDOException $e){
    die("接続エラー:".$e);
}


?>
