<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/XHTML1/DTD/xhtml1-transitional.dtd">
<html xmls="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<?php
    require_once './admin_header.php';
?>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
<div data-role="header">
<h1>画像管理</h1>
</div>
<div data-role="main" class="ui-content">
<?php

//require_once("../../core/admin_auth.inc");
require_once("../core/connect_db.inc");

$dir = "../img/";

if(empty($_GET['img_id']) || empty($_GET['mode'])){
    print("不正な操作が行われました<br><br><a href=./show_img.php>戻る</a>");
    exit();
}else{
    $img_id = $_GET['img_id'];
    $mode = $_GET['mode'];
}

if($mode==-1){
    //削除処理

    //DB登録処理
    try{
        $db = getDB();

        //ファイル名取得
        $stt = $db->prepare('SELECT file_name FROM ta_img_table WHERE img_id=:img_id;');
        $stt->bindValue(':img_id', $img_id);
        $stt->execute();
        $row = $stt->fetch();

        unlink($dir.$row['file_name']);

        //ユーザー基本情報DB
        $stt = $db->prepare('DELETE FROM ta_img_table WHERE img_id=:img_id');
        $stt->bindValue(':img_id', $img_id);
        $stt->execute();



    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

    print("削除完了しました<br><br><a href=./show_img.php>戻る</a>");

}
?>
</div>
</div>
</body>
</html>
