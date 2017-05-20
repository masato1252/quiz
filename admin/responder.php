<?php

require_once("../core/connect_db.inc");

try{
        $db = getDB();
    
        $stt = $db->prepare('SELECT * FROM ta_div_table ORDER BY div_num ASC;');
        $stt->execute();
        $c=0;
        while($row = $stt->fetch()){
            $stt2 = $db->prepare('SELECT id FROM ta_user WHERE div_num=:div_num;');
            $stt2->bindValue(":div_num", $row["div_num"]);
            $stt2->execute();
            
            $array[$c]["div_name"] = $row["div_name"];
            $array[$c]["count"] = $stt2->rowCount();
            $c++;
        }
    
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
        <h1>参加者数モニター</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./index.php">＜＜戻る</a><br><br>
    <?php
    
        for($i=0; $i<count($array); $i++){
            printf("チーム：".$array[$i]["div_name"]."  参加者数：".$array[$i]["count"]."<br>");
        }
        
    ?>
        
    </div>
</div>
</body>
</html>   
