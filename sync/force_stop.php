<?php

require_once("../core/connect_db.inc");

$user_id = $_GET["user_id"];
$quiz = $_GET["quiz_id"];

try{
        $db = getDB();

        $stt = $db->prepare('SELECT state, quiz FROM ta_condition ORDER BY date DESC LIMIT 1;');
        $stt->execute();
        $row = $stt->fetch();
        
        if($row["state"]==1){
            //回答受け付け中
            $array = array("state"=>$row["state"]); 
        }else if($row["state"]==2 || $row["state"]==3){
            //回答終了
            $array = array("state"=>$row["state"]);   
        }else if($row["state"]==0 && $row["quiz"]==$quiz){
            //仕切りなおし   
            $array = array("state"=>$row["state"]);
            
            //回答取り消し
            $stt2 = $db->prepare('DELETE FROM ta_tap WHERE user_id=:user_id AND quiz_id=:quiz_id');
            $stt2->bindValue(':user_id', $user_id);
            $stt2->bindValue(':quiz_id', $quiz);
            $stt2->execute();
        
        }else{
            $array = array("state"=>-1); 
        }
            
         
        printf(json_encode($array));
        
    }catch(PDOException $e){
        //die("接続エラー:".$e);
        $array = array("state"=>1); 
        printf(json_encode($array));
    }

?>
