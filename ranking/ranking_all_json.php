<?php

require_once("../core/connect_db.inc");

$start = $_GET["start"];
$end = $_GET["end"];

function convTime($str){
    $arr = explode(".",$str);
    $arr2 =explode(":",$arr[0]);
    return $arr2[1].":".$arr2[2].".".substr($arr[1],0,3);
}

$mode=0;


try{
        $db = getDB();
        
        $limit = $end-$start+1;
        $offset = $start-1;
     
        //正解取得
        $stt = $db->prepare('SELECT * FROM ta_ranking AS qr, ta_user AS qu, ta_div_table AS dt WHERE qu.id=qr.user_id AND dt.div_num=qu.div_num ORDER BY qr.correct DESC, qr.time_valid DESC, qr.sum_time ASC LIMIT :limit OFFSET :offset;');
        //$stt = $db->prepare("SELECT * FROM quiz_ranking ORDER BY correct DESC, sum_time ASC LIMIT :offset, :limit;");
        $stt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stt->execute();
        $c=1;
        $data[0] = $mode;
        while($row = $stt->fetch()){
            $data[$c]["div_name"] = $row["div_s"];
            $data[$c]["name"] = $row["name"];
            $data[$c]["correct"] = $row["correct"];
            $data[$c]["sum_time"] = convTime($row["sum_time"]);
            $data[$c]["rank"] = $start+$c-1;
            $c++;
        }

       
        
        printf(json_encode($data));
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
