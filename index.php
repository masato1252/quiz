<?php

require_once("./core/setting.php");
require_once("./core/connect_db.inc");


    try{
        $db = getDB();
        $stt = $db->prepare('select * from ta_div_table order by div_num ASC;');
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
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
<?php
    require_once './mobile_header.php';
?>
</head>

</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
        <h1><?php printf($TITLE); ?></h1>
    </div>
    <div data-role="main" class="ui-content">
        <form action="./regist_user.php" method="post">
            <p>参加登録</p>
            <br>
            所属チーム：
            <select name="div_num">
                <option value="-1">---- チーム名 ----</option>
                <?php
                    for($i=0; $i<count($divs); $i++){
                        printf("<option value='".$i."'>".$divs[$i]."</option>");
                    }
                ?>
            </select><br>
            お名前（フルネーム）：
            <input type="text" name="name" size="30" /><br><br>
            
            <input type="submit" value="登録" >
        </form>
    </div>
</div>
</body>
</html>   
