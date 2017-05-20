<?php

require_once("connect_db.inc");

try{
    $db = getDB();

    $stt = $db->prepare('SELECT TIME_TO_SEC(now(6));');
    $stt->execute();
    
    $row = $stt->fetch();
    
    printf(ceil($row[0]));
    
 }catch(PDOException $e){
    die("接続エラー:".$e);
}

function hms($sec) {
    $ss = $sec % 60;
    $mm = (int)($sec / 60) % 60;
    $hh = (int)($sec / (60 * 60));
    return array($hh, $mm, $ss);
}

printf("<br>");
$a = "00:00:17.882307";
$b = "00:00:15.025000";

$arr = explode(".", $a);

$micro = intval($arr[1])/1000000;
$arr2 = explode(":",$arr[0]);
var_dump($arr2);
$sec = intval($arr2[0])*3600 + intval($arr2[1])*60 + $arr2[2];

$time = $sec + $micro;

printf(intval($sec)." ".$time);
?>