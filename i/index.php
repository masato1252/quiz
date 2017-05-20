<?php

require_once("../connect_db.inc");


    try{
        $db = getDB();
        //$stt = $db->prepare('UPDATE quiz_condition SET state=:state, quiz=:quiz, date=now() WHERE 1;');
        $stt = $db->prepare('select * from quiz_div_table order by div_num ASC;');
        $stt->execute();
        $c=0;
        while($row = $stt->fetch()){
            $divs[$c] = $row["div_name"];
            $c++;
        }

    }catch(PDOException $e){
        die("接続エラー:".$e);
    }

?>

<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>情シスオールスター感謝クイズ</title>
</head>
<body>
<div id="main">
    <font color="blue"><center><h3>情シスオールスター感謝クイズ</h3></center></font>
    <hr>
    <p>参加登録</p>
    
    <form action="./regist_user_i.php" method="post">
    所属担当<br>
    <select name="div_num">
        <option value="-1">-- 担当名を選択して下さい --</option>
            <?php
                for($i=0; $i<count($divs); $i++){
                    printf("<option value='".$i."'>".$divs[$i]."</option>");
                }
            ?>
    </select><br><br>
    お名前（フルネーム）<br>
        <input type="text" name="name" size="20" /><br><br>
        
        <input type="submit" value="登録" >
    </form>

</div>
</body>
</html>