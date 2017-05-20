<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="./css/origin.css">
    <script src="./js/jquery-min.js" type="text/javascript"></script>
    <script src="./js/jquery.periodicalupdater.js" type="text/javascript"></script>
</head>
<body>
    <div id="main">
    <div data-role="header">
            <h1>情シスオールスター感謝クイズ</h1>
    </div>
    <div id="container">
<?php

require_once("connect_db.inc");

$quiz_id = $_GET["quiz_id"];
$page = $_GET["page"];
if(!$page || strcmp($page, "")==0){
    $page = 1;
}

$dir = "./img/";

try{
        $db = getDB();
        
        //画像パス取得
        $stt = $db->prepare('SELECT it.file_name FROM quiz_slide AS qs, img_table AS it WHERE qs.quiz_id=:quiz_id AND qs.img_id=it.img_name AND qs.type=:type AND qs.page=:page;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->bindValue(':type', 2);
        $stt->bindValue(':page', $page);
        $stt->execute();
        $row = $stt->fetch();
        
        //MAX頁
        $stt = $db->prepare('SELECT page FROM quiz_slide WHERE quiz_id=:quiz_id AND type=:type ORDER BY page DESC LIMIT 1;');
        $stt->bindValue(':quiz_id', $quiz_id);
        $stt->bindValue(':type', 2);
        $stt->execute();
        $tmp = $stt->fetch();
        $max = $tmp["page"];
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>
<div id="slide">
    <img id="slide_img" width='1100' height='730' src="<?php printf($dir.$row["file_name"]); ?>">
</div>
</div>
</div>
<div id="footer">
    <?php 
        if($page!=1){
    ?>　　
<a href=./slide_after.php?quiz_id=<?php printf($quiz_id); ?>&page=<?php printf($page-1); ?>>前スライド</a> 
    <?php } 
        if($page!=$max){
    ?>   
<a href=./slide_after.php?quiz_id=<?php printf($quiz_id); ?>&page=<?php printf($page+1); ?>>次スライド</a>
     <?php
        }
    ?>　　　
<a href=./slide_before.php?quiz_id=<?php printf($quiz_id+1); ?>>次問へ</a>

</div><!-- footer -->
</body>
</html>