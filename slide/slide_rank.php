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

$rank = $_GET["rank"];
if(!$rank || strcmp($rank, "")==0){
    exit();
}

$dir = "./img/";

if($rank==1){
    $next_link = "./ranking_end.php";
}else if($rank==4){
    $next_link = "./ranking_all.php?start=100&end=100&num=0";
}else if($rank==100){
    $next_link = "./ranking_all.php?start=1&end=10&num=3";
}else{
    $next_link = "./ranking_all.php?start=1&end=10&num=".($rank-1);
}

try{
        $db = getDB();
        
        //画像パス取得
        $stt = $db->prepare('SELECT it.file_name FROM quiz_slide_rank AS qs, img_table AS it WHERE qs.rank_num=:rank AND qs.img_id=it.img_name;');
        $stt->bindValue(':rank', $rank);
        $stt->execute();
        $row = $stt->fetch();
        
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

<a href='<?php printf($next_link); ?>'>次へ</a>　

</div><!-- footer -->
</body>
</html>