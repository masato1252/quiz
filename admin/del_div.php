<html>
<body>
<div data-role="page" id="index" data-theme="a">
<div data-role="main" class="ui-content">
<?php
    require_once("../core/connect_db.inc");
    
    $div_num = $_GET["div_num"];
    
    try{
        
        $db = getDB();
        
        $stt = $db->prepare('DELETE FROM ta_div_table WHERE div_num=:div_num;');
        $stt->bindValue(':div_num', $div_num);
        $stt->execute();
        
        $array = array("result" => 1);
        //printf(json_encode($array));
        
        printf("削除完了！<br><br><a href='./edit_div.php'>＜＜戻る</a>");
        
    }catch(PDOException $e){
        $array = array("result" => 0);
        //printf(json_encode($array));
        
        die("接続エラー:".$e);
    }
    
    
    ?>
</div>
</div>
</body>
</html>
