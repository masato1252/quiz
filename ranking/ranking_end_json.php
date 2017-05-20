<?php

require_once("../core/connect_db.inc");


function convTime($str){
    $arr = explode(".",$str);
    $arr2 =explode(":",$arr[0]);
    return $arr2[1].":".$arr2[2].".".substr($arr[1],0,3);
}


try{
        $db = getDB();
  
        //正解取得
        $stt = $db->prepare('SELECT * FROM ta_ranking AS qr, ta_user AS qu, ta_div_table AS dt
                            WHERE qu.id=qr.user_id AND dt.div_num=qu.div_num AND answered > 0 ORDER BY qr.correct DESC, qr.time_valid DESC, qr.sum_time ASC;');
        $stt->execute();
        $c=0;
        
        while($row = $stt->fetch()){
            $data[$c]["div_name"] = $row["div_s"];
            $data[$c]["name"] = $row["name"];
            $data[$c]["correct"] = $row["correct"];
            $data[$c]["sum_time"] = convTime($row["sum_time"]);
            $c++;
        }
                            
        printf(json_encode($data));
                            
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
