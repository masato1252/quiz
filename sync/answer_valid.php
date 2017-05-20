<?php

require_once("../core/connect_db.inc");

$quiz_id = $_GET["quiz_id"];
$type = $_GET["type"];

try{
        $db = getDB();
        
        if($type==1){
            $stt = $db->prepare('SELECT answer FROM ta_quiz_data WHERE quiz_id=:quiz_id;');
            $stt->bindValue(':quiz_id', $quiz_id);
            $stt->execute();
            $row = $stt->fetch();
            if($row["answer"]==0 || $row["answer"]==""){
                //未入力
                $array = array("valid"=>0);
            }else{
                //入力済み
                $array = array("valid"=>0);
            }
            printf(json_encode($array));
            
        }else if($type==2){
            $stt = $db->prepare('SELECT answer FROM ta_quiz_data WHERE quiz_id=:quiz_id;');
            $stt->bindValue(':quiz_id', $quiz_id);
            $stt->execute();
            $row = $stt->fetch();
            
            $array = array("answer"=>$row["answer"]);
            printf(json_encode($array));
            
        }

     
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
