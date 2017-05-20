<?php
require_once("../core/connect_db.inc");

$USER_NUM = $_GET["user_num"];
//$QUIZ_NUM = $_GET["quiz_num"];

//DB登録処理
try{
    $db = getDB();
    $stt = $db->prepare('SELECT * FROM ta_div_table;');
    $stt->execute();
    $div_count = $stt->rowCount();
    

    for($i=0; $i<$USER_NUM; $i++){
        //num_id生成
        $id = rand(1111111,9999999).rand(11111111,99999999);
        $id_array[$i] = $id;
        
        $div_num = rand(0, $div_count-1);
    
        //ユーザー基本情報DB
        $stt = $db->prepare('INSERT INTO ta_user (id,name,div_num) VALUES (:id,:name,:div_num)');
        $stt->bindValue(':id', $id);
        $stt->bindValue(':name', "テスト".($i+1));
        $stt->bindValue(':div_num', $div_num);
        $stt->execute();
        
    }
    
    $stt = $db->prepare('SELECT quiz_id,answer,quiz_type FROM ta_quiz_data ORDER BY quiz_id ASC;');
    $stt->execute();
    $QUIZ_NUM = $stt->rowCount();
    while($row = $stt->fetch()){
        $answer[$row["quiz_id"]] = $row["answer"];
    }
    
    //コンディション＆タップ生成
    for($i=1; $i<=$QUIZ_NUM; $i++){
        
        $stt = $db->prepare('INSERT INTO ta_condition(state, quiz, date6, date) VALUES (:state, :quiz, now(6), now());');
        $stt->bindValue(':state', 1);
        $stt->bindValue(':quiz', $i);
        $stt->execute();
        
        for($j=0; $j<$USER_NUM; $j++){
            
            $delay_s = rand(0,9);
            $delay_m = rand(0,9);
            
            //上位５名全問正解
            if($j<=4){
                $stt = $db->prepare("INSERT INTO ta_tap(user_id, quiz_id, answer, date) VALUES (:user_id, :quiz_id, :answer, ADDTIME(now(6),'0:0:".$delay_s.".00000".$delay_m."'));");
                $stt->bindValue(':user_id', $id_array[$j]);
                $stt->bindValue(':quiz_id', $i);
                $stt->bindValue(':answer', $answer[$i]);
                $stt->execute();
                
            }else{
                
                $rand_ans = $delay_s = rand(1,4);   
                    
                $stt = $db->prepare("INSERT INTO ta_tap(user_id, quiz_id, answer, date) VALUES (:user_id, :quiz_id, :answer, ADDTIME(now(6),'0:0:".$delay_s.".00000".$delay_m."'));");
                $stt->bindValue(':user_id', $id_array[$j]);
                $stt->bindValue(':quiz_id', $i);
                $stt->bindValue(':answer', $rand_ans);
                $stt->execute();
                
            }
        }
        
    }

}catch(PDOException $e){
    die("接続エラー:".$e);
}

printf("テストデータ生成完了");
?>
