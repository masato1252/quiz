<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>情シスオールスター感謝クイズ</title>
</head>
<body>
<div id="main">
    <font color="blue"><center><h3>回答受付</h3></center></font>
    <hr>
<?php

require_once("../connect_db.inc");

$user_id = $_POST["user_id"];
$answer = $_POST["answer"];

try{
        $db = getDB();

        $stt = $db->prepare('SELECT state,quiz FROM quiz_condition ORDER BY date DESC LIMIT 1;');
        $stt->execute();
        $row = $stt->fetch();
        
        if($row["state"]!=1){
            //回答受け付け未開始
            printf("まだ回答を受付開始していません。<br><br>
                    <a href='./quiz_i.php?user_id=".$user_id."'>戻る</a>");
                    
        }else{
            
            $stt2 = $db->prepare('SELECT answer FROM quiz_tap WHERE user_id=:user_id AND quiz_id=:quiz_id;');
            $stt2->bindValue(':user_id', $user_id);
            $stt2->bindValue(':quiz_id', $row["quiz"]);
            $stt2->execute();
            $tapCount = $stt2->rowCount();
            
            if($tapCount > 0){
                //回答済み
                printf("現在出題中の問題に<br>回答済みです。<br><br>
                        <a href='./quiz_i.php?user_id=".$user_id."'>戻る</a>");
            }else{
                //新規回答
                $stt = $db->prepare('INSERT INTO quiz_tap(user_id, quiz_id, answer, date) VALUES (:user_id, :quiz_id, :answer, now(6));');
                $stt->bindValue(':user_id', $user_id);
                $stt->bindValue(':quiz_id', $row["quiz"]);
                $stt->bindValue(':answer', $answer);
                $stt->execute();
                
                $stt2 = $db->prepare('SELECT quiz_title FROM quiz_data WHERE quiz_id=:quiz_id;');
                $stt2->bindValue(':quiz_id', $row["quiz"]);
                $stt2->execute();
                $row2 = $stt2->fetch();
                
                printf($row2["quiz_title"]."の回答：<br>".$answer."番<br>で受付しました。<br><br>
                       <a href='./quiz_i.php?user_id=".$user_id."'>次の問題までスタンバイする</a>");

            }
            

        }
        
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>