<?php

require_once("../core/connect_db.inc");

$quiz_id = $_GET["quiz_id"];

    try{
        $db = getDB();
        $stt = $db->prepare('select * from ta_quiz_data where quiz_id=:quiz_id LIMIT 1;');
        $stt->bindValue(":quiz_id", $quiz_id);
        $stt->execute();
        $row = $stt->fetch();
        
        if($row["img_type"]==1){
            for($i=1; $i<=4; $i++){
                $stt = $db->prepare('select * from ta_quiz_img where quiz_id=:quiz_id and num=:num;');
                $stt->bindValue(":quiz_id", $quiz_id);
                $stt->bindValue(":num", $i);
                $stt->execute();
                $tmp = $stt->fetch();
                $sel[$i] = $tmp["img_name"];
                $exp[$i] = $tmp["exp"];
            }
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
    
    function push_button() {
        // HTMLでの送信をキャンセル
        //event.preventDefault();
        
        // 操作対象のフォーム要素を取得
        var $form = $("form#quiz_img");
        
        // 送信ボタンを取得
        // （後で使う: 二重送信を防止する。）
        var $button = $form.find('button');
        
        // 送信
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serialize()
                + '&delay=1',  // （デモ用に入力値をちょいと操作します）
            timeout: 10000,  // 単位はミリ秒
            
            // 送信前
            beforeSend: function(xhr, settings) {
                // ボタンを無効化し、二重送信を防止
                $button.attr('disabled', true);
            },
            // 応答後
            complete: function(xhr, textStatus) {
                // ボタンを有効化し、再送信を許可
                $button.attr('disabled', false);
            },
            
            // 通信成功時の処理
            success: function(result, textStatus, xhr) {
                // 入力値を初期化
                //$form[0].reset();
                
                alert("更新完了！"+result);
                $button.attr('disabled', false);
            },
            
            // 通信失敗時の処理
            error: function(xhr, textStatus, error) {
                
                alert("更新エラー");
                $button.attr('disabled', false);
            }
        });
    }
    </script>
</head>
<body>
<div data-role="page" id="index" data-theme="a">
    <div data-role="header">
       <h1>画像セット <?php printf($row["quiz_title"]); ?></h1>
    </div>
    <div data-role="main" class="ui-content">
        <a href="./quiz_img.php">＜＜戻る</a><br><br>
        
        <form action="./set_img.php" method="post" id="quiz_img">

        <input type="hidden" name="quiz_id" value="<?php printf($row["quiz_id"]); ?>">

        選択肢①の画像ID：<input type="text" name="select1" size="30" value="<?php printf($sel["1"]); ?>"><br>
        赤枠表示名(任意)：<input type="text" name="explain1" size="30" value="<?php printf($exp["1"]); ?>"><br><br>
        選択肢②の画像ID：<input type="text" name="select2" size="30" value="<?php printf($sel["2"]); ?>"><br>
        赤枠表示名(任意)：<input type="text" name="explain2" size="30" value="<?php printf($exp["2"]); ?>"><br><br>
        選択肢③の画像ID：<input type="text" name="select3" size="30" value="<?php printf($sel["3"]); ?>"><br>
        赤枠表示名(任意)：<input type="text" name="explain3" size="30" value="<?php printf($exp["3"]); ?>"><br><br>
        選択肢④の画像ID：<input type="text" name="select4" size="30" value="<?php printf($sel["4"]); ?>"><br>
        赤枠表示名(任意)：<input type="text" name="explain4" size="30" value="<?php printf($exp["4"]); ?>"><br>

         <input type="submit" value="送信">
         </form>
    </div>
</div>
</body>
</html>   
