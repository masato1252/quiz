<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
<?php
    require_once("./core/setting.php");
    require_once("./core/connect_db.inc");
    require_once './mobile_header.php';
?>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
        <h1><?php printf($TITLE); ?></h1>
    </div>
    <div data-role="main" class="ui-content">
<?php

$name = $_POST["name"];
$div_num = $_POST["div_num"];

if($div_num==-1 && !$name || strcmp($name, "")==0){
        printf("・所属チームを選択してください。<br>");
        printf("・名前を入力してください。<br><br><a href=javascript:history.back()>戻る</a>");
        die();
}else if($div_num==-1){
        printf("所属チームを選択してください。<br><br><a href=javascript:history.back()>戻る</a>");
        die();
}else if(!$name || strcmp($name, "")==0){
        printf("名前を入力してください。<br><br><a href=javascript:history.back()>戻る</a>");
        die();
}

//DB登録処理
try{
    $db = getDB();
    
    //重複チェック
    $stt = $db->prepare('SELECT * FROM ta_user AS qu, ta_div_table AS dt WHERE qu.name=:name AND qu.div_num=:div_num AND dt.div_num=qu.div_num
                        ORDER BY qu.regist_date DESC LIMIT 1;');
    $stt->bindValue(':name', $name);
    $stt->bindValue(':div_num', $div_num);
    $stt->execute();
    $count = $stt->rowCount();
    
    if($count>0){
        //存在
        $row = $stt->fetch();
        
        printf("途中データあり<br><br>
        ".$row["div_name"]." ".$row["name"]."さん ですね？<br><br><br>
        <a href=./quiz_exe.php?user_id=".$row["id"]." class='ui-btn' data-ajax='false'>再ログインする</a>");

    }else{
        //新規登録
        //user_id生成
        $id = rand(1111111,9999999).rand(11111111,99999999);
            
        //ユーザー基本情報DB
        $stt = $db->prepare('INSERT INTO ta_user (id, name, div_num) VALUES (:id, :name, :div_num);');
        $stt->bindValue(':id', $id);
        $stt->bindValue(':name', $name);
        $stt->bindValue(':div_num', $div_num);
        $stt->execute();
        
        printf("参加登録が完了しました。<br><br><br>
        <a href=./quiz_exe.php?user_id=".$id." class='ui-btn' data-ajax='false'>スタンバイする</a>");
        
    }
    
    

    

}catch(PDOException $e){

    die("接続エラー:".$e);
}


?>
    </div><!-- main -->
</div><!-- index -->
</body>
</html>   
