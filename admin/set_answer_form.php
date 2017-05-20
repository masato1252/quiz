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
       <h1>答えの設定 <?php printf($row["quiz_title"]); ?></h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./set_answer.php">＜＜戻る</a><br><br>
        
        <?php printf($row["question"]); ?><br><br>
        
        <form action="./update_answer.php" method="post" id="update_answer">
        <input type="hidden" name="quiz_id" value="<?php printf($quiz_id); ?>">
        答え：
        <select name="answer">
            <option value="1" <?php if($row["answer"]==1) printf("selected"); ?>>①</option>
            <option value="2" <?php if($row["answer"]==2) printf("selected"); ?>>②</option>
            <option value="3" <?php if($row["answer"]==3) printf("selected"); ?>>③</option>
            <option value="4" <?php if($row["answer"]==4) printf("selected"); ?>>④</option>
        </select>
        <br><br>
        <input type="submit" value="送信" >
    </div>
</div>
</body>
</html>   
