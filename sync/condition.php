<?php

require_once("../core/connect_db.inc");

$user_id = $_GET["user_id"];

try{
        $db = getDB();

        $stt = $db->prepare('SELECT state, quiz, date FROM ta_condition ORDER BY date DESC LIMIT 1;');
        $stt->execute();
        $row = $stt->fetch();
        
        if($row["state"]==1 || $row["state"]==2){
            //回答受け付け中 or　回答終了
            
            $stt2 = $db->prepare('SELECT quiz_title, question, select1, select2, select3, select4, now() AS now_date, limit_time FROM ta_quiz_data WHERE quiz_id=:quiz_id LIMIT 1;');
            $stt2->bindValue(':quiz_id', $row['quiz']);
            $stt2->execute();
            $row2 = $stt2->fetch();
            
            $stt3 = $db->prepare('SELECT user_id FROM ta_tap WHERE quiz_id=:quiz_id AND user_id=:user_id;');
            $stt3->bindValue(':quiz_id', $row['quiz']);
            $stt3->bindValue(':user_id', $user_id);
            $stt3->execute();
            $check = $stt3->rowCount();
            
            if($check!=0 && $row["state"]==2){
                $array = array("state"=>200, "quiz"=>$row["quiz"], "date"=>$row["date"], "now_date"=>$row2["now_date"],
                               "limit_time"=>$row2["limit_time"], "question"=>$row2["question"], "quiz_title"=>$row2["quiz_title"], "select1"=>$row2["select1"],
                               "select2"=>$row2["select2"], "select3"=>$row2["select3"], "select4"=>$row2["select4"]);
            
            }else if($check!=0 && $row["state"]==1){
                $array = array("state"=>100, "quiz"=>$row["quiz"], "date"=>$row["date"], "now_date"=>$row2["now_date"],
                               "limit_time"=>$row2["limit_time"], "question"=>$row2["question"], "quiz_title"=>$row2["quiz_title"], "select1"=>$row2["select1"],
                               "select2"=>$row2["select2"], "select3"=>$row2["select3"], "select4"=>$row2["select4"]);
            }else{
                $array = array("state"=>$row["state"], "quiz"=>$row["quiz"], "date"=>$row["date"], "now_date"=>$row2["now_date"],
                               "limit_time"=>$row2["limit_time"], "question"=>$row2["question"], "quiz_title"=>$row2["quiz_title"], "select1"=>$row2["select1"],
                               "select2"=>$row2["select2"], "select3"=>$row2["select3"], "select4"=>$row2["select4"]);
            }
            
        }else{
            //その他の状態
            $array = array("state"=>$row["state"], "quiz"=>$row["quiz"], "date"=>$row["date"]);
            
        }
        
        printf(json_encode($array));
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
