<html>
<body>
<div data-role="page" id="index" data-theme="a">
<div data-role="main" class="ui-content">
<?php
    require_once("../core/connect_db.inc");
    
    $num = $_POST["num"] - 1;
    $div_name = $_POST["div_name"];
    $div_s = $_POST["div_s"];
    
    try{
        
        $db = getDB();
        
        $stt = $db->prepare('select * from ta_div_table where div_num=:div_num;');
        $stt->bindValue(':div_num', $num);
        $stt->execute();
        $count= $stt->rowCount();
        
        if($count>0){
            printf("指定した順番が他と重複しています。<br><br><a href='javascript:history.back();'>戻る</a>");
            exit();
        }
        
        
        $stt = $db->prepare('INSERT INTO ta_div_table(div_num, div_name, div_s)
                            VALUES (:num, :div_name, :div_s);');
        $stt->bindValue(':num', $num);
        $stt->bindValue(':div_name', $div_name);
        $stt->bindValue(':div_s', $div_s);
        $stt->execute();
        
        printf("登録完了！<br><br><a href='./edit_div.php'>＜＜戻る</a>");
        
    }catch(PDOException $e){
        die("接続エラー:".$e);
    }
    
    
    ?>
</div>
</div>
</body>
</html>
