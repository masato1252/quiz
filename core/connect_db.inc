<?php
function getDB(){

<<<<<<< HEAD
    $dsn = 'mysql:dbname=LAA0636957-bigfaceworks; host=mysql101.phy.lolipop.lan';
    $usr = 'LAA0636957';
    $pass = 'MATSUURA4';
=======
    $dsn = 'mysql:dbname=<SECRET>; host=<SECRET>';
    $usr = '<SECRET>';
    $pass = '<SECRET>';
>>>>>>> 6782f34... Modifed include file


    try{
        $db = new PDO($dsn,$usr,$pass);
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $db->exec('SET NAMES utf8');
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

    return $db;

}
