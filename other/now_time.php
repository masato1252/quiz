<?php

require_once("connect_db.inc");

try{
    $db = getDB();

    $stt = $db->prepare('SELECT UNIX_TIMESTAMP(now(6));');
    $stt->execute();
    
    $row = $stt->fetch();
    
    $array = array("date" => ceil($row[0]));
    printf(json_encode($array));
    
 }catch(PDOException $e){
    die("接続エラー:".$e);
}

?>