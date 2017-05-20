<?php

require_once("../core/connect_db.inc");

try{
        $db = getDB();
        $stt = $db->prepare('SELECT * FROM ta_tap;');
        $stt->execute();
        $tapCount = $stt->rowCount();
        
        $stt = $db->prepare('SELECT * FROM ta_condition;');
        $stt->execute();
        $condiCount = $stt->rowCount();
        
        $stt = $db->prepare('SELECT * FROM ta_user;');
        $stt->execute();
        $userCount = $stt->rowCount();
        
        $stt = $db->prepare('SELECT * FROM ta_ranking;');
        $stt->execute();
        $rankCount = $stt->rowCount();
        
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
function debug_func(num){
    
    
    $.ajax({
    url: './debug_exe.php',
    type: 'POST',
    data: {
        user_id: 0,
        quiz_id: 0,
        type: num
    },
    dataType: 'json'
    })
    .done(function( data ) {
            // ...
          location.reload();
    })
    .fail(function( data ) {
            // ...

    })
    .always(function( data ) {
            // ...
    });
    
}
    </script>

</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
        <h1>デバッグツール</h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./index.php">＜＜戻る</a><br><br>
        開発環境でのDB状態整理用　取り扱い注意<br><br>
        
        <a href="../process/make_testdata.php?user_num=200">テストユーザデータ生成</a><br><br>
        <a href="../process/make_ranking.php">ランキングデータ生成</a><br><br>
        
        投票データ件数:  <?php printf($tapCount); ?><br>
        <input type='button' value='投票RESET(debug)' onclick='debug_func(-1);'><br><br>
        
        ステートデータ件数:  <?php printf($condiCount); ?><br>
        <input type='button' value='状態TBL削除(debug)' onclick='debug_func(-2);'><br><br>
        
        ユーザデータ件数:  <?php printf($userCount); ?><br>
        <input type='button' value='ユーザTBL削除(debug)' onclick='debug_func(-3);'><br><br>
        
        総合ランキングデータ件数:  <?php printf($rankCount); ?><br> 
        <input type='button' value='総合RANK削除(debug)' onclick='debug_func(-4);'>

        
    </div>
</div>
</body>
</html>   
