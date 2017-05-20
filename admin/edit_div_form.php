<?php
    
    require_once("../core/connect_db.inc");
    
    $div_num = $_GET["div_num"];
    
    try{
        $db = getDB();
        $stt = $db->prepare('select * from ta_div_table where div_num=:div_num LIMIT 1;');
        $stt->bindValue(":div_num", $div_num);
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
<h1>チーム名編集</h1>
</div>
<div data-role="main" class="ui-content">
<a href="./edit_div.php">＜＜戻る</a><br><br>

<form action="./update_div.php" method="post" id="edit_div">

<input type="hidden" name="div_num" value="<?php printf($row["div_num"]); ?>">
順番：<input type="text" name="num" size="3" value="<?php printf($row["div_num"]+1); ?>"><br>
チーム名：<input type="text" name="div_name" size="30" value="<?php printf($row["div_name"]); ?>"><br>
ランキング表示名：<input type="text" name="div_s" size="30" value="<?php printf($row["div_s"]); ?>">
<br><br>
<input type="submit" value="送信" >
</div>
</div>
</body>
</html>
