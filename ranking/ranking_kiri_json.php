<?php
    
    require_once("../core/connect_db.inc");

    
    function convTime($str){
        $arr = explode(".",$str);
        $arr2 =explode(":",$arr[0]);
        return $arr2[1].":".$arr2[2].".".substr($arr[1],0,3);
    }
    
    $mode=0;
    
    try{
        $db = getDB();
        
        $stt = $db->prepare('SELECT rank FROM ta_rank_kiri ORDER BY rank ASC;');
        $stt->execute();
        
        $c=1;
        $data[0] = $mode;
        while($row = $stt->fetch()){
            
            $limit = 1;
            $offset = $row['rank']-1;
            
            $stt2 = $db->prepare('SELECT * FROM ta_ranking AS qr, ta_user AS qu, ta_div_table AS dt WHERE qu.id=qr.user_id AND dt.div_num=qu.div_num ORDER BY qr.correct DESC, qr.time_valid DESC, qr.sum_time ASC LIMIT :limit OFFSET :offset;');
            $stt2->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stt2->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stt2->execute();
            $row2 = $stt2->fetch();
            
            $data[$c]["div_name"] = $row2["div_s"];
            $data[$c]["name"] = $row2["name"];
            $data[$c]["correct"] = $row2["correct"];
            $data[$c]["sum_time"] = convTime($row2["sum_time"]);
            $data[$c]["rank"] = $row['rank'];
            $c++;
        }
        
        printf(json_encode($data));
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }
    
    ?>
