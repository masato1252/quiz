<?php

require_once("../core/connect_db.inc");


function convSec($a){
    $arr = explode(".", $a);
    $micro = intval($arr[1])/1000000;
    $arr2 = explode(":",$arr[0]);
    //var_dump($arr2);
    $sec = intval($arr2[0])*3600 + intval($arr2[1])*60 + $arr2[2];
    
    $time = $sec + $micro;
    return $time;
}

function hms($sec) {
    $ss = $sec % 60;
    $mm = (int)($sec / 60) % 60;
    $hh = (int)($sec / (60 * 60));
    return array($hh, $mm, $ss);
}

try{
        $db = getDB();
        
        
        //正解取得
        $stt = $db->prepare('SELECT quiz_id,answer,quiz_type FROM ta_quiz_data ORDER BY quiz_id ASC;');
        $stt->execute();
        while($row = $stt->fetch()){
            if($row["quiz_type"]==0){
                //例題は正解数に含めない
                $answer[$row["quiz_id"]] = -1;
            }else{
                $answer[$row["quiz_id"]] = $row["answer"];
            }
            $quiz_type[$row["quiz_id"]] = $row["quiz_type"];
            
            //回答開始時間取得
            $stt2 = $db->prepare('SELECT quiz,date6 FROM ta_condition WHERE quiz=:quiz_id AND state=:state;');
            $stt2->bindValue(':quiz_id', $row["quiz_id"]);
            $stt2->bindValue(':state', 1);
            $stt2->execute();
            $tmp = $stt2->fetch();
            $start[$tmp["quiz"]] = $tmp["date6"];
            
        }
        
        
        
        //ユーザーごとに算出
        $stt = $db->prepare('SELECT id AS user_id FROM ta_user;');
        $stt->execute();
        while($row = $stt->fetch()){
            $num_answer = 0; //正解数
            $answered = 0;
            $time_valid = 0;
            $stt2 = $db->prepare('SELECT quiz_id,answer,user_id,date FROM ta_tap WHERE user_id=:user_id ORDER BY quiz_id ASC, date DESC;');
            $stt2->bindValue(':user_id', $row["user_id"]);
            $stt2->execute();
            $qid=0; $sum_diff=0;
            while($tap = $stt2->fetch()){
                if($tap["quiz_id"]==$qid){
                    //重複チェック
                    continue;
                }else{
                    $answered++;
                    $qid = $tap["quiz_id"];
                    
                    if($tap["answer"]==$answer[$tap["quiz_id"]]){
                        //正解
                        $num_answer++;
                        
                        //時間合算(例題とミニゲーム以外)
                        if($quiz_type[$tap["quiz_id"]]==1){
                            $time_valid = 1;
                            $stt3 = $db->prepare('SELECT TIMEDIFF(:date, :start) AS diff;');
                            $stt3->bindValue(':date', $tap["date"]);
                            $stt3->bindValue(':start', $start[$tap["quiz_id"]]);
                            $stt3->execute();
                            $d = $stt3->fetch();
                            
                            $diff = convSec($d["diff"]);
                            $sum_diff = $sum_diff + $diff;
                        }   
                        
                                               
                    }
                    
                    
                }

            }
            $str = strval($sum_diff);
            $arr = explode(".",$str);
            $hms = hms($arr[0]);
            $sum_time = $hms[0].":".$hms[1].":".$hms[2].".".$arr[1];
            
            
            //ユーザーごとに元ネタ作成
            $stt2 = $db->prepare('INSERT INTO ta_ranking(user_id, correct, sum_time, answered, time_valid) VALUES (:user_id, :correct, :sum_time, :answered, :time_valid);');
            $stt2->bindValue(':user_id', $row["user_id"]);
            $stt2->bindValue(':correct', $num_answer);
            $stt2->bindValue(':sum_time', $sum_time);
            $stt2->bindValue(':answered', $answered);
            $stt2->bindValue(':time_valid', $time_valid);
            $stt2->execute();
            
            
        }
        
     printf("OK!");
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
