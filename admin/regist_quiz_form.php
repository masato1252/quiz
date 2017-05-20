<?php

require_once("../core/connect_db.inc");

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
       <h1>問題作成</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./edit_quiz.php">＜＜戻る</a><br><br>
        
        <form action="./regist_quiz.php" method="post" id="regist_quiz">

        順番：<input type="text" name="num" size="3" value=""><br>
        見出し：（「問題○」、「例題○」など）：
        <input type="text" name="quiz_title" size="30" value=""><br>

        問題タイプ：
        <select name="quiz_type">
            <option value="0">例題</option>
            <option value="1">早押し</option>
            <option value="2">ミニゲーム</option>
        </select><br>

        選択肢タイプ：
        <select name="img_type">
        <option value="0" <?php if($row["img_type"]==0) printf("selected");  ?>>文字</option>
        <option value="1" <?php if($row["img_type"]==1) printf("selected");  ?>>画像</option>
        </select><br>

        問題文：<textarea name="question" rows="4" cols="20"></textarea><br>
        選択肢①文：<input type="text" name="select1" size="30" value=""><br>
        選択肢②文：<input type="text" name="select2" size="30" value=""><br>
        選択肢③文：<input type="text" name="select3" size="30" value=""><br>
        選択肢④文：<input type="text" name="select4" size="30" value=""><br>
        正解番号：(その場で決める場合は0を入力)
        <input type="text" name="answer" size="3" value=""><br>
        制限時間(sec)：<input type="text" name="limit_time" size="3" value=""><br>
        景品の有無：
        <select name="goods">
            <option value="0">なし</option>
            <option value="1">あり</option>
        </select><br>
        <input type="submit" value="送信">
    </div>
</div>
</body>
</html>   
