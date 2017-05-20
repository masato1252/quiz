<?php
    
    require_once("../core/connect_db.inc");


    $type = $_POST["type"];


    if($type==-1){
        //タップ情報削除
        try{
            $db = getDB();
            
            $stt = $db->prepare('DELETE FROM ta_tap;');
            $stt->execute();
            
        }catch(PDOException $e){
            die("接続エラー:".$e);
        }
        
    }else if($type==-2){
        //コンディション削除
        try{
            $db = getDB();
            
            $stt = $db->prepare('DELETE FROM ta_condition;');
            $stt->execute();
            
        }catch(PDOException $e){
            die("接続エラー:".$e);
        }
        
    }else if($type==-3){
        //コンディション削除
        try{
            $db = getDB();
            
            $stt = $db->prepare('DELETE FROM ta_user;');
            $stt->execute();
            
        }catch(PDOException $e){
            die("接続エラー:".$e);
        }
        
    }else if($type==-4){
        //コンディション削除
        try{
            $db = getDB();
            
            $stt = $db->prepare('DELETE FROM ta_ranking;');
            $stt->execute();
            
        }catch(PDOException $e){
            die("接続エラー:".$e);
        }
        
    }

    $array = array("result" => 1);
    printf(json_encode($array));


?>
