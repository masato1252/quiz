<?php

require_once("../core/connect_db.inc");

$user_id = $_GET["user_id"];

try{
        $db = getDB();

        $stt = $db->prepare('SELECT * FROM ta_condition ORDER BY date DESC;');
        $stt->execute();
        $row = $stt->fetch();
        
        if($row["state"]==3){
            //回答受け付け中
            $stt2 = $db->prepare('SELECT answer FROM ta_tap WHERE quiz_id=:quiz_id AND user_id=:user_id;');
            $stt2->bindValue(':quiz_id', $row['quiz']);
            $stt2->bindValue(':user_id', $user_id);
            $stt2->execute();
            $check = $stt2->rowCount();
            $row2 = $stt2->fetch();
            
            
            if($check>0){
                $stt3 = $db->prepare('SELECT answer FROM ta_quiz_data WHERE quiz_id=:quiz_id;');
                $stt3->bindValue(':quiz_id', $row['quiz']);
                $stt3->execute();
                $row3 = $stt3->fetch();
                
                if($row2["answer"]==$row3["answer"]){
                    //正解
                    $array = array("state"=>1, "result"=>1);
                }else{
                    //不正解
                    $array = array("state"=>1, "result"=>0);
                }

            }else{
                $array = array("state"=>1, "result"=>0);
            }
            
        }else{
            //その他の状態
            $array = array("state"=>0, "result"=>0);
            
        }
        
        printf(json_encode($array));
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }
?>
