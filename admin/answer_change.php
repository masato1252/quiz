<?php

require_once("../connect_db.inc");


    try{
        $db = getDB();
        //$stt = $db->prepare('UPDATE quiz_condition SET state=:state, quiz=:quiz, date=now() WHERE 1;');
        $stt = $db->prepare('select * from quiz_data order by quiz_id asc;');
        $stt->execute();
        

    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/js/jquery.mobile-1.4.5.min.css">
    <link rel="stylesheet" href="/js/theme1.css">
    <link rel="stylesheet" href="/js/theme1.min.css">
    <script src="/js/jquery-min.js" type="text/javascript"></script>
    <script src="/js/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
    <script src="/js/jquery.periodicalupdater.js" type="text/javascript"></script>
    
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
       <h1>問題編集</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./index.php">＜＜戻る</a><br><br>
        <a href="./regist_quiz_form.php">新規登録</a><br><br>
<?php
    while($row = $stt->fetch()){
        printf("<a href='./edit_quiz_form.php?quiz_id=".$row["quiz_id"]."'>第".$row["quiz_id"]."問 ".mb_substr($row["question"], 0, 15)."...</a><br><br>");    
        
    }
?>

    <a href="./index.php">＜＜戻る</a>
    </div>
</div>
</body>
</html>   