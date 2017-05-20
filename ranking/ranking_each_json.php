<?php

require_once("../core/connect_db.inc");

$quiz_id = $_GET["quiz_id"];

function convTime($str){
    $arr = explode(".",$str);
    $arr2 =explode(":",$arr[0]);
    return $arr2[2].".".substr($arr[1],0,3);
}


try{
        $db = getDB();
        
        //正解取得
        $stt = $db->prepare('SELECT answer,quiz_title FROM ta_quiz_data WHERE quiz_id=:quiz_id;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->execute();
        $row = $stt->fetch();
        $answer = $row["answer"];
        $quiz_title = $row["quiz_title"];
        
        //回答開始時間取得
        $stt = $db->prepare('SELECT date6 FROM ta_condition WHERE quiz=:quiz_id AND state=:state ORDER BY date DESC LIMIT 1;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->bindValue(':state', 1);
        $stt->execute();
        $row = $stt->fetch();
        $start = $row["date6"];

        $stt = $db->prepare('SELECT user.name AS name, dt.div_s AS div_name, TIMEDIFF(tap.date, :start) AS diff FROM ta_tap AS tap, ta_user AS user, ta_div_table AS dt WHERE tap.quiz_id=:quiz_id AND tap.user_id=user.id AND tap.answer=:answer AND user.div_num=dt.div_num ORDER BY diff ASC LIMIT 10;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->bindValue(':answer', $answer);
        $stt->bindValue(':start', $start);
        $stt->execute();

        $c=1;
        while($row = $stt->fetch()){
            $data[$c]["name"] = $row["name"];
            $data[$c]["div_name"] = $row["div_name"];
            $data[$c]["diff"] = convTime($row["diff"]);
            $c++;
        }
        
        printf(json_encode($data));
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
