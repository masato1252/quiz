<?php

require_once("../core/connect_db.inc");

$quiz_id = $_GET["quiz_id"];

    try{
        $db = getDB();
        $stt = $db->prepare('select * from ta_quiz_data where quiz_id=:quiz_id LIMIT 1;');
        $stt->bindValue(":quiz_id", $quiz_id);
        $stt->execute();
        $row = $stt->fetch();

    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <?php
        require_once './admin_header.php';
    ?>
    <script type="text/javascript">

    </script>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
       <h1>問題編集 <?php printf($row["quiz_title"]); ?></h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./edit_quiz.php">＜＜戻る</a><br><br>
        
        <form action="./update_quiz.php" method="post" id="edit_quiz">

        <input type="hidden" name="quiz_id" value="<?php printf($row["quiz_id"]); ?>">
        順番：<input type="text" name="num" size="3" value="<?php printf($row["quiz_id"]); ?>"><br>
        見出し：（「問題○」、「例題○」など）：<input type="text" name="quiz_title" size="30" value="<?php printf($row["quiz_title"]); ?>"><br>
        問題タイプ：
        <select name="quiz_type">
            <option value="0" <?php if($row["quiz_type"]==0) printf("selected");  ?>>例題</option>
            <option value="1" <?php if($row["quiz_type"]==1) printf("selected");  ?>>早押し</option>
            <option value="2" <?php if($row["quiz_type"]==2) printf("selected");  ?>>ミニゲーム</option>
        </select><br>

        選択肢タイプ：
        <select name="img_type">
            <option value="0" <?php if($row["img_type"]==0) printf("selected");  ?>>文字</option>
            <option value="1" <?php if($row["img_type"]==1) printf("selected");  ?>>画像</option>
        </select><br>

        問題文：<textarea name="question" rows="4" cols="20"><?php printf($row["question"]); ?></textarea><br>
        選択肢①文：<input type="text" name="select1" size="30" value="<?php printf($row["select1"]); ?>"><br>
        選択肢②文：<input type="text" name="select2" size="30" value="<?php printf($row["select2"]); ?>"><br>
        選択肢③文：<input type="text" name="select3" size="30" value="<?php printf($row["select3"]); ?>"><br>
        選択肢④文：<input type="text" name="select4" size="30" value="<?php printf($row["select4"]); ?>"><br>
        正解番号：(その場で決める場合は0を入力)
        <input type="text" name="answer" size="3" value="<?php printf($row["answer"]); ?>"><br>
        制限時間：(sec)：<input type="text" name="limit_time" size="3" value="<?php printf($row["limit_time"]); ?>"><br>
        景品有無：
        <select name="goods">
            <option value="0" <?php if($row["goods"]==0) printf("selected");  ?>>なし</option>
            <option value="1" <?php if($row["goods"]==1) printf("selected");  ?>>あり</option>
        </select><br><br>
        <input type="submit" value="送信" >
    </div>
</div>
</body>
</html>   
